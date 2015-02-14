<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;
use Album\Form\AlbumForm;
use Zend\View\Model\JsonModel;

class AlbumController extends AbstractActionController {

    protected $albumTable;
    protected $shelveTable;
    protected $platformTable;

    public function getDataTable()
    {
        if (!$this->dataTableService) {
            $sm = $this->getServiceLocator();
            $this->dataTableService = $sm->get('Album\Service\DataTableInterface');
        }
        return $this->dataTableService;
    }

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
        
        return new ViewModel();
    }
    
    public function indexAjaxAction(){
                /*
         * DataTables example server-side processing script.
         *
         * Please note that this script is intentionally extremely simply to show how
         * server-side processing can be implemented, and probably shouldn't be used as
         * the basis for a large complex system. It is suitable for simple use cases as
         * for learning.
         *
         * See http://datatables.net/usage/server-side for full details on the server-
         * side processing requirements of DataTables.
         *
         * @license MIT - http://datatables.net/license_mit
         */

        /*         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * Easy set variables
         */

// DB table to use
        $table = 'album';

// Table's primary key
        $primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'cover', 'dt' => 1),
            array('db' => 'title', 'dt' => 2),
            array('db' => 'artist', 'dt' => 3),
            array('db' => 'platform', 'dt' => 4),
            array('db' => 'shelve', 'dt' => 5),
            array('db' => 'seen', 'dt' => 6),
            array('db' => 'registerDate', 'dt' => 7),
            array('db' => 'editDate', 'dt' => 8),
        );

        $config = $this->getServiceLocator()->get('Config');
        
        $sql_details = array(
            'user' => $config['db']['username'],
            'pass' => $config['db']['password'],
            'db' => 'mjrojase_mylibrary',
            'host' => 'localhost'
        );


        /*         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        $return = $this->getDataTable()->simple($this->params()->fromQuery(), $sql_details, $table, $primaryKey, $columns);
        foreach ($return['data'] as &$item){
            foreach ($item as &$i)
            $i = utf8_encode($i);
        }
        return new JsonModel($return);

    }

}
