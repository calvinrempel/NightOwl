<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NightOwl\Model;

use MongoClient;
use Zend\Cache\Storage\Adapter\MongoDB;

/**
 * Auth abstracts OAuth to interface with MongoDB
 *
 * @author Marc
 */
class Auth {
    protected $db;
    
    public function __construct()
    {
        $config = $this->getConfig();
        $dbn = 'nightowl';
        $m = new MongoClient($config['dbaccess']);
        $this->db = $m->$dbn;
        $this->db->Auth;
        
        
    }
    
    public function validate($user, $pass)
    {
        $user = array('user' => $user);
        
        $user2 = $this->db->Auth->findOne($user);
        
        if($user2['pass'] === $pass)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * I don't understand how I'm supposed to get this any other way.
     */
    private function getConfig()
    {
        return include 'config/autoload/local.php';
    }
}
