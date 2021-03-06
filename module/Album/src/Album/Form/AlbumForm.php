<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Album\Form;

use Zend\Form\Form;

class AlbumForm extends Form {

    public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct('album');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title',
            ),
            'attributes' => array(
                'id' => 'title',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'artist',
            'type' => 'Text',
            'options' => array(
                'label' => 'Artist',
            ),
            'attributes' => array(
                'id' => 'artist',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'platform',
            'type' => 'Select',
            'options' => array(
                'label' => 'Platform',
            ),
            'attributes' => array(
                'style' => 'display: none',
                'class' => 'form-control selectpicker',
                'data-live-search' => 'true',
            ),
        ));
        $this->add(array(
            'name' => 'shelve',
            'type' => 'Select',
            'options' => array(
                'label' => 'Shelve',
            ),
            'attributes' => array(
                'style' => 'display: none',
                'class' => 'form-control selectpicker',
                'data-live-search' => 'true',
            ),
        ));
        $this->add(array(
            'name' => 'cover',
            'type' => 'Text',
            'options' => array(
                'label' => 'Cover',
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'seen',
            'type' => 'Text',
            'options' => array(
                'label' => 'Seen',
            ),
            'attributes' => array(
                'id' => 'seen',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'idUser',
            'type' => 'Hidden',
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

        $this->add(array(
            'name' => 'submitAndContinue',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save and Continue',
                'id' => 'submitAndContinueButton',
                'class' => 'btn btn-default',
            ),
        ));
    }

}