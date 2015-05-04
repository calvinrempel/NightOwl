<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NightOwl\Model;

use MongoClient;


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
        $dbn = $config['mongo']['name'];
        $m = new MongoClient($config['mongo']['url']);
        $this->db = $m->$dbn;
        //$this->db->Auth;
    }

    public function login($user, $pass)
    {
        $user = array('user' => $user);

        $userfound = $this->db->Auth->findOne($user);

        if($userfound == NULL)
        {
            return false;
        }

        if($userfound['keyTTL'] < time(0))
            $userfound['key'] = substr(sha1(time(0) . rand()), 20);
        $userfound['keyTTL'] = time(0) + 60 * 60;   // One hour

        $this->db->Auth->update($user, $userfound);

        if($userfound['pass'] === $pass)
        {
            return $userfound['key'];
        }
        else
        {

            return false;
        }
    }

    public function auth($key)
    {
        $token = array('key' => $key);
        if(($user = $this->db->Auth->findOne($token)) === NULL )
        {
            return false;
        }
        if( $user['keyTTL'] < time(0) )
        {
            $user['key'] = '';
            $this->db->Auth->update($token, $user);

            return false;
        }

        return true;

    }

    /**
     * I don't understand how I'm supposed to get this any other way.
     */
    private function getConfig()
    {
        return include __DIR__ . '../../../../../../config/autoload/local.php';
    }
}
