<?php

require 'database-config.php';
session_start();
$userEmail = "";
$password = "";
error_log($userEmail . " : " . $password);
if (isset($_POST['userEmail'])) {
    $userEmail = $_POST['userEmail'];
}
if (isset($_POST['password'])) {
    $password = hash('sha256', $_POST['password']);
}
if (!$dbh) {
    header('Location: index.php?err=99');
}
$q = 'SELECT * FROM tbl_users WHERE userEmail=:userEmail AND password=:password';
$query = $dbh->prepare($q);
$query->execute(array(':userEmail' => $userEmail, ':password' => $password));

if ($query->rowCount() == 0) {
    header('Location: index.php?err=1');
} else {
    $row = $query->fetch(PDO::FETCH_ASSOC);
    session_regenerate_id();
    if ($row['active'] == 0) {
         header('Location: index.php?err=98');
    } else {
        $_SESSION['sess_user_id'] = $row['id'];
        $_SESSION['sess_username'] = $row['name'];
        $_SESSION['sess_userEmail'] = $row['userEmail'];
        $_SESSION['sess_userrole'] = $row['role'];
        session_write_close();
        if ($_SESSION['sess_userrole'] == "admin") {
            header('Location: adminhome.php');
        } else {
            header('Location: userhome.php');
        }
    }
}
?>