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
        echo 'dave';
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
    
    public function index()
    {
        echo 'pineapple';
    }
}