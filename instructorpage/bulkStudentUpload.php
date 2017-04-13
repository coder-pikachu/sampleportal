<?php

include 'Errors.php';
include './database-config.php';
session_start();

$role = $_SESSION ['sess_userrole'];

if ($role != "user") {
    header('Location: index.php?err=2');
}
$addCourseErrors = new Errors();
$valCourseId = (string) filter_input(INPUT_POST, "valBlkCourseId");

$extension = end(explode(".", basename($_FILES['valFlStudentFile']['name'])));
if (isset($_FILES['valFlStudentFile']) && $_FILES['valFlStudentFile']['size'] < (2 * 10485760) && $extension == 'csv') {
    $file = $_FILES['valFlStudentFile']['tmp_name'];
    $handle = fopen($file, "r");
    try {

        $STM = $dbh->prepare('INSERT INTO `tbl_students`(`FirstName`, `Lastname`, `SpiritualName`, `Sex`, `Phone`, `Phone-day`, `Email`, `StreetAddress`, `Address2`, `City`, `State`, `Country`, `ZipCode`, `courseId`) VALUES (:FirstName, :Lastname, :SpiritualName, :Sex, :Phone, :PhoneDay, :Email, :StreetAddress, :Address2, :City, :State, :Country, :ZipCode, :courseId)');
        if ($handle !== FALSE) {
            fgets($handle);
            while (($data = fgetcsv($handle)) !== FALSE) {
                $STM->bindValue(":FirstName", $data[2]);
                $STM->bindValue(":Lastname", $data[0]);
                $STM->bindValue(":SpiritualName", $data[1]);
                $STM->bindValue(":Sex", $data[3]);
                $STM->bindValue(":Phone", $data[4]);
                $STM->bindValue(":PhoneDay", $data[5]);
                $STM->bindValue(":Email", $data[6]);
                $STM->bindValue(":StreetAddress", $data[7]);
                $STM->bindValue(":Address2", $data[8]);
                $STM->bindValue(":City", $data[9]);
                $STM->bindValue(":State", $data[10]);
                $STM->bindValue(":Country", $data[11]);
                $STM->bindValue(":ZipCode", $data[12]);
                $STM->bindValue(":courseId", $valCourseId);
                $STM->execute();
            }
            fclose($handle);
            $addCourseErrors->addError('blksuccess', 0);
            header('Location: students.php?cid=' . $valCourseId . '&o=' . base64_encode(serialize($addCourseErrors)));
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        error_log($e->getTraceAsString());
        $addCourseErrors->addError('blkSystem', 1);
        header('Location: students.php?cid=' . $valCourseId . '&o=' . base64_encode(serialize($addCourseErrors)));
    }
} else {
    if (!isset($_FILES['valFlStudentFile'])) {
        $addCourseErrors->addError('invalidFile', 1);
    }
    if ($_FILES['valFlStudentFile']['size'] >= (2 * 10485760)) {
        $addCourseErrors->addError('invalidFileSize', 1);
    }
    if ($extension != 'csv') {
        $addCourseErrors->addError('invalidFileExtension', 1);
    }
    header('Location: students.php?cid=' . $valCourseId . '&o=' . base64_encode(serialize($addCourseErrors)));
}
?>