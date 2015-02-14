<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Platform;
use Album\Form\PlatformForm;
use Zend\View\Model\JsonModel;

class PlatformController extends AbstractActionController {

    protected $platformTable;
    protected $dataTableService;

    public function getPlatformTable() {
        if (!$this->platformTable) {
            $sm = $this->getServiceLocator();
            $this->platformTable = $sm->get('Album\Model\PlatformTable');
        }
        return $this->platformTable;
    }

    public function addAction() {
        $form = new PlatformForm();
        $form->get('submit')->setValue('Add');
        $form->get('submitAndContinue')->setValue('Add and Continue');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $platform = new Platform();
            $form->setInputFilter($platform->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $platform->exchangeArray($form->getData());
                $this->getPlatformTable()->savePlatform($platform);

                // Redirect to list of albums
                if ($request->getPost('submit') != null) {
                    return $this->redirect()->toRoute('platform');
                }
            }
        }

        $form = new PlatformForm();
        $form->get('submit')->setValue('Add');
        $form->get('submitAndContinue')->setValue('Add and Continue');
        
        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('platform', array(
                        'action' => 'add'
            ));
        }

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $platform = $this->getPlatformTable()->getPlatform($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('platform', array(
                        'action' => 'index'
            ));
        }

        $form = new PlatformForm();
        $form->bind($platform);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($platform->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getPlatformTable()->savePlatform($platform);

                // Redirect to list of albums
                return $this->redirect()->toRoute('platform');
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
            return $this->redirect()->toRoute('platform');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getPlatformTable()->deletePlatform($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('platform');
        }

        return array(
            'id' => $id,
            'platform' => $this->getPlatformTable()->getPlatform($id)
        );
    }

    public function getDataTable()
    {
        if (!$this->dataTableService) {
            $sm = $this->getServiceLocator();
            $this->dataTableService = $sm->get('Album\Service\DataTableInterface');
        }
        return $this->dataTableService;
    }

    public function indexAjaxAction() {
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
        $table = 'platform';

// Table's primary key
        $primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'name', 'dt' => 1),
            array('db' => 'description', 'dt' => 2),
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
            foreach($item as &$i){
                $i = utf8_encode($i);
            }
        }
        return new JsonModel($return);
    }

    public function indexAction() {
        return new ViewModel(array(
            'platforms' => $this->getPlatformTable()->fetchAll(),
        ));
    }

}
