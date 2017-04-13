<?php
   // define database related variables
   $database = 'iamdatabase';
   $host = 'localhost';
   $user = 'iamadminuser';
   $pass = 'iamadminuserPassword';
   $port = '3306';

   // try to conncet to database
   $dbh = new PDO("mysql:dbname={$database};host={$host};port={$port}", $user, $pass);

   if(!$dbh){
      echo "Unable to connect to the database.";
   }
   
?>