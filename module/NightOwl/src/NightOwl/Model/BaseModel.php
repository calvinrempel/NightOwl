<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NightOwl\Model;

/**
 * Description of BaseModel
 *
 * @author mvouve
 */
class BaseModel 
{
    protected $services;

    /**
     * I don't understand how I'm supposed to get this any other way.
     */
    protected function getConfig()
    {
        return include __DIR__ . '../../../../../../config/autoload/local.php';
    }
}
