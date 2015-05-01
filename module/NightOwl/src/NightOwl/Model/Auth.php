<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NightOwl\Model;

use Mongo;
use Zend\Cache\Storage\Adapter\MongoDB;

/**
 * Auth abstracts OAuth to interface with MongoDB
 *
 * @author Marc
 */
class Auth {
    protected $mongo;
    protected $options;
    protected $saveHandler;
    protected $manager;
    
    public function __construct()
    {
        $mongo = new Mongo;
        $options = new MongoDBOptions(array(
           'database'   => 'nightowl',
           'collection' => 'nightowl'
        ));
        
        $saveHandler = new MongoDB($mongo, $options);
        $manager     = new SessionManager();
    }
}
