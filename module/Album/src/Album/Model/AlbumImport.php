<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Album\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class AlbumImport {

    public $albumsToImport;         // ARTIST/AUTHOR OR DEVELOPER
    
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->albumsToImport = (!empty($data['albumsToImport'])) ? $data['albumsToImport'] : null;
    }

    // Serilize the object. It is neede to transfer params for edition action.
    public function getArrayCopy() {
        return get_object_vars($this);
    }

    // Métodos para la validación del formulario:
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'albumsToImport',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 10000,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

    return $this->inputFilter;
    }

}
