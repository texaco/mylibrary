<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Album\Form;

use Zend\Form\Form;

class UserForm extends Form {

    public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct('user');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'options' => array(
                'label' => 'Email',
            ),
            'attributes' => array(
                'id' => 'email',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'pass',
            'type' => 'Password',
            'options' => array(
                'label' => 'Password',
            ),
            'attributes' => array(
                'id' => 'pass',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'rol',
            'type' => 'Select',
            'options' => array(
                'label' => 'Rol',
                'value_options' => array(
                    'Guest' => 'Guest',
                    'User' => 'User',
                    'Admin' => 'Admin',
                ),
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'btn btn-default',
            ),
        ));

    }
}
