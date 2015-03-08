<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Album\Model\User;
use \Zend\View\Model\ViewModel;
use Album\Form\HomeForm;
use Zend\Debug\Debug;

class HomeController extends AbstractActionController {

    protected $userTable;
    protected $authDbAdapter;
    protected $authService;

    public function getUserTable() {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Album\Model\UserTable');
        }
        return $this->userTable;
    }

    public function getAuthDbAdapter() {
        if (!$this->authDbAdapter) {
            $sm = $this->getServiceLocator();
            $this->authDbAdapter= $sm->get('AuthDbAdapter');
        }
        return $this->authDbAdapter;
    }

    public function getAuthService() {
        if (!$this->authService) {
            $sm = $this->getServiceLocator();
            $this->authService= $sm->get('Album\Service\AuthServiceInterface');
        }
        return $this->authService;
    }

    function loginAction() {
        $this->layout('layout/home');
        $form = new HomeForm();
        $var = 'test';
        Debug::dump($var);

        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                $this->getAuthDbAdapter()->setIdentity($user->email)->setCredential($user->pass);
                $result = $this->getAuthService()->authenticate($this->getAuthDbAdapter());
                if($result->isValid()){
                    return $this->redirect()->toRoute('album');
                }
                else{
                    return array('form' => $form, 'error_msg' => 'error_login');
                }
            }
        }
        return array('form' => $form);
    }
}