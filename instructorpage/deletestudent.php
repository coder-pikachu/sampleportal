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

if (isset($_POST['btnFrmDeleteStudent'])) {
    $valInstructorId = $_SESSION['sess_user_id'];
    $valDelStudentId = (string) filter_input(INPUT_POST, 'valDelStudentId');
    $valCourseId = (string) filter_input(INPUT_POST, 'valDelCourseId');
    $studentsPage = (int) filter_input(INPUT_POST, 'valDelPageNo');

    error_log('valDelStudentId: ' . $valDelStudentId);
    if (strlen($valDelStudentId) == 0) {
        $addCourseErrors->addError('txtDelStudentId', 1);
    }

    if ($addCourseErrors->getErrorsLength() == 0) {
        try {
            error_log("Deleting student.. ");
            $qDel = "DELETE FROM `tbl_students` WHERE `id`=:studentId";
            $queryDel = $dbh->prepare($qDel);
            $queryDel->execute(array(':studentId' => $valDelStudentId));
            $addCourseErrors->addError('delStudentSuccess', 0);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            $addCourseErrors->addError('delStudentSystem', 1);
        }
    }
    header('Location: students.php?cid=' . $valCourseId . '&page=' . ($studentsPage ? $studentsPage : 1) . '&o=' . base64_encode(serialize($addCourseErrors)));
}
?>