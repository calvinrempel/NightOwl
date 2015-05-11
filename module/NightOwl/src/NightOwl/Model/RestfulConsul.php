<?php

namespace NightOwl\Model;

use NightOwl\Model\IConsul;

/**
 * RestfulConsul is an IConsul that provides access to Consul data through a
 * it's RESTful api.
 *
 * Author: Calvin Rempel
 * Date: May 9, 2015
 */
class RestfulConsul implements IConsul
{
    /* HTTP Statuses that indicate Consul response types. */
    const CONSUL_SUCCESS_CODE = 200;

    /**
    * Create a new RestfulConsul that connects to Consul at the given host on
    * the given port.
    *
    * Params:
    *       $host : the host on which to connect to Consul
    *       $port : the port on which Consul is listening for REST requests.
    */
    public function __construct($host, $port)
    {
        $this->baseUrl = $host. ':' . $port . '/v1/kv/';
    }

    /**
    * Get the list of Key/Value pairs stored in Consul
    *
    * This function will retrieve the list of key/value pairs from Consul that
    * either (a) are wholly the prefix (if recurse is false) or (b) contain the
    * the prefix (if recurse is true).
    *
    * Params:
    *      $prefix : the prefix (or key) to search for in the data store.
    *      $recurse : if True, all keys beginning with $prefix will be returned
    *                 if False, only that key matching the $prefix is returned.
    *      $dataCentre : the Consul data centre to retrieve values from.
    *
    * Returns:
    *      The list of launch codes as returned by Consul (may be empty) if
    *      successful. If an error occurred, FALSE is returned instead.
    *
    * Author: Calvin Rempel
    * Date: April 30, 2015
    */
    public function get($prefix, $recurse, $dataCentre)
    {
        $endpoint = $this->baseUrl . $prefix . '?';

        // Build the URL according to supplied parameters
        if ($dataCentre !== '')
            $endpoint .= 'dc=' . $dataCentre . '&';
        if ($recurse)
            $endpoint .= 'recurse';

        // Make the REST request.
        $httpCode = 0;
        $result = $this->doCurlRequest($endpoint, $httpCode, 'GET');

        // Successfully retrieved data from Consul
        if ($httpCode == self::CONSUL_SUCCESS_CODE)
        {
            return json_decode(($result), true);
        }

        // An error occurred or the set is empty
        return FALSE;
    }

    /**
    * Set the value of the key in the Consul data store. If the key does not
    * currently exist, it will be created.
    *
    * Params:
    *		$key 		   : the key of the key value pair
    *		$value		   : the value associated with the key
    *       $dataCentre    : the datacentre to set in ('' for default).
    *
    * Returns: True if successfully set, False on failure.
    *
    * Author: Calvin Rempel
    * Date: May 9, 2015
    */
    public function set($key, $value, $dataCentre = '')
    {
        $endpoint = $key . '?';

        // Build the URL according to supplied parameters
        if ($dataCentre !== '')
            $endpoint .= 'dc=' . $dataCentre . '&';

        // Make the REST request
        $httpCode = 0;
        $result = $this->doCurlRequest($endpoint, $httpCode, 'PUT', json_encode($value));

        // Return TRUE on success, FALSE on failure.
        if ($httpCode == self::CONSUL_SUCCESS_CODE && $result == 'true')
        {
            return true;
        }

        return false;
    }

    /**
    * Delete a Key/Value pair from Consul.
    *
    * Params:
    *   	$key        : the key of the pair to delete.
    *       $dataCentre : the data centre to delte from ('' for default).
    *
    * Returns: True on success, False on failure.
    *
    * Author: Calvin Rempel
    * Date: May 9, 2015
    */
    public function remove($key, $dataCentre = '')
    {
        $endpoint = $key . '?';

        // Build the URL according to supplied parameters
        if ($dataCentre !== '')
            $endpoint .= 'dc=' . $dataCentre . '&';

        // Make the REST request
        $httpCode = 0;
        $result = $this->doCurlRequest($endpoint, $httpCode, 'DELETE');

        // Return true on success / false on failure
        if ($status == self::CONSUL_SUCCESS_CODE)
        {
            return true;
        }

        return false;
    }

    /**
    * Perform a generic curl request to the given URL, with the given body.
    * The result of the call is returned from the function and the HTTP status
    * returned is supplied through the $status parameter.
    *
    * Params:
    *      $endpoint : The endpoint to make the request to.
    *      $status   : A reference through which the HTTP code is returned.
    *      $verb     : the HTTP verb to use in the request (e.g. "GET").
    *      $body     : the payload of the request (if required).
    *
    * Returns: the results of the curl request.
    *
    * Author: Calvin Rempel
    * Date: April 30, 2015
    */
    private function doCurlRequest($endpoint, &$status, $verb, $body='')
    {
        // Define the content type of the data.
        $contentType = array('Content-Type: application/text',
                             'Content-Length: ' . strlen($body));

        // Create/initialize curl handle
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $contentType);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        // Make curl request.
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close curl handle
        curl_close($ch);

        return $result;
    }

    /**
    * The Base URL to make REST requests to.
    */
    private $baseUrl;
}
