<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Album\Form;

use Zend\Form\Form;

class AlbumImportForm extends Form {

    public function __construct($name = null) {
        // we want to ignore the name passed
        parent::__construct('album_import');

        $this->add(array(
            'name' => 'albumsToImport',
            'type' => 'Textarea',
            'options' => array(
                'label' => 'Albums To Import',
            ),
            'attributes' => array(
                'id' => 'AlbumsToImport',
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
