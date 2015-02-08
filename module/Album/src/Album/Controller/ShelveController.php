<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Shelve;
use Album\Form\ShelveForm;
use Zend\View\Model\JsonModel;

class ShelveController extends AbstractActionController {

    protected $shelveTable;

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
        $json_array = array("draw" => 1,
            "recordsTotal" => 57,
            "recordsFiltered" => 57,
            "data" => array(
                array(
                    "Airi",
                    "Satou",
                    "Accountant",
                    "Tokyo",
                    "28th Nov 08",
                    "$162,700"
                ),
                array(
                    "Angelica",
                    "Ramos",
                    "Chief Executive Officer (CEO)",
                    "London",
                    "9th Oct 09",
                    "$1,200,000"
                ),
                array(
                    "Ashton",
                    "Cox",
                    "Junior Technical Author",
                    "San Francisco",
                    "12th Jan 09",
                    "$86,000"
                ),
                array(
                    "Bradley",
                    "Greer",
                    "Software Engineer",
                    "London",
                    "13th Oct 12",
                    "$132,000"
                ),
                array(
                    "Brenden",
                    "Wagner",
                    "Software Engineer",
                    "San Francisco",
                    "7th Jun 11",
                    "$206,850"
                ),
                array(
                    "Brielle",
                    "Williamson",
                    "Integration Specialist",
                    "New York",
                    "2nd Dec 12",
                    "$372,000"
                ),
                array(
                    "Bruno",
                    "Nash",
                    "Software Engineer",
                    "London",
                    "3rd May 11",
                    "$163,500"
                ),
                array(
                    "Caesar",
                    "Vance",
                    "Pre-Sales Support",
                    "New York",
                    "12th Dec 11",
                    "$106,450"
                ),
                array(
                    "Cara",
                    "Stevens",
                    "Sales Assistant",
                    "New York",
                    "6th Dec 11",
                    "$145,600"
                ),
                array(
                    "Cedric",
                    "Kelly",
                    "Senior Javascript Developer",
                    "Edinburgh",
                    "29th Mar 12",
                    "$433,060"
                ),
        ));


        $result = new JsonModel(array(
            'some_parameter' => 'some value',
            'success' => true,
        ));

        //return new JsonModel($json_array);
        //return $result;

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

// SQL server connection information BORRAR
        $sql_details = array(
            'user' => 'mjrojase_mylibra',
            'pass' => 'wWqw1WqvOujBs5BorhUD',
            'db' => 'mjrojase_mylibrary',
            'host' => 'localhost'
        );
// SQL server connection information BORRAR


        /*         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        require( 'ssp.class.php' );

        echo json_encode(
                SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
        );
    }

    public function testAction() {
        return new ViewModel(array(
            'shelves' => $this->getShelveTable()->fetchAll(),
        ));
    }

}
