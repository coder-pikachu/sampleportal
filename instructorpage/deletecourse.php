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

if (isset($_POST['btnDeleteCourse'])) {
    $valInstructorId = $_SESSION['sess_user_id'];
    $valTxtCourseId = (string) filter_input(INPUT_POST, 'txtDelCourseId');

    error_log('$valTxtCourseId: ' . $valTxtCourseId);
    if (strlen($valTxtCourseId) == 0) {
        $addCourseErrors->addError('txtDelCourseId', 1);
    }

    if ($addCourseErrors->getErrorsLength() == 0) {
        $q = "SELECT COUNT(1) STUDENT_COUNT FROM TBL_STUDENTS WHERE COURSEID=:courseId";
        $query = $dbh->prepare($q);
        try {
            $query->execute(array(':courseId' => $valTxtCourseId));
            $studentCount = 0;
            if ($query->rowCount() == 0) {
                $addCourseErrors->addError('system', 1);
                header('Location: courses.php?o=' . base64_encode(serialize($addCourseErrors)));
            } else {
                $row = $query->fetch(PDO::FETCH_ASSOC);
                $studentCount = $row ['STUDENT_COUNT'];
                error_log("Student Count: " . $studentCount);
                if ($studentCount == 0) {
                    error_log("Deleting .. ");
                    $qDel = "DELETE FROM `tbl_courses` WHERE `id`=:courseId";
                    $queryDel = $dbh->prepare($qDel);
                    $queryDel->execute(array(':courseId' => $valTxtCourseId));
                } else {
                    $addCourseErrors->addError('valNonEmptyCourse', 1);
                    header('Location: courses.php?o=' . base64_encode(serialize($addCourseErrors)));
                }
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            $addCourseErrors->addError('system', 1);
        }
    }
    header('Location: courses.php?o=' . base64_encode(serialize($addCourseErrors)));
}
?>