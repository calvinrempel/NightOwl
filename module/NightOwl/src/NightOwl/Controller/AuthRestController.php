<?php

/* 
 * This implements the AuthRestController. I'm using it to test unit testing.
 */

namespace NightOwl\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

class AuthRestController extends AbstractRestfulController
{
    public function get($id)
    {
        $user = $id;
        $pass = $this->params('pw');
        
        if(strlen($pass) > 3 )
            return new \Zend\View\Model\JsonModel(array('status' => true, 'key'=> '123fakekey'));
        else {
            return new \Zend\View\Model\JsonModel(array('status' => false));
        }
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
}