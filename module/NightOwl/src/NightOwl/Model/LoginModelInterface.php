<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NightOwl\Model;

/**
 * This file provides an interface for a login model.
 * as login can be handled in different ways by different organisations, this provides a generalized interface for login.
 *
 * @see PlaceholderAuth
 *
 * @author Marc Vouve
 */
interface LoginModelInterface {

    /**
     * @function auth()
     *
     * @designer Marc Vouve
     *
     * @programmer Marc Vouve
     *
     * @date May 11, 2015
     *
     * @date May 14, 2015 - removed key from this interface. it shouldn't be
     * nessesary.
     *
     * This function authentificates against the stored information.
     */
    public function auth();

    /**
     * @function Login
     *
     * @designer Marc Vouve
     *
     * @programmer Marc Vouve
     *
     * @date May 11, 2015
     *
     * @prototype login($user, $pass)
     *
     * @param type $user The username of the user logging in.
     * @param type $pass The password of the user logging in.
     *
     * @returns key: returns a key created on the server for the session.
     *               if the $user and pass provided is false, return is false.
     *
     * This method logs a user in on the server.
     */
    public function login($user, $pass);


    /**
     * @function logout
     *
     * @designer Marc Vouve
     *
     * @programmer Marc Vouve
     *
     * @date May 11, 2015
     *
     * @prototype logout($user)
     *
     * @param string $user The username of the user logging out.
     * @param string $pass The password being used to log in.
     *
     * @returns key: true on success, false on failure.
     *
     * This method logs a user out from the server.
     */
    public function logout();

    /**
     * @function getCurrentUsers
     *
     * @designer Calvin Rempel
     *
     * @programmer Marc Vouve
     *
     * @date May 11, 2015
     *
     * @prototype getCurrentUser()
     *
     * @returns string: current user logged in as a string..
     *
     * This method logs a user out from the server.
     */
    public function getCurrentUser();

}
