<?php

function selectData($table, $mysqli, $select = "*", $where = "", $order = "") {
    $sql = "SELECT $select FROM `$table` $where $order";
    return $mysqli->query($sql);
}

function deleteData($table, $mysqli, $where) {
    $sql = "DELETE FROM `$table` WHERE $where";
    return $mysqli->query($sql);
}

function updateData($table, $mysqli, $data, $where){
    $sql = "UPDATE `$table` SET ";
    $updates = [];
    foreach ($data as $key => $value){
        $escaped = $mysqli->real_escape_string($value);
        $updates[] = "`$key` = '$escaped'";
    }
    $sql .= implode(", ", $updates);
    $sql .= " WHERE ";
    $wheres = [];
    foreach ($where as $key => $value){
        $escaped = $mysqli->real_escape_string($value);
        $wheres[] = "`$key` = '$escaped'";
    }
    $sql .= implode(" AND ", $wheres);
    return $mysqli->query($sql);
}

function insertData($table, $mysqli , $values){
    $columns = [];
    $items = [];
    foreach ($values as $key => $item){
        $columns[] = "`" . $key . "`";
        if (is_null($item)) {
            $items[] = "NULL";
        } else {
            $items[] = "'" . $mysqli->real_escape_string($item) . "'";
        }
    }
    $columns_str = implode(', ', $columns);
    $items_str = implode(', ', $items);

    $sql = "INSERT INTO `$table` ($columns_str) VALUES ($items_str)";
    return $mysqli->query($sql);
}
