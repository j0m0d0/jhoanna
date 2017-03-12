<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;
//Entity
use Application\Model\DataMediaEntity;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
//Autentificación de usuario cargado
use Zend\Authentication\AuthenticationService;

class IndexController extends AbstractActionController
{

    private $auth;
    public function __construct()
    {
        //Cargamos el servicio de autenticación en el constructor
        $this->auth = new AuthenticationService();
    }

    public function indexAction()
    {    
        $auth = $this->auth;
        $identi=$auth->getStorage()->read();
        // echo "<pre>";
        // print_r($identi);
        // echo "</pre>";
        return array();
    }

    public function getMapper($mapper)
    {
        $sm = $this->getServiceLocator();
        return $sm->get($mapper);
    }
    public function getHelper($helper){

        return $this->getServiceLocator()->get($helper);
    }
}
