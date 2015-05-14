<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NightOwl\Model;

use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use MongoClient;


/**
 * Auth abstracts OAuth to interface with MongoDB
 *
 * @author Marc
 */
class Auth extends BaseModel implements LoginModelInterface{
    /**
     *
     * @var MongoDB: The database in which auth is stored.
     */
    protected $db;
    /**
     *
     * @var Container: The session container.
     */
    protected $session;
    
    protected $session_manager;
    
    /**
     * @const The TTL of the session.
     */
    const SESSION_LENGTH = 3600; // 60 minutes~
    
    public function __construct()
    {
        // Load Config.
        $db = $this->getConfig()['mongo'];
        
        // init mongo connection.
        $dbn = $db['name'];
        $m = new MongoClient($db['url']);
        $this->db = $m->$dbn;

        // configure session manager.
        $this->session_manager = new SessionManager();
        Container::setDefaultManager($this->session_manager);
        
        // init session.
        $this->session = new Container('nightowl_auth');
    }
    

    /** 
     * @author Marc Vouve
     * 
     * @designer Marc Vouve
     * 
     * @date April 30th
     * 
     * @revision May 13, added session.
     * 
     * @param array $user
     * @param type $pass
     * @return a unique key on success, or false on failure.
     * 
     * Creates a user session, on mongo that stores the IP and a unique session
     * key. 
     */
    public function login($user, $pass)
    {
        
        $user = array('user' => $user);

        $userfound = $this->db->Auth->findOne($user);

        if($userfound == NULL)
        {
            return false;
        }

        if(password_verify($pass, $userfound['pass']))
        {
            
            var_dump($this->session);
            $this->session->user = $userfound['pass'];
            $this->set_session();
            
            return $userfound['key'];
        }
        else
        {
            return false;
        }
    }
    
    /**
     * @author Marc Vouve
     * 
     * @designer Marc Vouve
     * 
     * @date May 13, 2015
     * 
     * This function creates/extends a session for the user.
     */
    private function set_session()
    {
        $request = \Zend\Http\PhpEnvironment\Request();
        $session = array(
            'user'  => $this->session->user, 
            'key'   => $this->session->key,
            'IP'    => $request->getServer('REMOTE_ADDR')
            );
        
        // get the mongoID for the session if it exists.
        $existing_session = $this->db->session->findOne($session);
        
        // check if a session was found, sets the session to that session if true.
        if($existing_session !== NULL)
        {
            $session = $existing_session;
        }
        
        // set the time to live.
        $session['ttl'] = time() + self::SESSION_LENGTH;  
        // create a new key.
        $session['key'] = substr(sha1(time(0) . rand()), 20);
        
        // update the local session.
        $this->session->key = $session['key'];
        
        // save the updated session.
        $this->db->session->save($session);
            
    }
    
    public function create_account($user, $pass)
    {
        if($this->db->Auth->findOne(array('user' => $user)))
        {
            return false;
        }
        
        $user = array('user' => $user, 'pass' => $pass);
        
        $this->db->Auth->insert($user);
        
    }
    
    
    public function logout($user)
    {
        
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
	 * Get the username of the user with the given authentication token.
	 *
	 * Params:
	 *		$token   : the users authentication token
	 *
	 * Returns: The logged in users username if the token is value, False if the
     *          token is invalid.
	 *
	 * Author: Calvin Rempel
	 * Date: May 3, 2015
	 */
    public function getCurrentUser($token)
    {
        $utoken = array('key' => $token);
        if(($user = $this->db->Auth->findOne($utoken)) === NULL )
        {
            return false;
        }

        return $user['user'];
    }

    
}
