<?php

/*
 * This is designed to test login/auth functionality.
 */

namespace NightOwlTest;

use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;

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
        //$this->getRequest()->setMethod('GET');
        $this->dispatch('/login/id');
        $this->assertResponseStatusCode(200);
    }
}
