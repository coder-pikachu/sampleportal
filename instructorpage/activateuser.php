<?php

include 'Errors.php';
include './database-config.php';
session_start();
$role = $_SESSION['sess_userrole'];

if ($role != "admin") {
    header('Location: index.php?err=2');
}
$addCourseErrors = new Errors();

if (isset($_POST['btnActUser'])) {

    $valUserId = (string) filter_input(INPUT_POST, 'actUserId');
    $pageNo = (int) filter_input(INPUT_POST, 'actPageNo');
    $valUserActive = (int) filter_input(INPUT_POST, 'actUserActivate');


    error_log('$valUserId: ' . $valUserId);
    if (strlen($valUserId) == 0) {
        $addCourseErrors->addError('valUserId', 1);
    }
    error_log('$valUserActive: ' . $valUserActive);
    if (strlen($valUserActive) == 0) {
        $addCourseErrors->addError('valUserActive', 1);
    }


    if ($addCourseErrors->getErrorsLength() == 0) {
        $q = "UPDATE `TBL_USERS` SET `active`=:activeOperation WHERE `id`=:userId";
        $query = $dbh->prepare($q);
        try {
            $query->execute(array(':activeOperation' => $valUserActive,
                ':userId' => $valUserId));
            $addCourseErrors->addError('actUserSuccess', 0);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            $addCourseErrors->addError('actSystem', 1);
        }
    }
    header('Location: activateusers.php?page=' . $pageNo . '&o=' . base64_encode(serialize($addCourseErrors)));
}
?>