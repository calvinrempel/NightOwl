<?php

namespace NightOwl\Model;

use MongoClient;

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
    /* HTTP Statuses that indicate Consul response types. */
    const CONSUL_SUCCESS_CODE = 200;

    /**
     * Create a new instance of LaunchCodesModel and connect to the MongoDB.
     */
    public function __construct()
    {
        $this->config = $this->getConfig();
        $dbn = $this->config['mongo']['name'];
        $m = new MongoClient($this->config['mongo']['url']);
        $this->db = $m->$dbn;
    }

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
	 * Create a new launch code in both Consul and the MongoDB.
	 *
	 * TODO: Get Username of Current User and fillin "Owner"
	 *
	 * Params:
	 *		$key           : the Launch Code Key
	 *		$restriction   : the restriction type (eg "boolean")
	 *		$value 		   : the value associated with the restriction (eg "true")
	 *		$description   : a description of the Launch Code
	 *		$availableToJS : true or false - whether the code is available in JavaScript.
	 *
	 * Returns: True on success, False on failure
	 *
	 * Author: Calvin Rempel
	 * Date: May 1, 2015
     *
     * REVISIONS:
     *      Calvin Rempel - May 3, 2015
     *          - Added Mongo insertion/updating.
	 */
	public function createOrEditLaunchCode($key, $restriction, $value, $owner, $description, $availableToJS)
	{
        // Try to set the code in Consul. If that works, add to Mongo.
		if ($this->setInConsul($key, $restriction, $value, $availableToJS))
        {
            // Setup update/creation constraints
            $where = array('key' => $key);
            $obj = array(
                'key'         => $key,
                'description' => $description,
                'createdDate' => date('Y-m-d H:i:s'),
                'owner'       => 'DummyOwner',
            );
            $options = array('upsert' => true);

            // If the code doesn't already exist, set the creation date.
            $code = array('key' => $key);
            if(($code = $this->db->LaunchCodes->findOne($code)) !== NULL )
            {
                $obj['createdDate'] = $code['createdDate'];
            }

            // Create/Edit the LaunchCode in Mongo
            $this->db->LaunchCodes->update($where, $obj, $options);

            return true;
        }

        return false;
	}

	/**
	 * Delete a Launch Code from Consul (and eventually MongoDB).
	 *
	 * Params:
	 *		$key : the key of the launch code to delete.
	 *
	 * Returns: True on success, False on failure.
	 *
	 * Author: Calvin Rempel
	 * Date: May 1, 2015
     *
     * REVISIONS:
     *      Calvin Rempel - May 3, 2015
     *          - Added Mongo entry deletion.
	 */
	public function deleteLaunchCode($key)
	{
		$url = $this->getConsulKVUrl() . $key;
		$status = 0;

		// Attempt to Delete from Consul
		$this->doCurlRequest($url, $status, 'DELETE');

		// Return true on success / false on failure
		if ($status == self::CONSUL_SUCCESS_CODE)
        {
            $this->db->LaunchCodes->remove(array('key' => $key));
			return true;
        }

		return false;
	}

    /**
     * Gets the metadata associated with each code in the list and adds it to
     * the code in the array.
	 *
     * Params:
     *      $codes : the array of codes (which contains at least the key "key").
     *               When the function completes, each code in the array will
     *               have it's available metadata added to it.
     *
     * Author: Calvin Rempel
     * Date: May 3, 2015
     *
     * REVISIONS:
     *      Calvin Rempel - May 3, 2015
     *          - Added MetaData retrieval from MongoDB
     */
    public function injectMetadata(&$codes)
    {
        $keys = array();
        $count = count($codes);

        // Build the query parameters for Mongo lookup.
        for ($i = 0; $i < $count; $i++)
        {
            $keys[] = $codes[$i]['key'];
        }

        // Create an associative array where code key maps to a reference of the code.
        $codeMap = array();
        foreach ($codes as &$code)
        {
            $codeMap[$code['key']] = &$code;
        }

        // Get the MetaData from Mongo
        $metadata = $this->db->LaunchCodes->find(array('key' => array('$in' => $keys)));

        // Inject MetaData into the codes
        foreach ($metadata as $codeData)
        {
            $codeMap[$codeData['key']]['description'] = $codeData["description"];
            $codeMap[$codeData['key']]['dateCreated'] = $codeData["createdDate"];
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
        $consul = $this->config['consul'];

        return $consul['host'] . ':' . $consul['port'] . '/v1/kv/';
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
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/text','Content-Length: ' . strlen($body)));
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
	 * Set the value of the key in the Consul data store. If the key does not
	 * currently exist, it will be created.
	 *
	 * Params:
	 *		$key 		   : the Launch Code key
	 *		$restriction   : the restriction type of the code (eg "boolean")
	 *		$value		   : the value associated with the restriction (eg "true")
     *      $availableToJS : true or false - whether the code is available in JavaScript.
	 *
	 * Returns: True if successfully set, False on failure.
	 *
	 * Author: Calvin Rempel
	 * Date: April 1, 2015
	 */
	private function setInConsul($key, $restriction, $value, $availableToJS)
	{
		// Create the value to store in Consul
        $consulData = new \stdClass();
        $consulData->restriction  = $restriction;
        $consulData->value        = $value;
        $consulData->availbleToJS = $availableToJS;

		// Create the URL to PUT to.
		$url = $this->getConsulKVUrl() . $key;
		$status;

		// Make creation request to Consul
		$result = $this->doCurlRequest($url, $status, 'PUT', json_encode($consulData));

		// Return TRUE on success, FALSE on failure.
		if ($status == self::CONSUL_SUCCESS_CODE && $result == 'true')
		{
			return true;
		}

		return false;
	}

    /**
     * I don't understand how I'm supposed to get this any other way.
     */
    private function getConfig()
    {
        return include __DIR__ . '../../../../../../config/autoload/local.php';
    }

    /**
     * A reference to the MonogDB database that holds LaunchCode metadata.
     */
    private $db;

    /**
     * Configuration data found in NightOwl/config/autoload/local.php
     */
    private $config;
}
