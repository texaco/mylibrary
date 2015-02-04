<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Shelve;
use Album\Form\ShelveForm;

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

}
