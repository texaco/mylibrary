<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Album\Model;

use Zend\Db\TableGateway\TableGateway;

class AlbumTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getAlbum($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getAlbums($title = null, $artist = null, $platform = null, $shelve = null, $seen = null) {

        if (!empty($title) or ! empty($artist) or ! empty($platform) or ! empty($shelve) or ! empty($seen)) {
            $select = $this->tableGateway->getSql()->select();
            $where = $select->where;
            if (!empty($title)) {
                $where->like('title', '%' . $title . '%');
            }
            if (!empty($artist)) {
                $where->like('artist', '%' . $artist . '%');
            }
            if (!empty($platform)) {
                $where->like('platform', '%' . $platform . '%');
            }
            if (!empty($shelve)) {
                $where->like('shelve', '%' . $shelve . '%');
            }
            if (!empty($seen)) {
                $where->like('seen', '%' . $seen . '%');
            }
            return $this->tableGateway->select($where);
        } else {
            return $this->fetchAll();
        }
    }

    public function saveAlbum(Album $album) {
        $data = array(
            'artist' => $album->artist,
            'title' => $album->title,
            'platform' => $album->platform,
            'shelve' => $album->shelve,
            'cover' => $album->cover,
            'seen' => $album->seen,
        );

        $id = (int) $album->id;
        if ($id == 0) {
            $data['registerDate'] = date('d-m-Y');
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAlbum($id)) {
                $data['editDate'] = date('d-m-Y');
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Album id does not exist');
            }
        }
    }

    public function deleteAlbum($id) {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

}
