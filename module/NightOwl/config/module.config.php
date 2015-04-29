<?php
return array(
	'controllers' => array(
		'invokables' => array(
		'NightOwl\Controller\NightOwl' => 'NightOwl\Controller\Codes',
		),
	),
	'router' => array(
         'routes' => array(
             'nightowl' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/nightowl[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Codes',
						 '__NAMESPACE__' => 'NightOwl',
                     ),
                 ),
             ),
         ),
     ),
	'view_manager' => array(
		'template_path_stack' => array(
		'album' => __DIR__ . '/../view',
		),
	),
);