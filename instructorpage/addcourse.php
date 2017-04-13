<?php

include 'Errors.php';
include './database-config.php';
session_start();
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$role = $_SESSION ['sess_userrole'];

if ($role != "user") {
    header('Location: index.php?err=2');
}
$addCourseErrors = new Errors();

if (isset($_POST['btnAddCourse'])) {
    $valInstructorId = $_SESSION['sess_user_id'];
    $valTxtCourseName = (string) filter_input(INPUT_POST, 'txtCourseName');
    $valDtCourseDate = (string) filter_input(INPUT_POST, 'dtCourseDate');
    $valSltIAMVersion = (string) filter_input(INPUT_POST, 'sltIAMVersion');
    $valTxtCity = (string) filter_input(INPUT_POST, 'txtCity');
    $valTxtState = (string) filter_input(INPUT_POST, 'txtState');

    error_log('$valTxtCourseName: ' . $valTxtCourseName);
    if (strlen($valTxtCourseName) == 0) {
        $addCourseErrors->addError('txtCourseName', 1);
    }
    error_log('$valDtCourseDate: ' . $valDtCourseDate);
    if (strlen($valDtCourseDate) == 0) {
        $addCourseErrors->addError('dtCourseDate', 1);
    }
    error_log('$valSltIAMVersion: ' . $valSltIAMVersion);
    if (strlen($valSltIAMVersion) == 0) {
        $addCourseErrors->addError('sltIAMVersion', 1);
    }
    error_log('$valTxtCity: ' . $valTxtCity);
    if (strlen($valTxtCity) == 0) {
        $addCourseErrors->addError('txtCity', 1);
    }
    error_log('$valTxtState: ' . $valTxtState);
    if (strlen($valTxtState) == 0) {
        $addCourseErrors->addError('txtState', 1);
    }

    if ($addCourseErrors->getErrorsLength() == 0) {
        $q = "INSERT INTO `tbl_courses` ( `name`, `instructorId`, `iamversion`, `city`, `date`, `state`) VALUES (:courseName,:instructorId,:iamversion,:city,:date,:state)";
        $query = $dbh->prepare($q);
        try {
            $query->execute(array(':courseName' => $valTxtCourseName,
                ':instructorId' => $valInstructorId,
                ':iamversion' => $valSltIAMVersion,
                ':city' => $valTxtCity,
                ':date' => $valDtCourseDate,
                ':state' => $valTxtState));
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            $addCourseErrors->addError('system', 1);
        }
    }
    header('Location: courses.php?o=' . base64_encode(serialize($addCourseErrors)));
}
?>