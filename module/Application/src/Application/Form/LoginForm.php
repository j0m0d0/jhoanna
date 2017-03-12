<?php
namespace Application\Form;
 
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;
 
class LoginForm extends Form
{
    public function __construct($name = null)
     {

        parent::__construct('login');
         $this->setAttribute('method', 'post');
         $this->setAttribute('enctype','multipart/form-data');
         $this->setInputFilter(new LoginFormFilter());
         $this->setHydrator(new ClassMethods());
         
        $this->add(array(
            'name' => 'correo',
            'attributes' => array(
                'type' => 'correo',
                'class' => 'form-control',
            ),
            'attributes' => array(
                 'class' => 'form-control',
                 'placeholder'=>'E-mail',
                 //'autocomplete'=>'off',
             )
        ));
         
         $this->add(array(
            'name' => 'pass',
            'attributes' => array(
                'type' => 'password',
                'class' => 'form-control',
                //'required'=>'input required',
                'placeholder'=>'Password'
            ),
        ));

          
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(    
                'type' => 'submit',
                'value' => 'Entrar',
                'title' => 'Iniciar Sesión',
                'style' => 'width:99px;',
                'class' => 'btn btn-primary'
            ),
        ));
  
     }
}
 
?>