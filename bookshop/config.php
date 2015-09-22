<?php
//include the classes
include_once( "class" . "/book.php" );
include_once( "class" . "/books.php" );
include_once( "class" . "/transaction.php" );
include_once( "class" . "/user.php" );

//set off all error for security purposes
error_reporting(0);

//define some contstant
define( "DB_DSN", "ACCESS_DENIED" ); //this constant will be use as our connectionstring/dsn
define( "DB_USERNAME", "ACCESS_DENIED" ); //username of the database
define( "DB_PASSWORD", "ACCESS_DENIED" ); //password of the database
define( "CLS_PATH", "class" ); //the class path of our project

?>