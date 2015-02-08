<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;
use Album\Form\AlbumForm;
use Album\Form\AlbumSearchForm;

class AlbumController extends AbstractActionController {

    protected $albumTable;
    protected $shelveTable;
    protected $platformTable;

    private function get_array_unique_search($albums){
        $titles = array();
        $artists = array();
        $seens = array();

        foreach($albums as $album){
            $titles[] = $album->title;
            $artists[] = $album->artist;
            $seens[] = $album->seen;
        }
        return array('titles' => array_unique($titles), 'artists' => array_unique($artists), 'seens' => array_unique($seens),);
    }
    
    public function getAlbumTable() {
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }

    public function getShelveTable() {
        if (!$this->shelveTable) {
            $sm = $this->getServiceLocator();
            $this->shelveTable = $sm->get('Album\Model\ShelveTable');
        }
        return $this->shelveTable;
    }

    public function getPlatformTable() {
        if (!$this->platformTable) {
            $sm = $this->getServiceLocator();
            $this->platformTable = $sm->get('Album\Model\PlatformTable');
        }
        return $this->platformTable;
    }

    public function getShelveOptions() {
        $shelves = $this->getShelveTable()->fetchAll();
        $shelveOptions = array();

        foreach ($shelves as $shelve) {
            $shelveOptions[$shelve->name] = $shelve->name; // TODO: Habrá que poner los ids, pero actualmente nos vale con poblar la columna con los nombres.
        }
        return $shelveOptions;
    }

    public function getPlatformOptions() {
        $platforms = $this->getPlatformTable()->fetchAll();
        $platformOptions = array();

        foreach ($platforms as $platform) {
            $platformOptions[$platform->name] = $platform->name; // TODO: Habrá que poner los ids, pero actualmente nos vale con poblar la columna con los nombres.
        }
        return $platformOptions;
    }

    public function addAction() {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');
        $form->get('submitAndContinue')->setValue('Add and Continue');


        $form->get('shelve')->setValueOptions($this->getShelveOptions());
        $form->get('platform')->setValueOptions($this->getPlatformOptions());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Album();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                $this->getAlbumTable()->saveAlbum($album);

                if ($request->getPost('submit') != null) {
                    return $this->redirect()->toRoute('album');
                }
            }
        }

        //The form is reset in order to be empty
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');
        $form->get('submitAndContinue')->setValue('Add and Continue');
        $form->get('shelve')->setValueOptions($this->getShelveOptions());
        $form->get('platform')->setValueOptions($this->getPlatformOptions());

        $albums = $this->getAlbumTable()->fetchAll();
        $search_array = $this->get_array_unique_search($albums);
        
        return array('form' => $form,
            'search_array' => $search_array,
            );
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album', array(
                        'action' => 'add'
            ));
        }

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $album = $this->getAlbumTable()->getAlbum($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('album', array(
                        'action' => 'index'
            ));
        }

        $form = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');
        $form->get('shelve')->setValueOptions($this->getShelveOptions());
        $form->get('platform')->setValueOptions($this->getPlatformOptions());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getAlbumTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }

        $albums = $this->getAlbumTable()->fetchAll();
        $search_array = $this->get_array_unique_search($albums);

        return array(
            'id' => $id,
            'form' => $form,
            'search_array' => $search_array,
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deleteAlbum($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }

        return array(
            'id' => $id,
            'album' => $this->getAlbumTable()->getAlbum($id)
        );
    }

    public function indexAction() {
        $form = new AlbumSearchForm();
        $form->get('submit')->setValue('Search');


        $form->get('shelve')->setEmptyOption('- No Selected -');
        $form->get('shelve')->setValueOptions($this->getShelveOptions());
        $form->get('platform')->setEmptyOption('- No Selected -');
        $form->get('platform')->setValueOptions($this->getPlatformOptions());

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setData($request->getPost());
            $albums = $this->getAlbumTable()->getAlbums(
                    $form->get('title')->getValue(), $form->get('artist')->getValue(), $form->get('platform')->getValue(), $form->get('shelve')->getValue(), $form->get('seen')->getValue());
        }

        if (!isset($albums)) {
            $albums = $this->getAlbumTable()->fetchAll();
        }

        $albums->buffer();
        
        $search_array = $this->get_array_unique_search($albums);
        
        return new ViewModel(array(
            'albums' => $albums,
            'form' => $form,
            'search_array' => $search_array,
        ));
    }

}
