<?php

namespace NightOwl;


return array(
     'controllers' => array(
         'invokables' => array(
             'NightOwl\Controller\AuthRest' => 'NightOwl\Controller\AuthRestController',
         ),
     ),
     'router' => array(
         'routes' => array(
             'login' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/login[/:id]',
                     'constraints' => array(
                         'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
                     ),
                     'defaults' => array(
                         'controller' => 'NightOwl\Controller\AuthRest',
                     ),
                 ),
             ),
         ),
     ),
     'view_manager' => array(
         'template_path_stack' => array(
             'login' => __DIR__ . '/../view',
         ),
    )
);