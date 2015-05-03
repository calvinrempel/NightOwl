<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This controller gets a valid key.
 *
 * @author Marc
 */

namespace NightOwlTest;

use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;
use NightOwl\Model\Auth;

class AuthControllerTestCase extends AbstractControllerTestCase
{
    protected $user = 'McBuppy';
    protected $pass = 'test';
    protected $key;
    
    public function setUp()
    {
        $this->getRequest()->setMethod('GET');
        $this->dispatch('/login/McBuppy/test');
        $json = json_decode($this->getResponse()->getBody());
        $this->key = $json->key;
        
        $this->setApplicationConfig(include '../../../config/application.config.php');
    }
}
