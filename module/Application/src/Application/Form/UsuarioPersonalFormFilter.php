<?php
 namespace Application\Form;

 use Zend\InputFilter\InputFilter;

 class UsuarioPersonalFormFilter extends InputFilter
 {
     public function __construct()
     {
        

        $this->add(array(
             'name' => 'nombre',
             'required' => true,
             'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty',
                    ),
                ),
        ));

        $this->add(array(
             'name' => 'apaterno',
             'required' => true,
             'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty',
                    ),
                ),
        ));  

        $this->add(array(
             'name' => 'amaterno',
             'required' => true,
             'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty',
                    ),
                ),
        ));

        /*$this->add(array(
             'name' => 'correo',
             'required' => true,
             'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty',
                    ),
                     array(
                      'name' =>'EmailAddress',
                    ),
                ),
        ));*/


        /*$this->add(array(
             'name' => 'correo',
             'required' => true,
             'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
             'validators' => array( 
                array ( 
                    'name' => 'StringLength', 
                    'options' => array( 
                        'encoding' => 'UTF-8',  
                    ), 
                ), 
                array ( 
                    'name' => 'EmailAddress', 
                    'options' => array( 
                        'messages' => array( 
                            'emailAddressInvalidFormat' => 'Correo Electrónico No Valido', 
                        ) 
                    ), 
                ), 
                array ( 
                    'name' => 'NotEmpty', 
                    'options' => array( 
                        'messages' => array( 
                            'isEmpty' => 'Ingrese un Correo Electrónico Por favor', 
                        ) 
                    ), 
                ), 
            ), 
        ));*/

        /*$this->add(array(
             'name' => 'correo',
             'required' => true,
             'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
             'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                    ),
                     array(
                      'name' =>'EmailAddress',
                    ),
                ),
        ));

        $this->add(array(
             'name' => 'password',
             'required' => true,
             'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty',
                    ),
                ),
        ));

        $this->add(array(
            'name' => 'passwordCheck', // add second password field
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password', // name of first password field
                    ),
                ),
            ),
        ));*/


        /*$this->add(array(
             'name' => 'nivel',
             'required' => true,
             'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty',
                    ),
                ),
        ));*/
     }
 }