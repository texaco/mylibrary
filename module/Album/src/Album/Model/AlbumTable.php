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

    public function getAlbums($title = '', $artist = '', $platform = '', $shelve = '', $seen = '') {
        $select = $this->tableGateway->getSql()->select();

        var_dump($title);
        echo $title != null;
        echo '%' . ($title != null) ? $title : '' . '%';
        $where = $select->where;
        $where->like('title', '%' . ($title != null) ? $title : '' . '%');
//        $where->or;
//        $where->like('artist', '%' . ($artist != null) ? $artist : '' . '%');
//        $where->or;
//        $where->like('platform', '%' . ($platform != null) ? $platform : '' . '%');

//        //        $where->or;
//        $where->like('shelve', '%' . ($shelve != null) ? $shelve : '' . '%');
//        $where->or;
//        $where->like('seen', '%' . ($seen != null) ? $seen : '' . '%');

        $rowset = $this->tableGateway->select($where);
        echo $this->tableGateway->getSql()->getSqlStringForSqlObject($select);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row with title $title");
        }

        var_dump($row);

        return $row;
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
