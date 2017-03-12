<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Validator;
use Zend\I18n\Validator as I18nValidator;
use Zend\Db\Adapter\Adapter;
use Zend\Crypt\Password\Bcrypt;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
 
//Componentes de autenticación
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Session\Container;
 
//Incluir modelos
use Application\Model\UsuarioPersonalEntity;
use Application\Model\LogLoginEntity;
 
//Incluir formularios
use Application\Form\LoginForm;

 
class loginController extends AbstractActionController{
    private $dbAdapter;
    private $auth;
     
    public function __construct() {
        //Cargamos el servicio de autenticación en el constructor
        $this->auth = new AuthenticationService();
    }

    public function indexAction(){
      $auth = $this->auth;
    	$message = array("type" => "danger", "msg" => "");
      $notif = "";
    	$form = new LoginForm();
      $usuario = new UsuarioPersonalEntity();
		  $form->bind($usuario);
      
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
              $authAdapter = new AuthAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
              $authAdapter->setTableName('usuario_personal')
                ->setIdentityColumn('correo')
                ->setCredentialColumn('password')
                ->setIdentity($this->params()->fromPost('correo'))
                ->setCredential($this->params()->fromPost('pass'));
              //Le decimos al servicio de autenticación que el adaptador
              $auth->setAdapter($authAdapter);
              //Le decimos al servicio de autenticación que lleve a cabo la identificacion
              $result = $auth->authenticate();
              //Si el resultado del login es falso, es decir no son correctas las credenciales
              if($authAdapter->getResultRowObject()==null){
                //Crea un mensaje flash y redirige
                $message = array("type" => "danger", "msg" => "El correo electronico o la contraseña que introduciste no es correcta, por favor intentalo de nuevo.");
                //print_r($message);
              }else{
                 // Le decimos al servicio que guarde en una sesión
                 // el resultado del login cuando es correcto
                $auth->getStorage()->write($authAdapter->getResultRowObject());
                $is_login = $this->getMapper("UsuarioPersonalMapper")->getByCorreo( $this->params()->fromPost('correo') );

                if($is_login->getIsBaja() != 1){
                  $entity = new LogLoginEntity();
                  $entity->setType( "login" );
                  $entity->setUserName( $this->params()->fromPost('correo') );
                  $entity->setUserId( $is_login->getId() );
                  $entity->setFecha( date("Y-m-d H:i:s") );
                  $this->getMapper("LogLoginMapper")->save( $entity );                  
                  //Nos redirige a una pagina interior
                  return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/');
                }else{
                  $message = array("type" => "danger", "msg" => "El correo electronico o la contraseña que introduciste no es correcta, por favor intentalo de nuevo.");
                }
              }

            }
        }

        $this->layout('layout/login');
        return array(
                    	'form'=>$form,
                      'message'=>$message,
                      'notif'=>$notif,
                  	);

    }
     
    public function cerrarAction()
    {
        $identi = $this->auth->getStorage()->read();        
        if(isset($identi)){
            $id = $identi->id;
            $correo = $identi->correo;
        }else{
            $id = $this->params('id');
            $is_login = $this->getMapper("UsuarioPersonalMapper")->getById( $id );
            $correo = $is_login->getCorreo();
        }

        $entity = new LogLoginEntity();
        $entity->setType( "logout" );
        $entity->setUserName( $correo );
        $entity->setUserId( $id );
        $entity->setFecha( date("Y-m-d H:i:s") );
        $this->getMapper("LogLoginMapper")->save( $entity ); 
        //Cerramos la sesión borrando los datos de la sesión.
        $this->auth->clearIdentity();
        return $this->redirect()->toRoute('login');
    }

    public function getMapper($mapper)
    {
        $sm = $this->getServiceLocator();
        return $sm->get($mapper);
    }

}