<?php
namespace NightOwl\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use NightOwl\Model\LaunchCodesModel;
use NightOwl\Model\Auth;

/**
* LaunchCodesController provides a RESTful API for retrieving, updating, and
* creating Launch Codes. It uses the LaunchCodesModel to persist and restore
* the Launch Codes.
*
* Specifically, it provides the following endpoints:
*    GET:
*          /codes/{token}/{datacentre}/{prefix}[/{filterBy}/{filter}]
*
*    POST:
*      /codes/{token}/{key}
*        BODY:
*          {
*            "restriction"   : "___",
*            "value"        : "___",
*            "description"   : "___",
*            "availableToJS" : "true/false",
*                      "owner"         : "___",             {if not set, current user is used}
*          }
*
*    DELETE:
*      /codes/{token}/{key}
*
* Author: Calvin Rempel
* Date: April 29, 2015
*/
class LaunchCodesController extends AbstractRestfulController
{
    /* Constants that define the available filter types. */
    const FILTER_BY_KEY = 'Key';
    const FILTER_BY_VALUE = 'Value';
    const HARD_OUTPUT_LIMIT = 100;

    /* Return HTTP status codes */
    const RETURN_STATUS_SUCCESS = 200;
    const RETURN_STATUS_INVALID_ARGUMENTS = 400;
    const RETURN_STATUS_AUTH_INVALID = 401;

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
    *
    * REVISIONS:
    *      Calvin Rempel - May 3, 2015
    *          - Added fixed output limit on result set.
    */
    public function getList()
    {
        // Verify that the user is authorized
        $authResult = $this->verifyAuthToken();
        if ($authResult !== true)
        {
            return $authResult;
        }

        // Get all necessary data from the HTTP request.
        $token    = $this->params('token');
        $dc       = $this->params('seg1');
        $prefix   = $this->params('seg2');
        $filterBy = $this->params('seg3');
        $filter   = $this->params('seg4');

        // Verify the presence of the required arguments
        if (is_null($dc))
        {
            return $this->prepareInvalidArgumentResponse();
        }

        // Retrieve the applicable codes from the model.
        $codeProvider = new LaunchCodesModel();
        $codes = $codeProvider->getLaunchCodes($dc, $prefix, true);

        // If the user has asked to filter by a valid parameter, filter the results.
        if ($this->isValidFilter($filterBy) && $filter)
        {
            $codes = $this->filterResults($filterBy, $filter, $codes);
        }

        // If there are codes to output, format and inject metadata.
        if (count($codes) > 0)
        {
            // Alter the structure of the codes for applicability on the client
            $codes = array_slice($this->formatCodeOutput($codes), 0, self::HARD_OUTPUT_LIMIT);

            // Get the MetaData from the Database and add it to the output
            $codeProvider->injectMetadata($codes);
        }

        // Return the results as a JSON string.
        return new \Zend\View\Model\JsonModel(array('codes'=> $codes));
    }

    /**
    * This function creates OR updates a Launch Code.
    * This function is called in response to a POST request.
    *
    * Params:
    *    $data : the data posted to the server; must contains:
    *        {
    *          'restriction' : '__',
    *          'value' : '__',
    *          'description' : '__',
    *          'availableToJS' : 'true|false',
    *        }
    *
    * Returns: a status code in JSON if the request is OK (either true or false).
    *      Will return empty JSON is request is not authorized or missing parameter.
    *
    * Author: Calvin Rempel
    * Date: May 1, 2015
    */
    public function create($data)
    {
        // Verify that the user is authorized
        $authResult = $this->verifyAuthToken();
        if ($authResult !== true)
        {
            return $authResult;
        }

        // Retrieve Code parameters
        $key = $this->params('seg1');

        // Verify the presence of the arguments
        if (is_null($key) ||
            !isset($data['restriction']) ||
            !isset($data['value']) ||
            !isset($data['description']) ||
            !isset($data['availableToJS']))
        {
            return $this->prepareInvalidArgumentResponse();
        }

        // Get the parameters
        $restriction = $data['restriction'];
        $value       = $data['value'];
        $description = $data['description'];
        $js      = $data['availableToJS'];
        $owner       = '';

        if (isset($data['owner']))
            $owner = $data['owner'];
        else
            $owner = (new Auth())->getCurrentUser($this->params('token'));

        // Request Code Modification on Server
        $codeProvider = new LaunchCodesModel();
        if ( $codeProvider->createorEditLaunchCode($this->params('token'), $key,
            $restriction, $value, $owner, $description, $js) )
        {
            return new \Zend\View\Model\JsonModel(array('status' => true));
        }

        return new \Zend\View\Model\JsonModel(array('status' => false));
    }

