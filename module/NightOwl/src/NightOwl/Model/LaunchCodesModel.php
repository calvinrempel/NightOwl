<?php

namespace NightOwl\Model;

use NightOwl\Model\Audit;
use NightOwl\Model\RestfulConsul;
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
    const METHOD_GET = 'GET';
    const METHOD_SET = 'SET';
    const METHOD_DELETE = 'DELETE';

    /**
    * Create a new instance of LaunchCodesModel and connect to the MongoDB.
    */
    public function __construct()
    {
        $this->config = $this->getConfig();

        // Setup IConsul objects to use for each request type
        $restfulConsul= new RestfulConsul($this->config['consul']['host'],
                                          $this->config['consul']['port']);

        // Map Consul Interfaces to the appropriate request types.
        $this->consulInterfaces = array(
                self::METHOD_GET    => $restfulConsul,
                self::METHOD_SET    => $restfulConsul,
                self::METHOD_DELETE => $restfulConsul
        );

        // Create DB
        $dbn = $this->config['mongo']['name'];
        $m = new MongoClient($this->config['mongo']['url']);
        $this->db = $m->$dbn;

        // Create Audit Controller
        $this->audit = new Audit();
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
        $consul = $this->consulInterfaces[self::METHOD_GET];
        return $consul->get($prefix, $recurse, $dataCentre);
    }

    /**
    * Create a new launch code in both Consul and the MongoDB.
    *
    * Params:
    *    $token         : the users authorization token
    *		$key           : the Launch Code Key
    *		$restriction   : the restriction type (eg "boolean")
    *		$value 		     : the value associated with the restriction (eg "true")
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
	*
	*	   Calvin Rempel - May 8, 2015
	*		   - Fix: Creation date set on creation, not altered on edit.
    */
    public function createOrEditLaunchCode($token, $key, $restriction, $value, $owner, $description, $availableToJS)
    {
        $logMessage = ' - CREATE - ';

        // Try to set the code in Consul. If that works, add to Mongo.
        if ($this->setInConsul($key, $restriction, $value, $availableToJS))
        {
            // Setup update/creation constraints
            $where = array('key' => $key);
            $obj = array(
                'key'         => $key,
                'description' => $description,
                'createdDate' => date('Y-m-d H:i:s'),
                'owner'       => $owner,
            );
            $options = array('upsert' => true);

            // If the code doesn't already exist, set the creation date.
            $code = array('key' => $key);
            if(!is_null(($code = $this->db->LaunchCodes->findOne($code))))
            {
                $obj['createdDate'] = $code['createdDate'];
                $logMessage = ' - EDIT - ';
            }

            // Create/Edit the LaunchCode in Mongo
            $this->db->LaunchCodes->update($where, $obj, $options);

            // Log the edit
            $logMessage .= $restriction . ' - ' . $value . ' JS(' . $availableToJS . ')';

            $this->audit->LogEdit($token, $logMessage, $key);

            return true;
        }

        return false;
    }

    /**
    * Delete a Launch Code from Consul (and eventually MongoDB).
    *
    * Params:
    *    $token : the users authorization token
    *		$key   : the key of the launch code to delete.
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
    public function deleteLaunchCode($key, $dataCentre='')
    {
        $consul = $this->consulInterfaces[self::METHOD_DELETE];
        $success = $consul->remove($key, $dataCentre);

        // Return true on success / false on failure
        if ($success)
        {
            $this->audit->LogEdit($token, ' - DELETE - ', $key);
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
            $codeMap[$codeData['key']]['owner'] = $codeData["owner"];
        }
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
        $consulData->availableToJS = $availableToJS;

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
    * A reference to an Audit Model for logging audit information.
    */
    private $audit;

    /**
    * Configuration data found in NightOwl/config/autoload/local.php
    */
    private $config;

    /**
    * A map indicating which IConsul type to use for each method.
    */
    private $consulInterfaces;
}
