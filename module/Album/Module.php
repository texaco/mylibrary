<?php

namespace Album;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Album\Model\User;
use Album\Model\UserTable;
use Album\Model\Album;
use Album\Model\AlbumTable;
use Album\Model\Shelve;
use Album\Model\ShelveTable;
use Album\Model\Platform;
use Album\Model\PlatformTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface {

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Album\Model\UserTable' => function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                'Album\Model\AlbumTable' => function($sm) {
                    $tableGateway = $sm->get('AlbumTableGateway');
                    $table = new AlbumTable($tableGateway);
                    return $table;
                },
                'AlbumTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Album());
                    return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
                },
                'Album\Model\ShelveTable' => function($sm) {
                    $tableGateway = $sm->get('ShelveTableGateway');
                    $table = new ShelveTable($tableGateway);
                    return $table;
                },
                'ShelveTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Shelve());
                    return new TableGateway('shelve', $dbAdapter, null, $resultSetPrototype);
                },
                'Album\Model\PlatformTable' => function($sm) {
                    $tableGateway = $sm->get('PlatformTableGateway');
                    $table = new PlatformTable($tableGateway);
                    return $table;
                },
                'PlatformTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Platform());
                    return new TableGateway('platform', $dbAdapter, null, $resultSetPrototype);
                },
            ),
            'invokables' => array(
                // Keys are the service names
                // Values are valid class names to instantiate.
                'Album\Service\DataTable' => 'Album\Service\DataTable',
            ),  
        );
    }

}
