<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NightOwl\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use NightOwl\Model\Audit;
use NightOwl\Model\Auth;
use Zend\Stdlib\Parameters;


class AuditController extends AbstractRestfulController
{
    protected $audit;
    protected $auth;
    const DEFAULT_RESULTS_PER_PAGE = 100;

    public function __construct()
    {
        //parent::__construct();
        $this->audit = new Audit();
        $this->auth = new Auth();
    }


    public function get($id)
    {

    }

    public function getList()
    {
        $query = $this->params('query');
        if ($this->auth->auth())
        {
            $query = json_decode(urldecode($query), true);
            if(is_array($query))
                return new \Zend\View\Model\JsonModel($this->audit->getLog($query));
            else {
                $this->response->setStatusCode(400);

                return new \Zend\View\Model\JsonModel(null);
            }

        }
        else
        {
            $this->response->setStatusCode(401);

            return new \Zend\View\Model\JsonModel(null);
        }
    }

    public function update($id, $data)
    {

    }

    public function create($data)
    {

    }

    public function delete($id)
    {

    }
}
