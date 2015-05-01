<?php
namespace NightOwl\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

class LaunchCodesController extends AbstractRestfulController
{
    public function get($id)
    {
        return new \Zend\View\Model\JsonModel(array('status'=> $id));
    }

    public function getList()
    {
        $dc       = $this->params('dc');
        $prefix   = $this->params('prefix');
        $token    = $this->params('token');
        $filterBy = $this->params('filterBy');
        $filter   = $this->params('filter');

        $codes = json_decode($this->getCodesFromConsul($dc, $prefix, true));

        if ($this->isValidFilter($filterBy) && $filter)
        {
            $codes = $this->filterResults($filterBy, $filter, $codes);
        }

        return new \Zend\View\Model\JsonModel(array('codes'=> $codes));
    }

    public function create($data)
    {

    }

    public function update($id, $data)
    {

    }

    public function delete($id)
    {

    }

    private function getCodesFromConsul($dataCentre, $prefix, $recurse)
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
        $result = $this->makeCurlRequest($url, $httpCode);

        // Successfully retrieved data from Consul
        if ($httpCode == self::CONSUL_SUCCESS_CODE)
            return $result;

        // An error occurred or the set is empty
        if (!$result)
            return FALSE;
    }

    private function isValidFilter($filterBy)
    {
        if (in_array($filterBy, array(self::FILTER_BY_KEY, self::FILTER_BY_VALUE)))
        {
            return true;
        }

        return false;
    }

    private function filterResults($filterBy, $filter, $codes)
    {
        $retval = array();
        $matchVal;

        foreach ($codes as $code)
        {
            // Determine which value is being filtered on.
            if ($filterBy == self::FILTER_BY_KEY)
                $matchVal = $code->Key;
            if ($filterBy == self::FILTER_BY_VALUE)
                $matchVal = $code->Value;

            print_r($matchVal);

            // If the code matches the filter, add to output array.
            if (preg_match("/$filter/i", $matchVal))
            {
                $retval[] = $code;
            }
        }

        return $retval;
    }

    private function getConsulKVUrl()
    {
        return self::CONSUL_HOST . ':' . self::CONSUL_PORT . '/v1/kv/';
    }

    private function makeCurlRequest($url, &$status)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        // Make curl request.
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close curl handle
        curl_close($ch);

        return $result;
    }

    const CONSUL_HOST = 'localhost';
    const CONSUL_PORT = 8500;
    const CONSUL_SUCCESS_CODE = 200;
    const CONSUL_EMPTY_CODE = 404;

    const FILTER_BY_KEY = 'Key';
    const FILTER_BY_VALUE = 'VALUE';
}
