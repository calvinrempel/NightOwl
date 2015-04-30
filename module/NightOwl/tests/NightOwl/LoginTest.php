<?php

/*
 * This is designed to test login/auth functionality.
 */

namespace NightOwlTest;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Description of LoginTest
 *
 * @author Marc
 */
class LoginTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(include '../../../config/application.config.php');
    }
    
    function testLogin()
    {
        $this->getRequest()->setMethod('GET');
        $this->dispatch('/login/id/pw');
        $this->assertResponseStatusCode(200);
    }
}
