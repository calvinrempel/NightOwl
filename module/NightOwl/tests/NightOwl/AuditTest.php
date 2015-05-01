<?php

/*
 * This is designed to test login/auth functionality.
 */

namespace NightOwlTest;

use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;
use NightOwl\Model\Audit;


/**
 * Description of LoginTest
 *
 * @author Marc
 */
class AuditTest extends AbstractControllerTestCase
{
    protected $audit;
    public function setUp()
    {
        $this->audit = new Audit();
        $this->setApplicationConfig(include '../../../config/application.config.php');
    }
    
    public function testAuditInsert()
    {
        $this->getRequest()->setMethod('GET');
        $this->dispatch('/login/McBuppy/test');
        $json = json_decode($this->getResponse()->getBody());
        $this->assertEquals($this->audit->LogEdit($json->key, 'test message', 'code'), true);
        
    }
}