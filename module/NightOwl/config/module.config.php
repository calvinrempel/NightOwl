<?php

namespace NightOwl;

return array(
     'controllers' => array(
         'invokables' => array(
             'NightOwl\Controller\AuthRest' => 'NightOwl\Controller\AuthRestController',
             'NightOwl\Controller\LaunchCodes' => 'NightOwl\Controller\LaunchCodesController',
             'NightOwl\Controller\Audit' => 'NightOwl\Controller\AuditController'
         ),
     ),
     'router' => array(
         'routes' => array(
             'login' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/auth/:method[/:id][/:pw]',
                     'defaults' => array(
                         'controller' => 'NightOwl\Controller\AuthRest',
                     ),
                 ),
             ),
             'codes' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route' =>  '/codes[/:token][/:seg1][/:seg2][/:seg3][/:seg4]',
                     'defaults' => array(
                         'controller' => 'NightOwl\Controller\LaunchCodes',
                     ),
                 ),
            ),
            'audit' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/audit[/:token][/:query]',
                    'defaults' => array(
                        'controller' => 'NightOwl\Controller\Audit',
                    )
                ),
            ),
         ),
     ),
     'view_manager' => array( 
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'strategies' => array(
           'ViewJsonStrategy',
        ),
    ),
);
