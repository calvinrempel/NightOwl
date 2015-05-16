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
    protected $user = 'dave';
    protected $pass = 'test';
    protected $container;
    
    public function setUp()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(new Parameters(array('name' => 'dave', 'pass' => 'test')));
        $this->dispatch('/auth');
        $json = json_decode($this->getResponse()->getBody());
        $this->key = $json->key;
        
        $this->setApplicationConfig(include '../../../config/application.config.php');
    }
}
