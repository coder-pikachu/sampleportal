<?php
ob_start();
session_start();
if (isset($_SESSION['sess_userEmail'])) {
    header('Location: index.php?err=2');
}
require 'database-config.php';

$error = false;

if (isset($_POST['btn-signup'])) {
// clean user inputs to prevent sql injections
    $name = trim($_POST['name']);
    $name = strip_tags($name);
    $name = htmlspecialchars($name);

    $email = trim($_POST['email']);
    $email = strip_tags($email);
    $email = htmlspecialchars($email);

    $pass = trim($_POST['pass']);
    $pass = strip_tags($pass);
    $pass = htmlspecialchars($pass);

// basic name validation
    if (empty($name)) {
        $error = true;
        $nameError = "Please enter your full name.";
    } else if (strlen($name) < 3) {
        $error = true;
        $nameError = "Name must have atleat 3 characters.";
    } else if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $error = true;
        $nameError = "Name must contain alphabets and space.";
    }

//basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $emailError = "Please enter valid email address.";
    } else {
// check email exist or not
        $q = "SELECT userEmail FROM tbl_users WHERE userEmail=:email";
        $emailQuery = $dbh->prepare($q);
        $emailQuery->execute(array(':email' => $email));

        if ($emailQuery->rowCount() != 0) {
            $error = true;
            $emailError = "Provided Email is already in use.";
        }
    }
// password validation
    if (empty($pass)) {
        $error = true;
        $passError = "Please enter password.";
    } else if (strlen($pass) < 6) {
        $error = true;
        $passError = "Password must have atleast 6 characters.";
    }

// password encrypt using SHA256();
    $password = hash('sha256', $pass);

// if there's no error, continue to signup
    if (!$error) {
        //INSERT INTO `tbl_users` (`id`, `username`, `name`, `userEmail`, `password`, `role`, `active`) VALUES (NULL, '', '', '', '', '', '0');
        $q = "INSERT INTO tbl_users(name,userEmail,password) VALUES(:name,:email,:password)";
        $signupQuery = $dbh->prepare($q);
        $res = $signupQuery->execute(array(':name' => $name, ':email' => $email, ':password' => $password));
        error_log($res);
        if ($res) {
            $errTyp = "success";
            $errMSG = "Successfully registered, Please wait for account activation before logging in.";
            unset($name);
            unset($email);
            unset($pass);
        } else {
            $errTyp = "danger";
            $errMSG = "Something went wrong, try again later...";
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>IAM Instructor Portal</title>

        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
         <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link href='http://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>

        <!-- REGISTRATION FORM -->
        <div class="text-center" style="padding:50px 0">
            <div class="logo">Sign Up</div>
            <!-- Main Form -->
            <div class="login-form-1">
                <form class="text-left" method="POST" autocomplete="off">
                    <div class="login-form-main-message <?php
                        if (isset($errMSG)) {
                            ?>show<?php
                        }
                        ?>">
                        <?php
                        if (isset($errMSG)) {
                            ?>
                        <div class="form-group" style="padding: 20px 0 0 0;">
                                <div class="alert alert-<?php echo ($errTyp == "success") ? "success" : $errTyp; ?>">
                                    <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="main-login-form">
                        <div class="login-group">
                            <div class="form-group">
                                <label for="name" class="sr-only">Full Name</label>
                                <input type="text" class="form-control" id="name" autocomplete="off" name="name" placeholder="Full name">
                                <span class="text-danger"><?php echo $nameError; ?></span>
                            </div>
                            <div class="form-group">
                                <label for="email" class="sr-only">Email</label>
                                <input type="text" class="form-control" id="email" autocomplete="off" name="email" placeholder="Email">
                                <span class="text-danger"><?php echo $emailError; ?></span>
                            </div>
                            <div class="form-group">
                                <label for="pass" class="sr-only">Password</label>
                                <input type="password" class="form-control" id="pass" autocomplete="off" name="pass" placeholder="Password">
                                <span class="text-danger"><?php echo $passError; ?></span>
                            </div>
                        </div>
                        <button type="submit" name="btn-signup" class="login-button"><i class="fa fa-chevron-right"></i></button>
                    </div>
                    <div class="etc-login-form">
                        <p>Already have an account? <a href="index.php">Sign in here.</a></p>
                    </div>
                </form>
            </div>
            <!-- end:Main Form -->
        </div>
       <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
    </body>
</html>
<?php ob_end_flush(); ?>