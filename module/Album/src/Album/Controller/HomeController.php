<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;
use Album\Form\HomeForm;
use Album\Model\Home;
use Zend\Debug\Debug;

class HomeController extends AbstractActionController {

    protected $authDbAdapter;
    protected $authService;

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

        if($this->getAuthService()->hasIdentity()){
            Debug::dump($this->getAuthService()->getIdentity());
        }

        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $home = new Home();
            $form->setInputFilter($home->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $home->exchangeArray($form->getData());
                $this->getAuthDbAdapter()->setIdentity($home->email)->setCredential($home->pass);
                $result = $this->getAuthService()->authenticate($this->getAuthDbAdapter());
                if($result->isValid()){
                    $storage = $this->getAuthService()->getStorage();
                    $storage->write($this->getAuthDbAdapter()->getResultRowObject(array('email', 'rol')));
                    return $this->redirect()->toRoute('album');
                }
                else{
                    return array('form' => $form, 'error_msg' => 'error_login');
                }
            }
        }
        return array('form' => $form);
    }
    
    function logoutAction(){
        $this->getAuthService()->clearIdentity();
        if($this->getAuthService()->hasIdentity()){
            Debug::dump($this->getAuthService()->getIdentity());
        } else {
            Debug::dump('It has not identity');
        }
        return array('form' => new HomeForm(), 'error_msg' => 'success_logout');
    }
}