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

class IndexController extends AbstractActionController
{
    public function indexAction()
    {    

    	$request = $this->getRequest();
    	if ($request->isPost()) {
	    	$msg       = "";
	    	$File      = $this->params()->fromFiles('file');
	    	$ruta_save = "/images/data_media";
	    	$result = $this->getHelper("GlobalsFunctionHelper")->uploadFile($File, $ruta_save, $type = null);
	        if ($result != "") {
	            $DataMediaEntity = new DataMediaEntity();
	            $DataMediaEntity->setPath($result);
	            date_default_timezone_set("America/Mexico_City");
	            $DataMediaEntity->setFecha(date("Y-m-d H:i:s") );
	            $DataMediaEntity->setUsuarioId( "Usuario Default" );
	            $this->getMapper("DataMediaMapper")->save($DataMediaEntity);
	        }
    	}

    	$DataMedia   = $this->getMapper("DataMediaMapper")->fetchAllArray();
        return array( 
            "DataMedia"=>$DataMedia            
        );
    }

    public function savedataAction()
    {
    	$msg       = "";
    	$msg = "no guardo";
	    $File      = $this->params()->fromFiles('file');
	    $ruta_save = "/images/data_media";
	    $result = $this->getHelper("GlobalsFunctionHelper")->uploadFile($File, $ruta_save, $type = null);
	        if ($result != "") {
	            $DataMediaEntity = new DataMediaEntity();
	            $DataMediaEntity->setPath($result);
	            date_default_timezone_set("America/Mexico_City");
	            $DataMediaEntity->setFecha(date("Y-m-d H:i:s") );
	            $DataMediaEntity->setUsuarioId( "Usuario Default" );
	            $this->getMapper("DataMediaMapper")->save($DataMediaEntity);
	            $msg = "si guardo";
	        }

	    header('Content-Type: application/json');
        echo json_encode($msg); 
        exit(0);
        //$this->layout('layout/blank');
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
