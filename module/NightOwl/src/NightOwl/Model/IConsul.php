<?php

namespace NightOwl\Model;

/**
 * IConsul is an interface to a Consul Key Value store. It provides a common
 * interface for interacting with Consul potentially using different mechanisms
 * (eg REST api calls, Conul Watch pushes, etc).
 *
 * Author: Calvin Rempel
 * Date: May 9, 2015
 */
interface IConsul
{
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
    public function get($prefix, $recurse, $dataCentre);

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
    public function set($key, $value, $dataCentre);

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
    public function remove($key, $dataCentre);
}
