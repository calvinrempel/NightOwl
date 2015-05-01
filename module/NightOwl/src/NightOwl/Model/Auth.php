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
        $dbn = 'nightowl';
        include('config/autoload/local.php.dist');
        $m = new MongoClient($dbname);
        //var_dump( $m->getHosts() );
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
        include('config/autoload/local.php.dist');
    }
}
