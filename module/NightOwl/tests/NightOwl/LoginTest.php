<?php

/*
 * This is designed to test login/auth functionality.
 */

namespace NightOwlTest;

use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;
use NightOwl\Model\Auth;
use Zend\Stdlib\Parameters;

/**
 * Description of LoginTest
 *
 * @author Marc
 */
class LoginTest extends AbstractControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(include '../../../config/application.config.php');
    }
    
    function testLogin()
    {
        
        $this->getRequest()->setMethod('POST')
                ->setPost(new Parameters(array('name' => 'dave', 'pass' => 'test')));
        $this->dispatch('/auth/login');
        $this->assertResponseStatusCode(201); 
        
    }
    
    
    function testInvalidLogin()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(new Parameters(array('name' => 'asdf', 'pass' => 'test')));
        $this->dispatch('/auth/login');
        $this->assertResponseStatusCode(401); 
    }
    
    function testAuthValidate()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(new Parameters(array('name' => 'dave', 'pass' => 'test')));
        $this->dispatch('/auth/login');
        $auth = new Auth();
        $this->assertEquals($auth->auth(), true);
    }
    
    function testLogout()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(new Parameters(array('name' => 'dave', 'pass' => 'test')));
        $this->dispatch('/auth/login');
        $json = json_decode($this->getResponse()->getBody());
        $auth = new Auth();
        $this->assertEquals($auth->auth(), true);
        
        $this->getRequest()->setMethod('DELETE');
        $this->dispatch('/auth/logout');
        $this->assertEquals($auth->auth(), false);
    }
    
    function testInvalidAuthValidate()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(new Parameters(array('name' => 'dave', 'pass' => 'asdf')));
        $this->dispatch('/auth/login');
        $json = json_decode($this->getResponse()->getBody());
        $auth = new Auth();
        $this->assertEquals($auth->auth('fakekey'), false);
    }
    
}
