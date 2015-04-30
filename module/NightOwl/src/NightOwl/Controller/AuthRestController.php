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
        return new \Zend\View\Model\JsonModel(array('status'=>'success'));
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