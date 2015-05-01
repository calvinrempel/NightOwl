<?php
namespace NightOwl\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use NightOwl\Model\LaunchCodesModel;

/**
 * LaunchCodesController provides a RESTful API for retrieving, updating, and
 * creating Launch Codes. It uses the LaunchCodesModel to persist and restore
 * the Launch Codes.
 *
 * Specifically, it provides the following endpoints:
 *    GET:
 *          /nightowl/codes/{token}/{datacentre}/{prefix}[/{filterBy}/{filter}]
 *
 * Author: Calvin Rempel
 * Date: April 29, 2015
 */
class LaunchCodesController extends AbstractRestfulController
{
    /* Constants that define the available filter types. */
    const FILTER_BY_KEY = 'Key';
    const FILTER_BY_VALUE = 'Value';

    /**
     * Retrieve the list of all Launch Codes with their data that matches the
     * constraints provided through the query string parameters.
     *
     * This method is invoked indirectly by the Router which routes GET requests
     * in the form:
     * /nightowl/codes/{token}/{dc}/{prefix}[/{filterBy}/{filter}]
     * to this method. Note that the filterBy and filter are both optional, but
     * if filterBy is provided, then filter must also be provided.
     *
     * Returns: the applicable codes in a JSON formatted array in the form:
     *              [{key, restriction, value, (metadata to come!)}, ...]
     *
     * Author: Calvin Rempel
     * Date: April 30, 2015
     */
    public function getList()
    {
        // Get all necessary data from the HTTP request.
        $dc       = $this->params('dc');
        $prefix   = $this->params('prefix');
        $token    = $this->params('token');
        $filterBy = $this->params('filterBy');
        $filter   = $this->params('filter');

        // Retrieve the applicable codes from the model.
        $codeProvider = new LaunchCodesModel();
        $codes = json_decode($codeProvider->getLaunchCodes($dc, $prefix, true));

        // If the user has asked to filter by a valid parameter, filter the results.
        if ($this->isValidFilter($filterBy) && $filter)
        {
            $codes = $this->filterResults($filterBy, $filter, $codes);
        }

        // If there are codes to output, format and inject metadata.
        if (count($codes) > 0)
        {
            // Alter the structure of the codes for applicability on the client
            $codes = $this->formatCodeOutput($codes);

            // Get the MetaData from the Database and add it to the output
            $codeProvider->injectMetadata($codes);
        }

        // Return the results as a JSON string.
        return new \Zend\View\Model\JsonModel(array('codes'=> $codes));
    }

    /**
     * Will Be Used to Create a new Launch Code Programmatically
     */
    public function create($data)
    {
    }

    /**
     * Will be Used to Edit a Launch Code Programmatically.
     */
    public function update($id, $data)
    {
    }

    /**
     * May(?) be used to Delete a Launch Code Programmatically.
     */
    public function delete($id)
    {
    }

    /**
     * Check if a filterBy string is a valid key to filter results by.
     *
     * Params:
     *      $filterBy : the key to filter on (e.g. "Key", "Value", etc.)
     *
     * Returns: True if the filter type is in the list of valid types, False
     *          if not.
     *
     * Author: Calvin Rempel
     * Date: April 29, 2015
     */
    private function isValidFilter($filterBy)
    {
        // The available filter types.
        $typeArray = array(self::FILTER_BY_KEY, self::FILTER_BY_VALUE);

        // If the type is acceptable, return true.
        if (in_array($filterBy, $typeArray))
        {
            return true;
        }

        return false;
    }

    /**
     * Filter the list by the given value on the given key.
     *
     * Params:
     *      $filterBy : the key to filter on (e.g. "Key", "Value")
     *      $filter   : the value of the filter to match against
     *      $codes    : the current list of codes to run the filter on
     *
     * Returns: The subset of the input $codes that matches the filter parameters.
     *
     * Author: Calvin Rempel
     * Date: April 29, 2015
     */
    private function filterResults($filterBy, $filter, $codes)
    {
        $retval = array();
        $matchVal;

        // Check each code in the list to see if it matches the filter parameters
        // and if it does, add it to the output list.
        foreach ($codes as $code)
        {
            // Determine which value is being filtered on.
            if ($filterBy == self::FILTER_BY_KEY)
                $matchVal = $code->Key;
            if ($filterBy == self::FILTER_BY_VALUE)
                $matchVal = $code->Value;

            // If the code matches the filter, add to output array.
            if (preg_match("/$filter/i", $matchVal))
            {
                $retval[] = $code;
            }
        }

        return $retval;
    }

    /**
     * Takes an array of codes in the raw format returned from Consul and strips
     * keys and changes key names to prepare the data for output to the client.
     *
     * Params:
     *      $codes  : the array of codes to format.
     *
     * Returns: The array of codes in the format:
     *              [{key, restriction, value, (metadata to come!)}...]
     *
     * TODO: Get restriction and value from Value stored in Consul.
     *
     * Author: Calvin Rempel
     * Date: April 29, 2015
     */
    private function formatCodeOutput($codes)
    {
        $output = array();

        // Convert each code into an associative array with nice names and only
        // relevant data.
        foreach ($codes as $code)
        {
            $temp = array(
                'key'         => $code->Key,
                'restriction' => 'boolean',             // Must be retrieved from value JSON
                'value'       => 'true',                // Must be retrieved from value JSON
            );

            $output[] = $temp;
        }

        return $output;
    }
}
