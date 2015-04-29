<?php
namespace NightOwl;

use Zend\Mvc\Controller\Zend_Controller_Action;

class Codes extends Zend_Controller_Action
{
    public function get($dc, $prefix, $page, $count, $filterby, $filter, $authToken)
    {
        $response = $this->getResponseWithHeader()
            ->setContent( __METHOD__.' CODE LIST REQUESTED WITH PARAMS' );
        
        return $response;
    }
}