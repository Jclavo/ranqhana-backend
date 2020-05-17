<?php

namespace App\Utils;

use Illuminate\Support\Facades\Schema;

class PaginationUtils
{
    static function getPageSize($pageSize){
        // Initialize values if they are empty.
        if (empty($pageSize)) {
            $pageSize = 20;
        }
        return $pageSize;
    }

    static function getSortColumn($sortColumn,$table){
        
        if (empty($sortColumn)) {
            $sortColumn = "updated_at";
        }

        if(Schema::hasColumn($table, $sortColumn ) === false){
            $sortColumn = "updated_at";
        }

        return $sortColumn;
    }

    static function getSortDirection($sortDirection){

        $sortArray = array("asc", "ASC", "desc", "DESC");

        if (!in_array($sortDirection, $sortArray)) {
            $sortDirection = "DESC";
        }
        return $sortDirection;
    }

}