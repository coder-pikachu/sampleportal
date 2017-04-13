<?php
require 'database-config.php';
require 'Errors.php';
session_start();
$role = $_SESSION['sess_userrole'];
$PAGE_SIZE = 10;
if ($role != "admin") {
    header('Location: index.php?err=2');
}
$currentPage = 1;
$q = 'SELECT COUNT(1) AS USER_COUNT FROM TBL_USERS U';
$query = $dbh->prepare($q);
$query->execute();
$userCount = 0;
if ($query->rowCount() == 0) {
    header('Location: index.php?err=1');
} else {
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $userCount = $row ['USER_COUNT'];
}
$totalPages = floor($userCount / $PAGE_SIZE) + 1;
$currentLoggedInUserId = $_SESSION['sess_user_id'];

function getUsers($pageNo, $dbh, $PAGE_SIZE,$currentLoggedInUserId) {
    $start = ($pageNo - 1) * $PAGE_SIZE;
    // $end = ($pageNo - 1) * $PAGE_SIZE + ($PAGE_SIZE - 1);
    error_log($start . " -- ");
    $q = 'SELECT `id`, `name`, `userEmail`, `active` FROM `TBL_USERS` U WHERE U.id!=:currentLoggedInUserId ORDER BY U.name LIMIT ' . $PAGE_SIZE . ' OFFSET ' . $start;
    error_log($q);
    $query = $dbh->prepare($q);
    $query->execute(array(currentLoggedInUserId => $currentLoggedInUserId));
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
                        <li><a href="adminhome.php">Home</a></li>
                        <li class="active"><a href="activateusers.php">Activate Instructors</a></li>
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
                <div class="col-md-12 text-center">
                    <h3>Total Registered Users: <span style='color: #71daff'><?php echo $userCount - 1 ?></span> </h3>
                </div>
                <div class="col-md-3"></div>
            </div>
            <div class="row" id="msgBox">
                <div class="col-md-12">
                    <?php
                    $errObjTxt = (string) filter_input(INPUT_GET, 'o');
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
                                <th>Email</th>
                                <th>Is Active?</th>
                            </tr>
                        <thead>
                        <tbody>
                            <?php
                            $rows = getUsers($currentPage, $dbh, $PAGE_SIZE,$currentLoggedInUserId);
                            foreach ($rows as $row) {
                                ?>
                                <tr _id="<?php echo $row['id'] ?>">
                                    <td><?php echo $row['name'] ?></td>
                                    <td><?php echo $row['userEmail'] ?></td>
                                    <td class="text-center"><?php
                                        if ($row['active']) {
                                            echo '<i class="fa fa-check-circle-o fa-2x text-green" aria-hidden="true"></i>';
                                        } else {
                                            echo '<i class="fa fa-times-circle-o fa-2x text-red" aria-hidden="true"></i>';
                                        }
                                        ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <ul class="pagination">
                        <li <?php echo (($currentPage == 1) ? 'class="disabled"' : '') ?>><a
                            <?php echo ($currentPage != 1) ? 'href="activateusers.php?page=' . ($currentPage - 1) . '"' : '' ?>
                                aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
                            <?php
                            for ($index = 1; $index <= $totalPages; $index ++) {
                                echo '<li ' . (($index == $currentPage) ? 'class="active"' : '') . '><a href = "activateusers.php?page=' . $index . '">' . $index . '</a></li>';
                            }
                            ?>
                        <li
                            <?php echo (($currentPage == $totalPages) ? 'class="disabled"' : '') ?>><a
                                <?php echo ($currentPage != $totalPages) ? 'href="activateusers.php?page=' . ($currentPage + 1) . '"' : '' ?>
                                aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 pad-top-sm">
                    <button type="button" class="btn btn-success" data-toggle="modal"
                            id="btnActivateUser"  ><i class="fa fa-check-circle-o" aria-hidden="true"></i> Activate</button>
                    <button type="button" class="btn btn-danger" id="btnDeActivateUser"><i class="fa fa-times-circle-o" aria-hidden="true"></i> Deactivate</button>

                </div>

            </div>
        </div>
    </div>    
    <form id="frmActivateUser" action="activateuser.php" method="POST" style="display:none;">
        <input name="actUserId" type="hidden" id="actUserId"/>
        <input name="actPageNo" type="hidden" id="actPageNo" value="<?php echo $currentPage ?>"/>
        <input name="actUserActivate" type="hidden" id="actUserActivate"/>
        <button type="submit"  name="btnActUser" id="btnActUser"/>
    </form>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/activateusers.js" type="text/javascript"></script>
</body>
</html>
