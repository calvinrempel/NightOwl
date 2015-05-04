<?php

namespace NightOwlTest;

use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;
use NightOwl\Model\Auth;

class LaunchCodesModel extends AbstractControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(include '../../../config/application.config.php');
    }

    function testLogin()
    {
        $this->getRequest()->setMethod('GET');
        $this->dispatch('/login/McBuppy/test');
        $this->assertResponseStatusCode(200);
    }

    function testInvalidLogin()
    {
        $this->getRequest()->setMethod('GET');
        $this->dispatch('/login/McBuppy/wrongpass');
        $this->assertResponseStatusCode(401);
    }

    function testAuthValidate()
    {
        $this->getRequest()->setMethod('GET');
        $this->dispatch('/login/McBuppy/test');
        $json = json_decode($this->getResponse()->getBody());
        $auth = new Auth();
        $this->assertEquals($auth->auth($json->key), true);
    }
    function testInvalidAuthValidate()
    {
        $this->getRequest()->setMethod('GET');
        $this->dispatch('/login/McBuppy/test');
        $json = json_decode($this->getResponse()->getBody());
        $auth = new Auth();
        $this->assertEquals($auth->auth('fakekey'), false);
    }

}
