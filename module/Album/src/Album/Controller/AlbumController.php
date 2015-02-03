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

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }


        return array('form' => $form, 'shelves' => $shelves);
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

        return array(
            'id' => $id,
            'form' => $form,
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


        $form->get('shelve')->setValueOptions($this->getShelveOptions());
        $form->get('platform')->setValueOptions($this->getPlatformOptions());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $albums = $this->getAlbumTable()->getAlbums('infamous', 'test');
            $form->get('submit')->setValue('Searched');

            $album = new Album();
            //$form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                // $this->getAlbumTable()->saveAlbum($album);
                // Redirect to list of albums
                // return $this->redirect()->toRoute('album');
            }
        }

        if (!$albums) {
            $albums = $this->getAlbumTable()->fetchAll();
        }
        
        return new ViewModel(array(
            'albums' => $albums,
            'form' => $form,
        ));
    }

}
