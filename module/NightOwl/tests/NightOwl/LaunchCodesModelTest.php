<?php

namespace NightOwlTest;

use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;
use NightOwl\Model\Auth;
use Zend\Stdlib\Parameters;

class LaunchCodesModel extends AbstractControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(include '../../../config/application.config.php');
    }

    function testLogin()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(new Parameters(array('name' => 'dave', 'pass' => 'test')));;
        $this->dispatch('/auth/login');
        $this->assertResponseStatusCode(201);
    }

    function testInvalidLogin()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(new Parameters(array('name' => 'sam', 'pass' => 'fakepass')));;
        $this->dispatch('/auth/login');
        $this->assertResponseStatusCode(401);
    }

    function testAuthValidate()
    {
        $this->getRequest()->setMethod('GET');
        $this->dispatch('/login/McBuppy/test');
        $json = json_decode($this->getResponse()->getBody());
        $auth = new Auth();
        $this->assertEquals($auth->auth(), true);
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
