<?php

/* 
 * This implements the AuthRestController. I'm using it to test unit testing.
 */

namespace NightOwl\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use NightOwl\Model\Auth;

class AuthRestController extends AbstractRestfulController
{
    protected $auth;
    
    public function __construct()
    {
        //parent::__construct();
        $this->auth = new Auth();
    }
    
    public function get($id)
    {
        $user = $id;
        $pass = $this->params('pw');
        
        if($this->auth->validate($user, $pass))
        {
            return new \Zend\View\Model\JsonModel(array('status' => true, 'key'=> '123fakekey'));
        }
        else 
        {
            return new \Zend\View\Model\JsonModel(array('status' => false));
        }
    }
    
    public function getList()
    {
        return new \Zend\View\Model\JsonModel(array('status' => false));
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