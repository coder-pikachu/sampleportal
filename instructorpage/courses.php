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
if (isset($_GET ['page'])) {
    $currentPage = $_GET ['page'];
}
$userEmail = $_SESSION ['sess_userEmail'];
$userId = $_SESSION ['sess_user_id'];

$q = 'SELECT COUNT(1) AS COURSE_COUNT FROM TBL_COURSES C INNER JOIN TBL_USERS U ON U.ID = C.INSTRUCTORID WHERE U.USEREMAIL=:userEmail';
$query = $dbh->prepare($q);
$query->execute(array(
    ':userEmail' => $userEmail
));
$courseCount = 0;
if ($query->rowCount() == 0) {
    header('Location: index.php?err=1');
} else {
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $courseCount = $row ['COURSE_COUNT'];
}
$totalPages = floor($courseCount / $PAGE_SIZE) + 1;

function getCourses($pageNo, $dbh, $PAGE_SIZE, $userEmail)
{
    $start = ($pageNo - 1) * $PAGE_SIZE;
    // $end = ($pageNo - 1) * $PAGE_SIZE + ($PAGE_SIZE - 1);
    error_log($start . " -- ");
    $q = 'SELECT c.id, c.name, c.date, c.iamversion, c.city, c.state FROM TBL_COURSES C INNER JOIN TBL_USERS U ON U.ID = C.INSTRUCTORID WHERE U.USEREMAIL=:userEmail ORDER BY c.id DESC LIMIT ' . $PAGE_SIZE . ' OFFSET ' . $start;
    error_log($q);
    $query = $dbh->prepare($q);
    $query->bindParam(':userEmail', $userEmail);
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
          content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

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
                <li class="active"><a href="courses.php">Courses</a></li>

            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#"><?php echo $_SESSION['sess_username']; ?></a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="container homepage">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6 welcome-page" style='text-align: center'>
            <h2>
                You have <span style='color: #71daff'><?php echo $courseCount ?> </span>course(s).
            </h2>
        </div>
        <div class="col-md-3"></div>
    </div>
    <div class="row spacer"></div>
    <div class="row" id="msgBox">
        <div class="col-md-12">
            <?php
            $errObjTxt = (string)filter_input(INPUT_GET, 'o');
            if (strlen($errObjTxt) > 0) {
                $errorObject = unserialize(base64_decode($errObjTxt));
                try {
                    if ($errorObject->getErrorsLength() > 0) {
                        echo '<script>var errorObj = ' . json_encode($errorObject) . '</script>';
                    } else {
                        echo '<script>var errorObj = {}</script><div class="alert alert-success">Operation completed successfully.</div>';
                    }
                } catch (Exception $e1) {
                    echo '<script>var errorObj = {}</script>';
                }
            } else {
                echo '<script>var errorObj = {}</script>';
            }
            ?> </div>
    </div>
    <div class="row">
        <div class="col-lg-12 well text-center">
            <table class="table table-bordered table-hover table-responsive text-left"
                   style="">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>IAM Version</th>
                    <th>City, State</th>
                </tr>
                <thead>
                <tbody>
                <?php
                $rows = getCourses($currentPage, $dbh, $PAGE_SIZE, $userEmail);
                foreach ($rows as $row) {
                    ?>
                    <tr _id="<?php echo $row['id'] ?>">
                        <td><?php echo $row['name'] ?></td>
                        <td><?php echo $row['date'] ?></td>
                        <td><?php echo $row['iamversion'] ?></td>
                        <td><?php echo $row['city'] . ', ' . $row['state'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <ul class="pagination">
                <li <?php echo(($currentPage == 1) ? 'class="disabled"' : '') ?>><a
                        <?php echo ($currentPage != 1) ? 'href="courses.php?page=' . ($currentPage - 1) . '"' : '' ?>
                            aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
                <?php
                for ($index = 1; $index <= $totalPages; $index++) {
                    echo '<li ' . (($index == $currentPage) ? 'class="active"' : '') . '><a href = "courses.php?page=' . $index . '">' . $index . '</a></li>';
                }
                ?>
                <li
                    <?php echo(($currentPage == $totalPages) ? 'class="disabled"' : '') ?>><a
                        <?php echo ($currentPage != $totalPages) ? 'href="courses.php?page=' . ($currentPage + 1) . '"' : '' ?>
                            aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 pad-top-sm">
            <button type="button" class="btn btn-primary" data-toggle="modal"
                    data-target="#addCourseModal"><i class="fa fa-plus-square" aria-hidden="true"></i> Add
            </button>
            <button type="button" class="btn btn-danger" id="btnDeleteCourse"><i class="fa fa-minus-square"
                                                                                 aria-hidden="true"></i> Delete
            </button>

        </div>
        <div class="col-lg-7" style="text-align: center">

        </div>
        <div class="col-lg-2 pad-top-sm">
            <button type="button" class="btn btn-info" id="btnManageStudents"><i class="fa fa-user-circle-o"
                                                                                 aria-hidden="true"></i> &nbsp;Manage
                Students
            </button>
        </div>
    </div>
</div>
<div style="display: none;">
    <form id="frmDeleteCourse" action="deletecourse.php" method="POST">
        <input type="hidden" id="txtDelCourseId" name="txtDelCourseId"/>
        <input type="submit" name="btnDeleteCourse" id="btnSubmitDeleteCourse" value=""/>
    </form>
</div>
<div class="modal fade" id="addCourseModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Add a new course</h4>
            </div>
            <form class="form-horizontal" method="POST" action="addcourse.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Course
                            name</label>
                        <div class="col-sm-10">
                            <input type="text" course-field='true' class="form-control"
                                   id="txtCourseName" required="true" name="txtCourseName"
                                   placeholder="Course Name"> <span class='alert-danger-text'
                                                                    id='txtCourseNameError'></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Organization</label>
                        <div class="col-sm-10">
                            <input type="text" course-field='true' class="form-control"
                                   id="txtOrganization" required="true" name="txtOrganization"
                                   placeholder="Organization"> <span class='alert-danger-text'
                                                                    id='txtOrganizationError'></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Date</label>
                        <div class="col-sm-10">
                            <input type="date" course-field='true' class="form-control"
                                   id="dtCourseDate" required="true" name="dtCourseDate"
                                   placeholder=" (yyyy-mm-dd)"> <span class='alert-danger-text'
                                                                      id='dtCourseDateError'></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">IAM
                            Version</label>
                        <div class="col-sm-10">
                            <select id="sltIAMVersion" required="true" course-field='true'
                                    name="sltIAMVersion" class="form-control">
                                <option value="IAM-35">IAM-35</option>
                                <option value="IAM-35">IAM-20</option>
                                <option value="Youth IAM">Youth IAM</option>
                            </select> <span class='alert-danger-text'
                                            id='sltIAMVersionError'></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtCityState" class="col-sm-2 control-label">City,
                            State</label>
                        <div class="col-sm-10">
                            <input type="text" required="true" class="form-control"
                                   id="txtCityState" name="txtCityState" placeholder="Enter city">
                            <input type="hidden" course-field='true' id="txtCity"
                                   name="txtCity"> <input type="hidden" id="txtState"
                                                          name="txtState"> <span class='alert-danger-text'
                                                                                 id='txtCityStateError'></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="btnAddCourse"
                            id="btnAddCourse">Add
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="js/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/bootbox.min.js"></script>
<script src="js/typeahead.jquery.min.js"></script>
<script src='js/courses.js'></script>
</body>
</html>
