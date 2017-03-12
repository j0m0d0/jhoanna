<?php
 namespace Application\Form;

 use Zend\InputFilter\InputFilter;

 class LoginFormFilter extends InputFilter
 {
     public function __construct()
     {
        $this->add(array(
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
             'name' => 'pass',
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
        
     }
 }