<?php
require 'database-config.php';
session_start();
$role = $_SESSION['sess_userrole'];
if ($role != "user") {
    header('Location: index.php?err=2');
}
$userEmail = $_SESSION['sess_userEmail'];

$q = 'SELECT COUNT(1) AS STUDENT_COUNT FROM TBL_STUDENTS S INNER JOIN TBL_COURSES C ON C.ID = S.COURSEID INNER JOIN TBL_USERS U ON U.ID = C.INSTRUCTORID WHERE U.USEREMAIL=:userEmail';
$query = $dbh->prepare($q);
$query->execute(array(':userEmail' => $userEmail));
$studentCount = 0;
if ($query->rowCount() == 0) {
    header('Location: index.php?err=1');
} else {
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $studentCount = $row['STUDENT_COUNT'];
}

$q = 'SELECT COUNT(1) AS COURSE_COUNT FROM TBL_COURSES C INNER JOIN TBL_USERS U ON U.ID = C.INSTRUCTORID WHERE U.USEREMAIL=:userEmail';
$query = $dbh->prepare($q);
$query->execute(array(':userEmail' => $userEmail));
$courseCount = 0;
if ($query->rowCount() == 0) {
    header('Location: index.php?err=1');
} else {
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $courseCount = $row['COURSE_COUNT'];
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
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link href='http://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
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
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="userhome.php">IAM Instructor Portal</a>
                </div>

                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="userhome.php">Home</a></li>
                        <li><a href="courses.php">Courses</a></li>
                        
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

                <div class="col-md-12 welcome-page">
                    <h2>Welcome, <?php echo $_SESSION['sess_username']; ?>.</h2>
                </div>
            </div>
            <div class="row spacer"></div>
            <div class="row spacer">
                <div class="col-lg-3"></div>
                <div class="col-lg-3 text-left">

                    <div class="circle-tile ">
                        <a href="#"><div class="circle-tile-heading dark-blue"><i class="fa fa-leanpub fa-fw fa-3x"></i></div></a>
                        <div class="circle-tile-content dark-blue">
                            <div class="circle-tile-description text-faded"> Courses</div>
                            <div class="circle-tile-number text-faded "><?php echo $courseCount ?></div>
                            <a class="circle-tile-footer" href="courses.php">Details <i class="fa fa-chevron-circle-right"></i></a>
                        </div>
                    </div>

                </div>
                <div class="col-lg-3 text-right">

                    <div class="circle-tile ">
                        <a href="#"><div class="circle-tile-heading purple"><i class="fa fa-users fa-fw fa-3x"></i></div></a>
                        <div class="circle-tile-content purple">
                            <div class="circle-tile-description text-faded"> Students</div>
                            <div class="circle-tile-number text-faded "><?php echo $studentCount ?></div>
                            <a class="circle-tile-footer" href="courses.php">Details <i class="fa fa-chevron-circle-right"></i></a>
                        </div>
                    </div>

                </div><div class="col-lg-3 text-right"></div>
            </div>

        </div>    

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>
