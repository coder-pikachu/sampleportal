<?php

require 'database-config.php';
require 'Errors.php';
session_start();
$PAGE_SIZE = 10;
$role = $_SESSION ['sess_userrole'];
if ($role != "user") {
    header('Location: index.php?err=2');
}
$currentPage = 1;
$outputType = 'php';
if (isset($_GET ['page'])) {
    $currentPage = $_GET ['page'];
}
$filterValue = '';
if (isset($_GET ['q'])) {
    $filterValue = $_GET ['q'];
}
$filterType = 'courseName';
if (isset($_GET ['filter_options'])) {
    $filterType = $_GET ['filter_options'];
}

$fitlerQuery = '';
switch ($filterType) {
    case 'courseName':
        $fitlerQuery = " and c.name like '%" . $filterValue . "%' ";
        break;
    case 'city':
        $fitlerQuery = " and c.city like '%" . $filterValue . "%' ";
        break;
    case 'date':
        $fitlerQuery = " and c.date like '%" . $filterValue . "%' ";
        break;
    default:
        break;
}

$userEmail = $_SESSION ['sess_userEmail'];
$userId = $_SESSION ['sess_user_id'];

$start = ($pageNo - 1) * $PAGE_SIZE;
// $end = ($pageNo - 1) * $PAGE_SIZE + ($PAGE_SIZE - 1);
error_log($start . " -- ");
$q = 'SELECT c.id, c.name, c.date, c.iamversion, c.city, c.state FROM TBL_COURSES C INNER JOIN TBL_USERS U ON U.ID = C.INSTRUCTORID WHERE U.USEREMAIL=:userEmail ' . $filterQuery . ' ORDER BY c.id DESC LIMIT ' . $PAGE_SIZE . ' OFFSET ' . $start;
error_log($q);
$query = $dbh->prepare($q);
$query->bindParam(':userEmail', $userEmail);
$query->execute();
$rows = $query->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET ['output_type'])) {
    if (strtolower($_GET ['output_type']) == 'json') {
        return json_encode($rows);
    } else {
        return $rows;
    }
} else {
    return $rows;
}

