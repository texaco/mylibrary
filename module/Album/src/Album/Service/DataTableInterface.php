<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Album\Service;

interface DataTableInterface
{
    static public function simple($request, $conn, $table, $primaryKey, $columns);
}
