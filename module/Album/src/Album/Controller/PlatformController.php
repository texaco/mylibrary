<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Platform;
use Album\Form\PlatformForm;

class PlatformController extends AbstractActionController {

    protected $platformTable;

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

    public function indexAction() {
        return new ViewModel(array(
            'platforms' => $this->getPlatformTable()->fetchAll(),
        ));
    }

}
