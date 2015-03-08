<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Album\Service;

use Album\Service\AlbumAclServiceInterface;
use Zend\Permissions\Acl\Acl; 
use \Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class AlbumAclService implements AlbumAclServiceInterface {

    private $acl;
    
    private function getAcl(){
        if(!$this->acl){
            $this->acl = new Acl();
            $this->acl->addRole(new Role('Guest'));
            $this->acl->addRole(new Role('User'), 'Guest');
            $this->acl->addRole(new Role('Admin'));
            $this->acl->addResource(new Resource('Home'));
            $this->acl->addResource(new Resource('Album'));
            $this->acl->addResource(new Resource('Shelve'));
            $this->acl->addResource(new Resource('Platform'));
            $this->acl->addResource(new Resource('User'));
            $this->acl->allow('Guest', 'Home');
            $this->acl->allow('User', 'Album');
            $this->acl->allow('User', 'Shelve');
            $this->acl->allow('User', 'Platform');
            $this->acl->allow('Admin');

        }
        return $this->acl;
    }
    
    public function isAllowed($rol, $resource, $privilege) {
        return $this->getAcl()->isAllowed($rol, $resource, $privilege);
    }
}
