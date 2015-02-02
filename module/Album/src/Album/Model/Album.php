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

class Album {

    public $id;
    public $artist;         // ARTIST/AUTHOR OR DEVELOPER
    public $title;          // TITLE
    public $platform;       // PS3, XBOX360, AUDIOCD, BLUERAY, DIGITAL...
    public $shelve;         // SHELVE, STEAM, PSN, XBOXMARKETPLACE...
    public $cover;          // TODO: IMAGE PNG, JPG...
    public $seen;           // ALREADY SEEN OR PENDING.
    public $registerDate;   // REGISTER DATE
    public $editDate;       // EDITED DATE
    
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->artist = (!empty($data['artist'])) ? $data['artist'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->platform = (!empty($data['platform'])) ? $data['platform'] : null;
        $this->shelve = (!empty($data['shelve'])) ? $data['shelve'] : null;
        $this->cover = (!empty($data['cover'])) ? $data['cover'] : null;
        $this->seen = (!empty($data['seen'])) ? $data['seen'] : null;
        $this->registerDate = (!empty($data['registerDate'])) ? $data['registerDate'] : null;
        $this->editDate = (!empty($data['editDate'])) ? $data['editDate'] : null;
    }

    // Hace serializable el objeto. Necesario para copiar los parametros desde la edición.
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
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'artist',
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
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'title',
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
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            // TO THE PLATFORM, I WOULD LIKE TO RESTRICT TO A FEW VALUES AS PS3, XBOX360...
            // IT CAN BE TAKEN FROM DATABASE, BUT FOR NOW IT CAN BE A STRING
            // OR IN A FIXED LIST.
            $inputFilter->add(array(
                'name' => 'platform',
                'required' => false,
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
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            // TO THE SHELVE, I WOULD LIKE TO RESTRICT TO A FEW VALUES AS STEAM, PSN, HOMESHELVE...
            // IT CAN BE TAKEN FROM DATABASE, BUT FOR NOW IT CAN BE A STRING
            // OR IN A FIXED LIST.
            $inputFilter->add(array(
                'name' => 'shelve',
                'required' => false,
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
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            // TO THE COVER, I WOULD LIKE STORE A IMAGE PATH. I ALSO HAVE TO RENAME THE IMAGE FILE AND MANAGE IT.
            $inputFilter->add(array(
                'name' => 'cover',
                'required' => false,
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
                            'max' => 2048, // IT SEEMS TO BE THE URL LENGTH LIMIT
                        ),
                    ),
                ),
            ));

            // TO THE SEEN, IS A BOOLEAN.
            // IN A FIRST TERM I WOULD PUT IT AS STRING, BUT IT WILL BE A BOOLEAN.
            $inputFilter->add(array(
                'name' => 'seen',
                'required' => false,
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
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

    return $this->inputFilter;
    }

}
