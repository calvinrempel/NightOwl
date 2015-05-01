<?php

namespace NightOwl\Model;

/**
 * LaunchCodesModel provides a concrete mechanism for retrieving and persisting
 * Launch Codes. It connects to a Consul Key/Value store to persist the basic
 * data pertaining to the code. It also uses MongoDB to store meta-data about
 * each code that is important for administration but not required in production.
 *
 * Author: Calvin Rempel
 * Date: April 30, 2015
 */
class LaunchCodesModel
{
    /* Configuration settings for Consul (May be moved to config file) */
    const CONSUL_HOST = 'localhost';
    const CONSUL_PORT = 8500;

    /* HTTP Statuses that indicate Consul response types. */
    const CONSUL_SUCCESS_CODE = 200;

    /**
     * Get the list of Launch Codes.
     *
     * This function will retrieve the list of launch codes from Consul that
     * either (a) are wholly the prefix (if recurse is false) or (b) contain the
     * the prefix (if recurse is true). It does not retrieve code meta-data from
     * the MongoDB.
     *
     * Params:
     *      $dataCentre : the Consul data centre to retrieve launch codes from.
     *      $prefix : the prefix (or key) to search for in the data store.
     *      $recurse : if True, all keys beginning with $prefix will be returned
     *                 if False, only that key matching the $prefix is returned.
     *
     * Returns:
     *      The list of launch codes as returned by Consul (may be empty) if
     *      successful. If an error occurred, FALSE is returned instead.
     *
     * Author: Calvin Rempel
     * Date: April 30, 2015
     */
    public function getLaunchCodes($dataCentre, $prefix, $recurse)
    {
        $httpCode = 0;
        $result = '';
        $url = $this->getConsulKVUrl() . $prefix;

        // Build the data centre into the URL if set
        if ($dataCentre !== '')
        {
            $url .= '?dc=' . $dataCentre;

            // Append 'recurse' property if requested
            if ($recurse)
                $url .= '&recurse';
        }
        // Append only 'recurse' if required.
        else if ($recurse)
        {
            $url .= '?recurse';
        }

        // Request data through curl
        $result = $this->doCurlRequest($url, $httpCode, 'GET');

        // Successfully retrieved data from Consul
        if ($httpCode == self::CONSUL_SUCCESS_CODE)
            return $result;

        // An error occurred or the set is empty
        if (!$result)
            return FALSE;
    }

    /**
     * Gets the metadata associated with each code in the list and adds it to
     * the code in the array.
     *
     *      !!!   --- CURRENTLY USES DUMMY DATA ---   !!! 
     *
     * Params:
     *      $codes : the array of codes (which contains at least the key "Key").
     *               When the function completes, each code in the array will
     *               have it's available metadata added to it.
     *
     * Author: Calvin Rempel
     * Date:
     */
    public function injectMetadata(& $codes)
    {
        $count = count($codes);

        for ($i = 0; $i < $count; $i++)
        {
            $codes[$i]['isAvailableToJS'] = true;
            $codes[$i]['description'] = 'Sample description until DB is up!';
            $codes[$i]['dateCreated'] = 'Always Yesterday';
        }
    }

    /**
     * Get the URL to access the Consul RESTful API on.
     *
     * Returns: the Consul REST API url in the form "host:port/v1/kv"
     *
     * Author: Calvin Rempel
     * Date: April 30, 2015
     */
    private function getConsulKVUrl()
    {
        return self::CONSUL_HOST . ':' . self::CONSUL_PORT . '/v1/kv/';
    }

    /**
     * Perform a generic curl request to the given URL, with the given body.
     * The result of the call is returned from the function and the HTTP status
     * returned is supplied through the $status parameter.
     *
     * Params:
     *      $url    : The url to make the request to.
     *      $status : A reference through which the HTTP code is returned.
     *      $verb   : the HTTP verb to use in the request (e.g. "GET").
     *      $body   : the payload of the request (if required).
     *
     * Returns: the results of the curl request.
     *
     * Author: Calvin Rempel
     * Date: April 30, 2015
     */
    private function doCurlRequest($url, &$status, $verb, $body='')
    {
        // Create/initialize curl handle
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        // Make curl request.
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close curl handle
        curl_close($ch);

        return $result;
    }
}
