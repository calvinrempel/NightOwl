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

class Audit extends BaseModel
{
    /**
     * @date May 3, 2015
     *
     * @author Marc Vouve
     *
     * @param array $query The Query to put to mongo. This is a read only
     * operation, nothing beyond the JSON object is required or will work.
     *
     * @return array an array of the results from Mongo. To give meaningful order
     * The resulting array is sorted by date desending.
     */
    public function getLog(array $query)
    {
        $cursor = $this->getDB()->ConsulAudit->find($query);
        $cursor->sort(array('date' => -1));

        $retval = array();

        if($cursor)
        {
            // transforms the cursor into an array.
            foreach ($cursor as $doc)
            {
                 $retval[] = $doc;
            }

            return $retval;
        }
        else
        {

            return null;
        }
    }


    /**
     *
     * @author Marc Vouve
     *
     * @date   May 3, 2015
     *
     * @param type $message The message to set in mongo.
     * @param type $code    The launch code that is being changed.
     * @return boolean      based in the mongo insertion's okay status.
     */
    public function LogEdit($message, $code)
    {
        $auth = new Auth();
        $data = array('owner'   => $auth->getCurrentUser(),
                      'code'    => $code,
                      'time'    => date('Y-m-d H:i:s'),
                      'message' => $message);

        return (boolean) $this->getDB()->ConsulAudit->insert($data)["ok"];
    }
}
