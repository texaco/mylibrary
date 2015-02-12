<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Shelve;
use Album\Form\ShelveForm;
use Zend\View\Model\JsonModel;
use Album\Service\DataTableInteface;

class ShelveController extends AbstractActionController {

    protected $shelveTable;
    protected $dataTableService;

    public function getShelveTable() {
        if (!$this->shelveTable) {
            $sm = $this->getServiceLocator();
            $this->shelveTable = $sm->get('Album\Model\ShelveTable');
        }
        return $this->shelveTable;
    }

    public function addAction() {
        $form = new ShelveForm();
        $form->get('submit')->setValue('Add');
        $form->get('submitAndContinue')->setValue('Add and Continue');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $shelve = new Shelve();
            $form->setInputFilter($shelve->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $shelve->exchangeArray($form->getData());
                $this->getShelveTable()->saveShelve($shelve);

                // Redirect to list of albums
                if ($request->getPost('submit') != null) {
                    return $this->redirect()->toRoute('shelve');
                }
            }
        }
        $form = new ShelveForm();
        $form->get('submit')->setValue('Add');
        $form->get('submitAndContinue')->setValue('Add and Continue');

        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('shelve', array(
                        'action' => 'add'
            ));
        }

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $shelve = $this->getShelveTable()->getShelve($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('shelve', array(
                        'action' => 'index'
            ));
        }

        $form = new ShelveForm();
        $form->bind($shelve);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($shelve->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getShelveTable()->saveShelve($shelve);

                // Redirect to list of albums
                return $this->redirect()->toRoute('shelve');
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
            return $this->redirect()->toRoute('selve');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getShelveTable()->deleteShelve($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('shelve');
        }

        return array(
            'id' => $id,
            'shelve' => $this->getShelveTable()->getShelve($id)
        );
    }

    public function indexAction() {
        return new ViewModel(array(
            'shelves' => $this->getShelveTable()->fetchAll(),
        ));
    }

    public function indextestAction() {
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
            array('db' => 'title', 'dt' => 0),
            array('db' => 'artist', 'dt' => 1),
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
            $item[0] = utf8_encode($item[0]);
            $item[1] = utf8_encode($item[1]);
        }
        return new JsonModel($return);
    }

    public function testAction() {
        return new ViewModel(array(
            'shelves' => $this->getShelveTable()->fetchAll(),
        ));
    }

    public function getDataTable()
    {
        if (!$this->dataTableService) {
            $sm = $this->getServiceLocator();
            $this->dataTableService = $sm->get('Album\Service\DataTableInterface');
        }
        return $this->dataTableService;
    }
}
