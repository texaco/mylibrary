<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\User;
use Album\Form\UserForm;
use Zend\View\Model\JsonModel;

class UserController extends AbstractActionController {

    protected $userTable;
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

    public function getUserTable() {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Album\Model\UserTable');
        }
        return $this->userTable;
    }

    public function addAction() {
        if ($this->hasPrivilege()) {

            $form = new UserForm();
            $form->get('submit')->setValue('Add');


            $request = $this->getRequest();
            if ($request->isPost()) {
                $user = new User();
                $form->setInputFilter($user->getInputFilter());
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $user->exchangeArray($form->getData());
                    $this->getUserTable()->saveUser($user);

                    if ($request->getPost('submit') != null) {
                        return $this->redirect()->toRoute('user');
                    }
                }
            }

            return array('form' => $form);
        }
        return $this->redirect()->toRoute('home');
    }

    public function editAction() {
        if ($this->hasPrivilege()) {

            $id = (int) $this->params()->fromRoute('id', 0);
            if (!$id) {
                return $this->redirect()->toRoute('user', array(
                            'action' => 'add'
                ));
            }

            // Get the Album with the specified id.  An exception is thrown
            // if it cannot be found, in which case go to the index page.
            try {
                $user = $this->getUserTable()->getUser($id);
            } catch (\Exception $ex) {
                return $this->redirect()->toRoute('user', array(
                            'action' => 'index'
                ));
            }

            $form = new UserForm();
            $form->bind($user);
            $form->get('submit')->setAttribute('value', 'Edit');

            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setInputFilter($user->getInputFilter());
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $this->getUserTable()->saveUser($user);

                    // Redirect to list of albums
                    return $this->redirect()->toRoute('user');
                }
            }

            return array(
                'id' => $id,
                'form' => $form,
            );
        }
        return $this->redirect()->toRoute('home');
    }

    public function deleteAction() {
        if ($this->hasPrivilege()) {

            $id = (int) $this->params()->fromRoute('id', 0);
            if (!$id) {
                return $this->redirect()->toRoute('user');
            }

            $request = $this->getRequest();
            if ($request->isPost()) {
                $del = $request->getPost('del', 'No');

                if ($del == 'Yes') {
                    $id = (int) $request->getPost('id');
                    $this->getUserTable()->deleteUser($id);
                }

                // Redirect to list of albums
                return $this->redirect()->toRoute('user');
            }

            return array(
                'id' => $id,
                'user' => $this->getUserTable()->getUser($id)
            );
        }
        return $this->redirect()->toRoute('home');
    }

    public function indexAction() {
        if ($this->hasPrivilege()) {
            return new ViewModel();
        }
        // TODO: Redirect to information page.
        return $this->redirect()->toRoute('home');
    }

    private function hasPrivilege($resource = 'User') {
        if ($this->getAuthService()->hasIdentity()) {
            \Zend\Debug\Debug::dump($this->getAuthService()->getIdentity());
            //TODO: Need to get the role.
            if ($this->getAclService()->isAllowed($this->getAuthService()->getIdentity()->rol, $resource, null)) {
                \Zend\Debug\Debug::dump('Granted access');
                return true;
            } else {
                \Zend\Debug\Debug::dump('Not enough privilege');
                return false;
            }
        }
        \Zend\Debug\Debug::dump('No Identity found');
        return false; // TODO: Set message.
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
        $table = 'user';

// Table's primary key
        $primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'email', 'dt' => 1),
            array('db' => 'pass', 'dt' => 2),
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
        foreach ($return['data'] as &$item) {
            foreach ($item as &$i)
                $i = utf8_encode($i);
        }
        return new JsonModel($return);
    }

}
