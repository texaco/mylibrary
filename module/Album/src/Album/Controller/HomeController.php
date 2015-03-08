<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Album\Model\User;
use \Zend\View\Model\ViewModel;
use Album\Form\HomeForm;

class HomeController extends AbstractActionController {

    protected $userTable;

    public function getUserTable() {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Album\Model\UserTable');
        }
        return $this->userTable;
    }

    function loginAction() {
        $form = new HomeForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                if($this->getUserTable()->checkUser($user->email, $user->pass)){
                    // TODO: Manage User Session.
                    return $this->redirect()->toRoute('album');
                }
                else{
                    // TODO: Send error message.
                    return array('form' => $form, 'error_msg' => 'error_login');
                }
            }
        }
        return array('form' => $form);
    }
}
