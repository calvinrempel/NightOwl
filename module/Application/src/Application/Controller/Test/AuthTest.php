<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NightOwl;

error_reporting( E_ALL | E_STRICT );
chdir(__DIR__);

class AuthTest
{
    public static $serviceManager;
    
    public static function init()
    {
        $ModPaths = array(dirname(dirname(__DIR__)));
        
        if(($path = static::findParentPath('vendor')))
        {
            $ModPath[] = $path; 
        }
        
        if(($path = static::findParentPath('module') !== $ModPaths[0]))
        {
            $ModPath[] = $path;
        }
        
        #static::initAutoloader();
    }
            
}