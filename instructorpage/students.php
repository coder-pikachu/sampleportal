<?php
include './database-config.php';
require 'Errors.php';
session_start();
$PAGE_SIZE = 10;
$role = $_SESSION ['sess_userrole'];
if ($role != "user") {
    header('Location: index.php?err=2');
}
$valTxtCourseId = (string) filter_input(INPUT_GET, 'cid');

$q = 'SELECT COUNT(1) AS STUDENT_COUNT FROM TBL_STUDENTS S WHERE S.COURSEID=:cid';
$query = $dbh->prepare($q);
$query->execute(array(
    ':cid' => $valTxtCourseId
));
$studentCount = 0;
if ($query->rowCount() == 0) {
    header('Location: index.php?err=1');
} else {
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $studentCount = $row ['STUDENT_COUNT'];
    error_log("Total: " . (5 / 3));
}
$totalPages = floor($studentCount / $PAGE_SIZE) + 1;
$currentPage = (int) filter_input(INPUT_GET, 'page');
$currentPage = $currentPage == "" ? 1 : $currentPage;

if ($valTxtCourseId != "") {
    $q = 'SELECT c.name, c.date, c.iamversion, c.city, c.state FROM TBL_COURSES c WHERE c.id=:cid';
    $query = $dbh->prepare($q);
    $query->bindParam(':cid', $valTxtCourseId);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $courseName = $row['name'];
    $courseDate = $row['date'];
    $courseIAMVersion = $row['iamversion'];
    $courseCity = $row['city'];
    $courseState = $row['state'];
} else {
    header('Location: logout.php');
}

