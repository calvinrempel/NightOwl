<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NightOwl\Model;

use NightOwl\Model\Auth;
use MongoClient;
use DateTime;

class Audit
{
    protected $db;

    public function __construct()
    {
        $config = $this->getConfig();
        $dbn = $config['mongo']['name'];
        $m = new MongoClient($config['mongo']['url']);
        $this->db = $m->$dbn;
    }

    public function getLog(array $query)
    {
        $cursor = $this->db->ConsulAudit->find($query);

        $retval = array();

        if($cursor)
        {
            foreach ($cursor as $doc) {
                 $retval[] = $doc;
            }

            return $retval;
        }
        else
        {

            return null;
        }
    }


    // Owner
    // Launch Code
    // Date / Time
    // Message
    public function LogEdit($key, $message, $code )
    {
        $auth = new Auth();
        $data = array('owner'   => $auth->getCurrentUser($key),
                      'code'    => $code,
                      'time'    => date('Y-m-d H:i:s'),
                      'message' => $message);

        return $this->db->ConsulAudit->insert($data)["ok"];
    }


    /**
     * I don't understand how I'm supposed to get this any other way.
     */
    private function getConfig()
    {
        return include __DIR__ . '../../../../../../config/autoload/local.php';
    }
}
