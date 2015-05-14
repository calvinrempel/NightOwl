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
     * @var Container: The session container.
     */
    protected $session;

    /**
     *
     * @var SessionManager: the session manager used by the session.
     */
    protected $session_manager;

    /**
     * @const The TTL of the session. NOTE: this is only actually accounted for
     * server side. the cookie may not provide acurate info.
     */
    const SESSION_LENGTH = 3600; // 60 minutes~

    const SESSION_NAME = 'nightowl_auth';

    public function __construct()
    {
        // configure session manager.
        $this->session_manager = new SessionManager();
        Container::setDefaultManager($this->session_manager);

        // init session.
        $this->session = new Container(self::SESSION_NAME);
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

        $db_user = array('user' => $user);

        $userfound = $this->getDB()->Auth->findOne($db_user);

        if($userfound === NULL)
        {
            return false;
        }

        if(password_verify($pass, $userfound['pass']))
        {
            $this->session->user = $userfound['user'];
            $key = $this->update_session();

            return $key;
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
     * This function creates/extends a session for the user. It is also called
     * within auth as it is
     */
    private function update_session()
    {
        $request = new \Zend\Http\PhpEnvironment\Request();
        $session = array(
            'user'  => $this->session->user,
            'IP'    => $request->getServer('REMOTE_ADDR')
            );

        // get the mongoID for the session if it exists.
        $existing_session = $this->getDB()->Session->findOne($session);

        // check if a session was found, sets the session to that session if true.
        if($existing_session !== NULL)
        {
            $session = $existing_session;
        }

        // set the time to live.
        $session['ttl'] = time() + self::SESSION_LENGTH;
        // create a new key. key is based on a random number and the current time.
        // the key is regenerated everytime the session is updated.
        $session['key'] = substr(sha1(time(0) . rand()), 20);

        // update the local session.
        $this->session->key = $session['key'];

        // save the updated session.
        $this->getDB()->Session->save($session);

        return $session['key'];

    }
    /**
     * This function can be used to create an account. it was added when passwords
     * started to be obfuscated in storage.
     *
     * @date May 13, 2015
     *
     * @author Marc Vouve
     *
     * @param type $user the username of the account being created.
     * @param type $pass the password of the account being created.
     * @return boolean if the account creation was successful.
     */
    public function create_account($user, $pass)
    {

        // check if username is already in use.
        if($this->getDB()->Auth->findOne(array('user' => $user)))
        {
            return false;
        }

        // create user.
        $bcrypt = new \Zend\Crypt\Password\Bcrypt();
        $secure_pass = $bcrypt->create($pass);

        $user = array('user' => $user, 'pass' => $secure_pass);
        $this->getDB()->Auth->insert($user);

        return true;

    }

    /**
     * @author Marc Vouve
     *
     * @date May 13, 2015
     *
     * @return boolean true.
     *
     * This function logs a user out of the system. It clears the session locally
     * and erases the session from mongo.
     *
     * NOTE: Sessions are based on IP as well as user. This is meant to somewhat mimic
     * the popular trend to allow users to force a logout from a specific machine remotely.
     * That functionality is still todo.
     */
    public function logout()
    {
        $request = new \Zend\Http\PhpEnvironment\Request();
        $session = array(
            'user'  => $this->session->user,
            'key'   => $this->session->key,
            'IP'    => $request->getServer('REMOTE_ADDR'),
            );

        // Zends way of clearing a cookie.
        $this->session_manager->getStorage()->clear(self::SESSION_NAME);
        $this->getDB()->Session->remove($session);

        return true;
    }

    /**
     * @author Marc Vouve
     *
     * @date (original) May 2, 2015
     *
     * @revision May 14, 2015 - pretty much rewrote the whole thing.
     *
     * @return boolean true if the user is authentificated false if no session
     * is found or the session has expired in Mongo.
     *
     * @throws MongoConnectionException when mongo cannot connect.
     *
     * This function is not directly accessable by a rest endpoint and is intended
     * be called by various other endpoints where authentification is required.
     */
    public function auth()
    {

        $key = $this->session->key;
        $user = $this->session->user;

        $session = array('key' => $key, 'user' => $user);

        // Try to find a session with the correct key/username
        try
        {
            if($session = $this->getDB()->Session->findOne($session))
            {
                // if a session was found check if it's still valid against the current time.
                if($session['ttl'] < time())
                {
                    return false;
                }
                else
                {
                    // update the current session.
                    $this->update_session();

                    return true;
                }
            }
            else
            {
                return false;
            }
        }
        catch(MongoConnectionException $e)
        {
            throw $e;
        }


    }

    /**
	 * Get the username of the user.
	 *
	 * Returns: The logged in users username if the token is value, False if the
     *          token is invalid.
	 *
	 * Author: Calvin Rempel
	 * Date: May 3, 2015
	 */
    public function getCurrentUser()
    {
        return $this->session->user;
    }


}
