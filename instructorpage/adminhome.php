<?php
require 'database-config.php';
session_start();
$role = $_SESSION['sess_userrole'];

if ($role != "admin") {
    header('Location: index.php?err=2');
}

$q = 'SELECT COUNT(1) AS USER_COUNT FROM TBL_USERS U WHERE U.ACTIVE=0';
$query = $dbh->prepare($q);
$query->execute();
$inActiveUserCount = 0;
if ($query->rowCount() == 0) {
    header('Location: index.php?err=1');
} else {
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $inActiveUserCount = $row['USER_COUNT'];
}

$q = 'SELECT COUNT(1) AS USER_COUNT FROM TBL_USERS U';
$query = $dbh->prepare($q);
$query->execute();
$userCount = 0;
if ($query->rowCount() == 0) {
    header('Location: index.php?err=1');
} else {
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $userCount = $row['USER_COUNT'];
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>IAM Instructor Portal Admin</title>

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
                    <a class="navbar-brand" href="adminhome.php">IAM Instructor Portal</a>
                </div>

                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="adminhome.php">Home</a></li>
                        <li><a href="activateusers.php">Activate Instructors</a></li>
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
                <div class="col-md-6 welcome-page">
                    <h2>This is Admin area.</h2>
                </div>

            </div>
            <div class="row spacer">
                <div class="col-lg-3"></div>
                <div class="col-lg-3 text-right">

                    <div class="circle-tile ">
                        <a href="#"><div class="circle-tile-heading purple"><i class="fa fa-users fa-fw fa-3x"></i></div></a>
                        <div class="circle-tile-content purple">
                            <div class="circle-tile-description text-faded"> Total Users</div>
                            <div class="circle-tile-number text-faded "><?php echo $userCount ?></div>
                            <a class="circle-tile-footer" href="activateusers.php">Details <i class="fa fa-chevron-circle-right"></i></a>
                        </div>
                    </div>

                </div>
                <div class="col-lg-3 text-right">

                    <div class="circle-tile ">
                        <a href="#"><div class="circle-tile-heading purple"><i class="fa fa-user-times fa-fw fa-3x"></i></div></a>
                        <div class="circle-tile-content purple">
                            <div class="circle-tile-description text-faded"> Inactive Users</div>
                            <div class="circle-tile-number text-faded "><?php echo $inActiveUserCount ?></div>
                            <a class="circle-tile-footer" href="activateusers.php">Details <i class="fa fa-chevron-circle-right"></i></a>
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
