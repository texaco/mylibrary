<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Album\Model;

use Zend\Db\TableGateway\TableGateway;

class ShelveTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getShelve($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveShelve(Shelve $shelve) {
        $data = array(
            'name' => $shelve->name,
            'description' => $shelve->description,
        );

        $id = (int) $shelve->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getShelve($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Shelve id does not exist');
            }
        }
    }

    public function deleteShelve($id) {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

}
