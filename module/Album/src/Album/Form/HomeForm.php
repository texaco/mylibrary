<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Album\Form;

use Zend\Form\Form;

class HomeForm extends Form {

    public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct('home');

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
            'type' => 'Text',
            'options' => array(
                'label' => 'Password',
            ),
            'attributes' => array(
                'id' => 'pass',
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