    /*
    * This function Deletes a Launch Code (deleteList is used as opposed to delete to
    * circumvent Zend's requirement to have an ID parameter in the delete route).
    * This function is called in response to a Delete request.
    *
    * Returns: a status code in JSON if the request is OK (either true or false).
    *      Will return empty JSON is request is not authorized or missing parameter.
    *
    * Author: Calvin Rempel
    * Date: May 1, 2015
    */
    public function deleteList()
    {
        // Verify that the user is authorized
        $authResult = $this->verifyAuthToken();
        if ($authResult !== true)
        {
            return $authResult;
        }

        // Retrieve Code parameters
        $key = $this->params('seg1');

        // Verify presence of required parameters
        if (is_null($key))
        {
            return $this->prepareInvalidArgumentResponse();
        }

        // Request Code Creation on Server
        $codeProvider = new LaunchCodesModel();
        if ( $codeProvider->deleteLaunchCode($this->params('token'), $key) )
        {
            return new \Zend\View\Model\JsonModel(array('status' => true));
        }

        return new \Zend\View\Model\JsonModel(array('status' => false));
    }

    /**
    * Check whether the user is authorized to perform the action.
    *
    * Returns: true if user is authorized, or an empty JSON model otherwise.
    *
    * Author: Calvin Rempel
    * Date: May 1, 2015
    *
    * REVISIONS:
    *      Calvin Rempel - May 3, 2015
    *          - Added auth token validity check.
    */
    private function verifyAuthToken()
    {
        // Verify presence and validity of Auth Token
        if (!is_null($this->params('token')))
        {
            $auth = new Auth();
            $valid = $auth->auth($this->params('token'));

            if ($valid)
            {
                return true;
            }
        }

        // If invalid (or missing) Auth Token, return error.
        $this->response->setStatusCode(self::RETURN_STATUS_AUTH_INVALID);
        $this->response->setReasonPhrase('Auth Token is Required.');
        return new \Zend\View\Model\JsonModel();
    }

    /**
    * Prepare the response header to indicate missing parameters.
    *
    * Returns: an empty JSON model for output.
    *
    * Author: Calvin Rempel
    * Date: May 1, 2015
    */
    private function prepareInvalidArgumentResponse()
    {
        $this->response->setStatusCode(self::RETURN_STATUS_INVALID_ARGUMENTS);
        $this->response->setReasonPhrase("Missing Required Parameters.");
        return new \Zend\View\Model\JsonModel();
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
    * Author: Calvin Rempel
    * Date: April 29, 2015
    */
    private function formatCodeOutput($codes)
    {
        $output = array();

        // If there are no codes, return an empty array.
        if (!is_array($codes) || count($codes) == 0)
        {
            return $output;
        }

        // Convert each code into an associative array with nice names and only
        // relevant data.
        foreach ($codes as $code)
        {
            $value = json_decode(base64_decode($code['Value']), true);
            $temp = array(
                'key'           => $code['Key'],
                'restriction'   => $value['restriction'],
                'value'         => $value['value'],
                'availableToJS' => $value['availableToJS']
            );

            $output[] = $temp;
        }

        return $output;
    }
}
