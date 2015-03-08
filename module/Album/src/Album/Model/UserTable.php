<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Album\Model;

use Zend\Db\TableGateway\TableGateway;

class UserTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getUser($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getUsers($email = null) {

        if (!empty($email)) {
            $select = $this->tableGateway->getSql()->select();
            $where = $select->where;
            $where->equalTo('email', $email);
            return $this->tableGateway->select($where);
        } else {
            return $this->fetchAll();
        }
    }

    public function checkUser($email, $pass) {

        if (!empty($email) and !empty($pass)) {
            $select = $this->tableGateway->getSql()->select();
            $where = $select->where;
            $where->equalTo('email', $email)->equalTo('pass', $pass);
            return $this->tableGateway->select($where)->count() > 0;
        } else {
            throw new \Exception('Params can not be empty');        
        }
    }

    public function saveUser(User $user) {
        $data = array(
            'email' => $user->email,
            'pass' => $user->pass,
        );

        $id = (int) $user->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUser($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Album id does not exist');
            }
        }
    }

    public function deleteUser($id) {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}
