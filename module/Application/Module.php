<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;
//Mappers
use Application\Model\DataMediaMapper;
use Application\Model\RolMapper;
use Application\Model\UsuarioPersonalMapper;
use Application\Model\ModuloMapper;
use Application\Model\AreaUsuarioMapper;
use Application\Model\ModuloAccesoMapper;
use Application\Model\PuestoMapper;
use Application\Model\LogMapper;
use Application\Model\LogLoginMapper;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $translator = new \Zend\I18n\Translator\Translator();
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator->addTranslationFile(
            'phpArray',
            './vendor/zendframework/zendframework/resources/languages/es/Zend_Validate.php',
            'default',
            'es_ES'
        );
        //AbstractValidator::setDefaultTranslator($translator);
        \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
        date_default_timezone_set("America/Mexico_City");

        /* ACL Module*/
        $this->initAcl($e);
        $route = $e->getApplication()->getEventManager()->attach('route', array($this, 'checkAcl'));

        $auth   = new \Zend\Authentication\AuthenticationService();
        $identi = $auth->getStorage()->read();

        if ($identi != null) {
            $viewModel            = $e->getApplication()->getMvcEvent()->getViewModel();
            $viewModel->identi    = $auth->getStorage()->read();
            $viewModel->roles     = $this->getDbRoles($e);
            //$viewModel->datauser = $this->getDataUser($e);
        }
    }

    /* ACL- functions*/
    public function getDbRoles(MvcEvent $e)
    {
        // I take it that your adapter is already configured
        $dbAdapter     = $e->getApplication()->getServiceManager()->get('Zend\Db\Adapter\Adapter');
        $ModuloMapper  = new ModuloMapper($dbAdapter);
        $UsuarioMapper = new UsuarioPersonalMapper($dbAdapter);
        $accesos = $UsuarioMapper->fetchAllAcceso();
        $modulos = $ModuloMapper->fetchAllArr();
        return $accesos;
    }

    public function initAcl(MvcEvent $e)
    {
        //Creamos el objeto ACL
        $acl = new \Zend\Permissions\Acl\Acl();
        //Incluimos la lista de roles y permisos, nos devuelve un array
        //$roles=require_once 'config/autoload/acl.roles.php';
        $roles = $this->getDbRoles($e);
        foreach ($roles as $role => $resources) {
            //Indicamos que el rol será genérico
            $role = new \Zend\Permissions\Acl\Role\GenericRole($role);
            //Añadimos el rol al ACL
            $acl->addRole($role);
            //Recorremos los recursos o rutas permitidas
            foreach ($resources["allow"] as $resource) {
                //Si el recurso no existe lo añadimos
                if (!$acl->hasResource($resource)) {
                    $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
                }
                //Permitimos a ese rol ese recurso
                $acl->allow($role, $resource);
            }
            /*foreach ($resources["deny"] as $resource) {
        //Si el recurso no existe lo añadimos
        if(!$acl->hasResource($resource)){
        $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
        }
        //Denegamos a ese rol ese recurso
        $acl->deny($role, $resource);
        }*/
        }
        //Establecemos la lista de control de acceso
        $e->getViewModel()->acl = $acl;
    }

    public function checkAcl(MvcEvent $e)
    {
        //guardamos el nombre de la ruta o recurso a permitir o denegar
        $route = $e->getRouteMatch()->getMatchedRouteName();
        //Instanciamos el servicio de autenticacion
        $auth   = new \Zend\Authentication\AuthenticationService();
        $identi = $auth->getStorage()->read();
        //Identi is not null
        if ($identi != null) {
            foreach ($identi as $key => $value) {
                if ($key == 'rol_id') {
                    $userRole = $value;
                }
            }
        } else {
            $sm = $e->getApplication()->getServiceManager();
            $router       = $sm->get('router');
            $request      = $sm->get('request');
            $matchedRoute = $router->match($request);
            $params = $matchedRoute->getParams();
            $ruta   = $e->getRouteMatch()->getMatchedRouteName();
            $publicos = array('login');
            if (!in_array($ruta, $publicos)) {
                $url      = $e->getRouter()->assemble(array('action' => 'index'), array('name' => 'login'));
                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();
            }
        }
        //Comprobamos si no está permitido para ese rol esa ruta
        if ($identi != null) {
            if ($e->getViewModel()->acl->hasResource($route) && !$e->getViewModel()->acl->isAllowed($userRole, $route)) {
                //Devolvemos un error 404
                $response = $e->getResponse();
                //location to page or what ever
                //return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/');
                $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/');
                //$response -> getHeaders() -> addHeaderLine('Location', $e -> getRequest() -> getBaseUrl() . '/404');
                $response->setStatusCode(303);
            }
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'DataMediaMapper'                       => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper    = new DataMediaMapper($dbAdapter);
                    return $mapper;
                },
                'UsuarioPersonalMapper'            => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper    = new UsuarioPersonalMapper($dbAdapter);
                    return $mapper;
                },
                'RolMapper'                        => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper    = new RolMapper($dbAdapter);
                    return $mapper;
                },
                'ModuloMapper'                     => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper    = new ModuloMapper($dbAdapter);
                    return $mapper;
                },
                'AreaUsuarioMapper'                => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper    = new AreaUsuarioMapper($dbAdapter);
                    return $mapper;
                },
                'ModuloAccesoMapper'               => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper    = new ModuloAccesoMapper($dbAdapter);
                    return $mapper;
                },
                'PuestoMapper'                     => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper    = new PuestoMapper($dbAdapter);
                    return $mapper;
                },
                'LogMapper'                   => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper    = new LogMapper($dbAdapter);
                    return $mapper;
                },
                'LogLoginMapper'                   => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper    = new LogLoginMapper($dbAdapter);
                    return $mapper;
                },
                'GlobalsFunctionHelper'            => function ($sm) {
                    return new Helper\GlobalsFunctionHelper;
                },
            ),
        );
    }
}
