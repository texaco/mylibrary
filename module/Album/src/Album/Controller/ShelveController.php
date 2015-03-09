<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Shelve;
use Album\Form\ShelveForm;
use Zend\View\Model\JsonModel;

class ShelveController extends AbstractActionController {

    protected $shelveTable;
    protected $dataTableService;

    protected $authService;
    protected $aclService;

    private function getAuthService() {
        if (!$this->authService) {
            $sm = $this->getServiceLocator();
            $this->authService = $sm->get('Album\Service\AuthServiceInterface');
        }
        return $this->authService;
    }

    private function getAclService() {
        if (!$this->aclService) {
            $sm = $this->getServiceLocator();
            $this->aclService = $sm->get('Album\Service\AlbumAclServiceInterface');
        }
        return $this->aclService;
    }

    public function getDataTable() {
        if (!$this->dataTableService) {
            $sm = $this->getServiceLocator();
            $this->dataTableService = $sm->get('Album\Service\DataTableInterface');
        }
        return $this->dataTableService;
    }

    public function getShelveTable() {
        if (!$this->shelveTable) {
            $sm = $this->getServiceLocator();
            $this->shelveTable = $sm->get('Album\Model\ShelveTable');
        }
        return $this->shelveTable;
    }

    public function addAction() {
        if ($this->hasPrivilege()) {

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
            $form->get('idUser')->setValue($this->getAuthService()->getIdentity()->id);

            return array('form' => $form);
        }
        return $this->redirect()->toRoute('home', array('action' => 'logout'));
    }

    public function editAction() {
        if ($this->hasPrivilege()) {

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
        return $this->redirect()->toRoute('home', array('action' => 'logout'));
    }

    public function deleteAction() {
        if ($this->hasPrivilege()) {

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
        return $this->redirect()->toRoute('home', array('action' => 'logout'));
    }

    public function indexAction() {
        if ($this->hasPrivilege()) {
            return new ViewModel(array(
                'shelves' => $this->getShelveTable()->fetchAll(),
            ));
        }
        return $this->redirect()->toRoute('home', array('action' => 'logout'));
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
        $table = 'shelve';

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

        $return = $this->getDataTable()->complex($this->params()->fromQuery(), $sql_details, $table, $primaryKey, $columns, null, 'idUser = '.$this->getAuthService()->getIdentity()->id);
        foreach ($return['data'] as &$item) {
            foreach ($item as &$i) {
                $i = utf8_encode($i);
            }
        }
        return new JsonModel($return);
    }

    private function hasPrivilege($resource = 'Shelve') {
        if ($this->getAuthService()->hasIdentity()) {
            //TODO: Need to get the role.
            if ($this->getAclService()->isAllowed($this->getAuthService()->getIdentity()->rol, $resource, null)) {
                return true;
            } else {
                return false;
            }
        }
        return false; // TODO: Set message.
    }

}
