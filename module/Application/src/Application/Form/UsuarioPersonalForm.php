<?php
namespace Application\Form;
 
use Zend\Form\Element;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;
 
class UsuarioPersonalForm extends Form
{
    public function __construct($name = null)
     {

        parent::__construct('admin');
         $this->setAttribute('method', 'post');
         $this->setAttribute('enctype','multipart/form-data');
         //$this->setInputFilter(new UsuarioPersonalFormFilter());
         $this->setHydrator(new ClassMethods());
         
        $this->add(array(
             'name' => 'nombre',
             'type' => 'text',
             'options' => array(
                 'label' => 'Nombre: *',
             ),
             'attributes' => array(
                 'id' => 'nombre',
                 'class' => 'form-control',
                 //'required'=>'input required',
             )
         ));
         
        $this->add(array(
            'name' => 'apaterno',
            'type' => 'text',
            'options' => array(
                'label' => 'Apellido Paterno : *',
            ),
            'attributes' => array(
                'id' => 'apaterno',
                'class' => 'form-control',
                //'required'=>'input required',
            ),
        ));

        $this->add(array(
            'name' => 'amaterno',
            'type' => 'text',
            'options' => array(
                'label' => 'Apellido Materno : *',
            ),
            'attributes' => array(
                'id' => 'amaterno',
                'class' => 'form-control',
                //'required'=>'input required',
            ),
        ));

        /*$this->add(array(
            'name' => 'apellidos',
            'type' => 'text',
            'options' => array(
                'label' => 'Apellidos : *',
            ),
            'attributes' => array(
                'id' => 'apellidos',
                'class' => 'form-control',
                //'required'=>'input required',
            ),
        ));*/

        $this->add(array(
            'name' => 'direccion',
            'type' => 'text',
            'options' => array(
                'label' => 'Dirección :',
            ),
            'attributes' => array(
                'id' => 'direccion',
                'class' => 'form-control',
                //'required'=>'input required',
            ),
        ));

        $this->add(array(
            'name' => 'telefono',
            'type' => 'text',
            'options' => array(
                'label' => 'Telefono :',
            ),
            'attributes' => array(
                'id' => 'telefono',
                'class' => 'form-control',
                //'required'=>'input required',
            ),
        ));

        $this->add(array(
            'name' => 'correo',
            'type' => 'email',
            'options' => array(
                'label' => 'Correo : *',
            ),
            'attributes' => array(
                'id' => 'correo',
                'class' => 'form-control',
                'required'=>'input required',
            ),
        ));

        
         
/*
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
*/
        /*$this->add(array( 
            'name' => 'correo', 
            'type' => 'email', 

            'attributes' => array( 
                'class' => 'form-control form-group',
                'id' => 'correo',
                //'maxlength' => 50, 
                //'placeholder' => 'ingrese Correo Electrónico', 
                'required' => 'required', 
                ), 
            'options' => array( 
                'label' => 'Correo : *', 
                ), 
            )); */

        /*$this->add(array(
            'name' => 'laboratorio',
            'type' => 'select',
            'options' => array(
                'label' => 'Laboratorio:',
                'value_options' => array(
                    'LABORATORIO CENTRAL XALAPA' => 'LABORATORIO CENTRAL XALAPA',
                    'LABORATORIO SATELITE' => 'LABORATORIO SATELITE',
                    'LABORATORIO VERACRUZ' => 'LABORATORIO VERACRUZ',
                ),
            ),
            'attributes' => array(
                'id' => 'laboratorio',
                'class' => 'form-control',
            )
        ));*/

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'options' => array(
                'label' => 'Contraseña : *',
            ),
            'attributes' => array(
                'id' => 'password',
                'class' => 'form-control',
                'required'=>'input required',
            ),
        ));

        $this->add(array(
            'name' => 'passwordCheck',
            'type' => 'password',
            'options' => array(
                'label' => 'Repite la contraseña : *',
            ),
            'attributes' => array(
                'id' => 'passwordCheck',
                'class' => 'form-control',
                'required'=>'input required',
            ),
        ));

        $this->add(array(
            'name' => 'nivel',
            'type' => 'text',
            'options' => array(
                'label' => 'Nivel : *',
            ),
            'attributes' => array(
                'id' => 'nivel',
                'class' => 'form-control',
                //'required'=>'input required',
            ),
        ));
          
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(    
                'type' => 'submit',
                //'value' => 'Crear Usuario',
                //'title' => 'Guardar Usuario',
                'class' => 'btn btn-primary'
            ),
        ));

  
     }
}
 
?>