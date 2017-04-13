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

if (isset($_POST['btnCreateStudent'])) {
    $valInstructorId = $_SESSION['sess_user_id'];

    $valFirstName = (string) filter_input(INPUT_POST, "valFirstName");
    $valLastname = (string) filter_input(INPUT_POST, "valLastname");
    $valSpiritualName = (string) filter_input(INPUT_POST, "valSpiritualName");
    $valSex = (string) filter_input(INPUT_POST, "valSex");
    $valPhone = (string) filter_input(INPUT_POST, "valPhone");
    $valPhoneDay = (string) filter_input(INPUT_POST, "valPhone-day");
    $valEmail = (string) filter_input(INPUT_POST, "valEmail");
    $valStreetAddress = (string) filter_input(INPUT_POST, "valStreetAddress");
    $valAddress2 = (string) filter_input(INPUT_POST, "valAddress2");
    $valLocation = (string) filter_input(INPUT_POST, "valLocation");
    $valCity = (string) filter_input(INPUT_POST, "valCity");
    $valState = (string) filter_input(INPUT_POST, "valState");
    $valCountry = (string) filter_input(INPUT_POST, "valCountry");
    $valZipCode = (string) filter_input(INPUT_POST, "valZipCode");
    $valCourseId = (string) filter_input(INPUT_POST, "valCourseId");

    error_log('$valFirstName: ' . $valTxtCourseName);
    if (strlen($valFirstName) == 0) {
        $addCourseErrors->addError('valFirstName', 1);
    }
    error_log('valLastname: ' . $valLastname);
    if (strlen($valLastname) == 0) {
        $addCourseErrors->addError('valLastname', 1);
    }
    error_log('$valSex: ' . $valSex);
    if (strlen($valSex) == 0) {
        $addCourseErrors->addError('valSex', 1);
    }
    error_log('$valEmail: ' . $valEmail);
    if (strlen($valEmail) == 0) {
        $addCourseErrors->addError('valEmail', 1);
    }
    error_log('$valCourseId: ' . $valCourseId);
    if (strlen($valCourseId) == 0) {
        $addCourseErrors->addError('valCourseId', 1);
    }

    if ($addCourseErrors->getErrorsLength() == 0) {
        $q = "INSERT INTO `tbl_students` (`FirstName`, `Lastname`, `SpiritualName`, `Sex`, `Phone`, `Phone-day`, `Email`, `StreetAddress`, `Address2`, `City`, `State`, `Country`, `ZipCode`, `courseId`) VALUES ( :FirstName, :Lastname, :SpiritualName, :Sex, :Phone, :PhoneDay, :Email, :StreetAddress, :Address2, :City, :State, :Country, :ZipCode, :courseId)";
        $query = $dbh->prepare($q);
        try {
            $query->execute(array(':FirstName' => $valFirstName,
                ':Lastname' => $valLastname,
                ':SpiritualName' => $valSpiritualName,
                ':Sex' => $valSex,
                ':Phone' => $valPhone,
                ':PhoneDay' => $valPhoneDay,
                ':Email' => $valEmail,
                ':StreetAddress' => $valStreetAddress,
                ':Address2' => $valAddress2,
                ':City' => $valCity,
                ':State' => $valState,
                ':Country' => $valCountry,
                ':ZipCode' => $valZipCode,
                ':courseId' => $valCourseId));
            $addCourseErrors->addError('addStudentSuccess', 0);
        } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        $addCourseErrors->addError('addStudentSystem', 1);
    }
}
header('Location: students.php?cid=' . $valCourseId . '&o=' . base64_encode(serialize($addCourseErrors)));
}
?>