<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Album\Model;

use Zend\Db\TableGateway\TableGateway;

class PlatformTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($idUser) {
        $select = $this->tableGateway->getSql()->select();
        $conditions = array('idUser' => $idUser);
        $select->where($conditions);
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getPlatform($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function savePlatform(Platform $platform) {
        $data = array(
            'name' => $platform->name,
            'description' => $platform->description,
            'idUser' => $platform->idUser,
        );

        $id = (int) $platform->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPlatform($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Platform id does not exist');
            }
        }
    }

    public function deletePlatform($id) {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

}