function getStudents($pageNo, $dbh, $PAGE_SIZE, $courseId) {
    $start = ($pageNo - 1) * $PAGE_SIZE;
    // $end = ($pageNo - 1) * $PAGE_SIZE + ($PAGE_SIZE - 1);
    error_log($start . " -- ");
    $q = 'SELECT `id`, `FirstName`, `Lastname`, `SpiritualName`, `Sex`, `Phone`, `Phone-day`, `Email`, `StreetAddress`, `Address2`, `City`, `State`, `Country`, `ZipCode`, `courseId` FROM `tbl_students` WHERE courseId = :courseId ORDER BY FirstName LIMIT ' . $PAGE_SIZE . ' OFFSET ' . $start;
    error_log($q);
    $query = $dbh->prepare($q);
    $query->bindParam(':courseId', $courseId);
    $query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>IAM Instructor Home</title>

        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <link rel="stylesheet"
              href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href='http://fonts.googleapis.com/css?family=Varela+Round'
              rel='stylesheet' type='text/css'>
        <meta name="viewport"
              content="width=device-width, initial-scale=1, maximum-scale=1" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
                  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
                  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                <![endif]-->
    </head>
    <body>
        <?php
        $errObjTxt = (string) filter_input(INPUT_GET, 'o');
        if (strlen($errObjTxt) > 0) {
            $errorObject = unserialize(base64_decode($errObjTxt));
            try {
                if ($errorObject->getErrorsLength() > 0) {
                    echo '<script>var errorObj = ' . json_encode($errorObject) . '</script>';
                } else {
                    echo '<script>var errorObj = {}</script>';
                }
            } catch (Exception $e1) {
                error_log($e1->getMessage());
                error_log($e1->getTrace());
                echo '<script>var errorObj = {}</script>';
            }
        } else {
            echo '<script>var errorObj = {}</script>';
        }
        ?> 
        <div class="navbar navbar-default" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed"
                            data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span> <span
                            class="icon-bar"></span> <span class="icon-bar"></span> <span
                            class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="userhome.php">IAM Instructor Portal</a>
                </div>

                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="userhome.php">Home</a></li>
                        <li><a href="courses.php">Courses</a></li>

                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#"><?php echo $_SESSION['sess_username']; ?></a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container-fluid"><div class="row">
                <div class="col-lg-12" id="msgBox"></div>
            </div></div>
        <div class="container-fluid" id="studentsContainer">
            <div class="row">
                <div class="col-md-2">
                    <a class="btn btn-primary" href="courses.php"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back to Courses</a>
                </div>

            </div>
            <div class="row spacer-sm" >

                <div class="col-md-6" >
                    <div class="well well-sm text-left">
                        <div class="lead">Selected Course</div>
                        <h3>
                            <small> Name: <?php echo $courseName ?></small><br/>
                            <small> Date: <?php echo $courseDate ?></small><br/>
                            <small> IAM Version: <?php echo $courseIAMVersion ?></small><br/>
                            <small> Location: <?php echo $courseCity . ', ' . $courseState ?></small><br/>
                        </h3>
                    </div>
                </div>
                <div class="col-md-6">

                    <div class="well well-sm text-left" style="height:230px;overflow-y: auto;position: relative;">
                        <div class="lead">Bulk upload student data</div>
                        <form action="bulkStudentUpload.php" method="POST" id="frmBulkStudentUpload" enctype="multipart/form-data">
                            <div class="input-group" style="margin-bottom: 10px;">
                                <label class="input-group-btn">
                                    <span class="btn btn-primary">
                                        Select CSV File <input name="valFlStudentFile" id="valFlStudentFile" type="file" accept=".csv" style="display: none;">
                                    </span>
                                </label>
                                <input type="text" class="form-control lblFile" readonly>
                            </div>
                            <div class="small text-left" id="blkUploadErrorMsgs">

                            </div>
                            <input type="hidden" name="valBlkCourseId" id="valBlkCourseId" value="<?php echo $valTxtCourseId ?>">

                            <a class="btn btn-primary" href="templates/IAM_Student_Information_Spreadsheet.csv"><i class="fa fa-download" aria-hidden="true"></i> Download Template</a>
                            <button type="submit" class="btn btn-warning"><i class="fa fa-upload" aria-hidden="true"></i> Upload Student Data</button>
                        </form>
                        <div id="blkOverlay" style="display:none;position: absolute; top: 0;left: 0;height: 100%;width: 100%;background-color: rgba(255, 255, 255, 0.83);margin: 0 auto; text-align: center;color:#bf7b11;">
                            <strong style="display: block; top: 40%; position: absolute; left: 43%; font-size: 20px;">Loading ...</strong>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Enrolled Students</h3>
                        </div>

                        <table class="table table-bordered table-hover table-responsive" style="height:100%;background-color: #fff; border-bottom: 1px solid #ddd;">
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Spiritual Name</th>
                                    <th>Gender</th>
                                    <th style="display: none;">Phone</th>
                                    <th style="display: none;">Phone-day</th>
                                    <th style="display: none;">Email</th>
                                    <th style="display: none;">StreetAddress</th>
                                    <th style="display: none;">Address2</th>
                                    <th style="display: none;">City</th>
                                    <th style="display: none;">State</th>
                                    <th style="display: none;">Country</th>
                                    <th style="display: none;">Zip Code</th>

                                </tr>
                            <thead>
                            <tbody>
                                <?php
                                $rows = getStudents($currentPage, $dbh, $PAGE_SIZE, $valTxtCourseId);
                                foreach ($rows as $row) {
                                    ?>
                                    <tr _id="<?php echo $row['id'] ?>">
                                        <td data-attr="FirstName" ><?php echo $row['FirstName'] ?></td>
                                        <td data-attr="Lastname" ><?php echo $row['Lastname'] ?></td>
                                        <td data-attr="SpiritualName" ><?php echo $row['SpiritualName'] ?></td>
                                        <td data-attr="Sex" ><?php echo $row['Sex'] ?></td>
                                        <td data-attr="Phone"  style="display: none;"><?php echo $row['Phone'] ?></td>
                                        <td data-attr="Phone-day"  style="display: none;"><?php echo $row['Phone-day'] ?></td>
                                        <td data-attr="Email"  style="display: none;"><?php echo $row['Email'] ?></td>
                                        <td data-attr="StreetAddress"  style="display: none;"><?php echo $row['StreetAddress'] ?></td>
                                        <td data-attr="Address2"  style="display: none;"><?php echo $row['Address2'] ?></td>
                                        <td data-attr="City"  style="display: none;"><?php echo $row['City'] ?></td>
                                        <td data-attr="State"  style="display: none;"><?php echo $row['State'] ?></td>
                                        <td data-attr="Country"  style="display: none;"><?php echo $row['Country'] ?></td>
                                        <td data-attr="ZipCode"  style="display: none;"><?php echo $row['ZipCode'] ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="panel-body" style="padding:10px 0px 0px 10px">
                            <div class="row">
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-danger btn-sm" id="deleteStudent">Delete</button></div>
                                <div class="col-sm-10">
                                    <ul class="pagination" style="padding:0px 0px 0px 10px;margin:0px;">
                                        <li <?php echo (($currentPage == 1) ? 'class="disabled"' : '') ?>><a
                                            <?php echo ($currentPage != 1) ? 'href="students.php?cid=' . $valTxtCourseId . '&page=' . ($currentPage - 1) . '"' : '' ?>
                                                aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
                                            <?php
                                            for ($index = 1; $index <= $totalPages; $index ++) {
                                                echo '<li ' . (($index == $currentPage) ? 'class="active"' : '') . '><a href = "students.php?cid=' . $valTxtCourseId . '&page=' . $index . '">' . $index . '</a></li>';
                                            }
                                            ?>
                                        <li
                                            <?php echo (($currentPage == $totalPages) ? 'class="disabled"' : '') ?>><a
                                                <?php echo ($currentPage != $totalPages) ? 'href="students.php?cid=' . $valTxtCourseId . '&page=' . ($currentPage + 1) . '"' : '' ?>
                                                aria-label="Next"><span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="dvSelectedStudent" class="col-md-4" style="border-left:1px dashed #269abc">
                    <button type="button" id="btnAddStudentContainer" class="btn btn-primary btn-default" style="float: right">Add New</button>
                    <div class="lead">Selected Student</div>
                    <div style="font-size: 120%;">
                        <span style="color:#989898;"> First name: </span><span id="lblFirstName"></span><br/>
                        <span style="color:#989898;"> Last name: </span><span id="lblLastname"></span><br/>
                        <span style="color:#989898;"> Spiritual name: </span><span id="lblSpiritualName"></span><br/>
                        <span style="color:#989898;"> Gender: </span><span id="lblSex"></span><br/>
                        <span style="color:#989898;"> Phone: </span><span id="lblPhone"></span><br/>
                        <span style="color:#989898;"> Phone-day: </span><span id="lblPhone-day"></span><br/>
                        <span style="color:#989898;"> Email: </span><span id="lblEmail"></span><br/>
                        <span style="color:#989898;"> Street Address: </span><span id="lblStreetAddress"></span><br/>
                        <span style="color:#989898;"> Address 2nd line: </span><span id="lblAddress2"></span><br/>
                        <span style="color:#989898;"> City: </span><span id="lblCity"></span><br/>
                        <span style="color:#989898;"> State: </span><span id="lblState"></span><br/>
                        <span style="color:#989898;"> Country: </span><span id="lblCountry"></span><br/>
                        <span style="color:#989898;"> ZipCode: </span><span id="lblZipCode"></span><br/>
                    </div>

                </div>
                <div id='dvCreateStudent' class="col-md-4" style="border-left:1px dashed #269abc; display: none;">
                    <button type="button" id="btnCancelStudentContainer" class="btn btn-primary btn-warning" style="float: right">Cancel</button>
                    <div class="lead">Create Student</div>
                    <form action="addstudent.php" method="POST">
                        <div class="row" style="font-size: 120%;">
                            <div class="col-md-6">

                                <span style="color:#989898;"> First name: </span><input required type="text" class="form-control spacer-btm-sm" name="valFirstName" id="valFirstName"/>
                                <div name="valFirstNameError" id="valFirstNameError" class='alert-danger-text'></div>
                                <span style="color:#989898;"> Last name: </span><input required type="text" class="form-control spacer-btm-sm" name="valLastname" id="valLastname"/>
                                <div name="valLastnameError" id="valLastnameError" class='alert-danger-text'></div>
                                <span style="color:#989898;"> Spiritual name: </span><input type="text" class="form-control spacer-btm-sm" name="valSpiritualName" id="valSpiritualName"/>
                                <div name="valSpiritualNameError" id="valSpiritualNameError" class='alert-danger-text'></div>
                                <span style="color:#989898;"> Gender: </span><select required class="form-control spacer-btm-sm" style="width: 150px; display: inline;" name="valSex" id="valSex">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <div name="valSexError" id="valSexError" class='alert-danger-text'></div>
                                <span style="color:#989898;"> Phone: </span><input class="form-control spacer-btm-sm" style="width: 150px; display: inline;"  name="valPhone" id="valPhone"/>
                                <div name="valPhoneError" id="valPhoneError" class='alert-danger-text'></div>
                                <span style="color:#989898;"> Phone-day: </span><input class="form-control spacer-btm-sm" style="width: 150px; display: inline;"  name="valPhone-day" id="valPhone-day"/>
                                <div name="valPhone-dayError" id="valPhone-dayError" class='alert-danger-text'>
                                    <span style="color:#989898;"> Email: </span><input type="email" class="form-control spacer-btm-sm" required name="valEmail" id="valEmail"/>
                                    <div name="valEmailError" id="valEmailError" class='alert-danger-text'></div></div></div><div class="col-md-6">
                                <span style="color:#989898;"> Street Address: </span><input class="form-control spacer-btm-sm" name="valStreetAddress" id="valStreetAddress"/>
                                <div name="valStreetAddressError" id="valStreetAddressError" class='alert-danger-text'></div>
                                <span style="color:#989898;"> Address 2nd line: </span><input class="form-control spacer-btm-sm" name="valAddress2" id="valAddress2"/>
                                <div name="valAddress2Error" id="valAddress2Error" class='alert-danger-text'></div>
                                <span style="color:#989898;"> Location: </span><input class="form-control spacer-btm-sm" name="valLocation" id="valLocation" style="width:100%"/>
                                <div name="valCityError" id="valCityError" class='alert-danger-text'></div>
                                <span style="color:#989898;"> City: </span><input class="form-control spacer-btm-sm" name="valCity" readonly id="valCity"/>
                                <div name="valCityError" id="valCityError" class='alert-danger-text'></div>
                                <span style="color:#989898;"> State: </span><input class="form-control spacer-btm-sm" name="valState" readonly id="valState"/>
                                <div name="valStateError" id="valStateError" class='alert-danger-text'></div>
                                <span style="color:#989898;"> Country: </span><input class="form-control spacer-btm-sm" name="valCountry" readonly id="valCountry"/>
                                <div name="valCountryError" id="valCountryError" class='alert-danger-text'></div>
                                <span style="color:#989898;"> ZipCode: </span><input class="form-control spacer-btm-sm" name="valZipCode" id="valZipCode"/>
                                <div name="valZipCodeError" id="valZipCodeError" class='alert-danger-text'></div>
                                <input type="hidden" name="valCourseId" id="valCourseId" value="<?php echo $valTxtCourseId ?>"/>
                                <input type="submit" class="btn btn-default btn-primary" name="btnCreateStudent" id="btnCreateStudent"/>
                            </div>
                        </div></form>
                </div>
            </div>
        </div>
        <form action="deletestudent.php" method="POST" style="display: none;">
            <input type="hidden" id="valDelStudentId" name="valDelStudentId"/>
            <input type="hidden" id="valDelCourseId" name="valDelCourseId" value="<?php echo $valTxtCourseId ?>"/>
            <input type="hidden" id="valDelPageNo" name="valDelPageNo" value="<?php echo $currentPage ?>"/>
            <button type="submit" name="btnFrmDeleteStudent" id="btnFrmDeleteStudent"/>
        </form>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script
        src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
        <script src="js/typeahead.jquery.min.js"></script>
        <script src="js/bootbox.min.js"></script>
        <script src="js/students.js"></script>
    </body>
</html>
