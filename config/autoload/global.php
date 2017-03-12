<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
     'service_manager' => array(
         'factories' => array(
             'Zend\Db\Adapter\Adapter' =>
                 'Zend\Db\Adapter\AdapterServiceFactory',
         ),
		 'abstract_factories' => array(
		            'Zend\Db\Adapter\AdapterAbstractServiceFactory',
		   ),
     ),
     'db' => array(
         'driver' => 'Pdo',
         //'dsn' => 'mysql:dbname=db_geotest;host=50.62.46.164',
         //'dsn' => 'mysql:dbname=db_jhoanna;host=localhost',
         'dsn' => 'mysql:dbname=u338517275_jho;host=localhost',
         'driver_options' => array(
             PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
         ),	 
     )/*,
     'view_manager' => array(
        'display_exceptions'       => false,
     )*/
 );
