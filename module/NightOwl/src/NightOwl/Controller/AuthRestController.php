<?php

/*
 * This implements the AuthRestController. I'm using it to test unit testing.
 */

namespace NightOwl\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use \Zend\View\Model\JsonModel;
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

        {
            $this->response->setStatusCode(401);
            return new \Zend\View\Model\JsonModel(array('status' => false));
        }
    }

    public function getList()
    {
        return new \Zend\View\Model\JsonModel(array('status' => false));
    }

    /**
     *
     * @param type $data
     * @return JsonModel
     */
    public function create($data)
    {

        if($this->params('method') === 'login')
        {
            return $this->login($data);
        }
        if($this->params('method') === 'create')
        {
            return $this->createUser($data);
        }
    }

    protected function createUser($data)
    {
        $name = $data['name'];
        $pass = $data['pass'];

        // attempt to create an account via the model.
        $created = $this->auth->create_account($name, $pass);

        if($created)
        {
            return new JsonModel(array('success' => true));
        }
        else
        {
            // set status code to indicate a conflict.
            $this->response->setStatusCode(409);
            return new JsonModel(array('success' => false));
        }
    }

    protected function login($data)
    {
        if(!isset($data['name']) || !isset($data['pass']))
        {
            // invalid request, no username or pass.
            $this->response->setStatusCode(400);
            return new \Zend\View\Model\JsonModel(array('status' => false));
        }

        $name = $data['name'];
        $pass = $data['pass'];

        // attempt to authentificate user.
        if($key = $this->auth->login($name, $pass))
        {
            $this->responce->setStatusCode(201);
            return new \Zend\View\Model\JsonModel(array('status' => true, 'key'=> $key));
        }
        else
        {
            // user was not authentificated.
            $this->response->setStatusCode(401);
            return new JsonModel(array());
        }
    }

    public function update($id, $data)
    {

    }

    public function deleteList()
    {
        if($this->params('method') === 'logout')
        {
            return new JsonModel(array('success' => $this->auth->logout()));
        }

        return new JsonModel(array());
    }
}
