<!DOCTYPE html>
<html lang="en">
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
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!-- Where all the magic happens -->
        <!-- LOGIN FORM -->
        <div class="text-center" style="padding:50px 0">
            <div class="logo">IAM Instructor Portal</div>
            <!-- Main Form -->
            <div class="login-form-1">

                <div class="login-form-main-message <?php
                if (isset($_GET['err'])) {
                    ?>show<?php
                     }
                     ?>">
                         <?php
                         $errors = array(
                             1 => "Invalid user name or password, Try again",
                             2 => "Please login to access this area",
                             98 => "User account inactive. Wait for activation before logging in.",
                             99 => "System Error. Please contact system administrator."
                         );

                         $error_id = isset($_GET['err']) ? (int) $_GET['err'] : 0;
                         echo '<div class="form-group" style="padding: 20px 0 0 0;">
                                <div class="alert alert-danger">
                                    <span class="glyphicon glyphicon-info-sign"></span> ';

                         if ($error_id == 1 || $error_id == 2 || $error_id == 99 || $error_id == 98) {
                             echo $errors[$error_id];
                         }

                         echo '</div>
                            </div>';
                         ?>  
                </div>
                <form action="authenticate.php" method="POST" class="form-signin" id="login-form" role="form">  
                    <div class="main-login-form">
                        <div class="login-group">
                            <div class="form-group">
                                <label for="userEmail" class="sr-only">User Email</label>
                                <input type="text" class="form-control" id="userEmail" name="userEmail" autocomplete="off" placeholder="User Email">
                            </div>
                            <div class="form-group">
                                <label for="password" class="sr-only">Password</label>
                                <input type="password" class="form-control" id="password" name="password" autocomplete="off" placeholder="Password">
                            </div>

                        </div>
                        <button type="submit" name="submit" id="submit" class="login-button btn btn-default"><i class="fa fa-chevron-right"></i></button>
                    </div>

                </form>
                <div class="etc-login-form">
                    <p> </p>
                    <p>New Users - <a href="signup.php">Click Here!</a></p>
                </div>
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