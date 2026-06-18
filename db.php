<?php
 // Needed to use $_SESSION

$servername = "localhost";
$username = "root";     // or your DB username
$password = "";         // or your DB password
$dbname = "service_booking";  // your database name

$conn =  mysqli_connect($servername, $username, $password, $dbname);

// if ($conn) {
//     echo"database successful";
// }
// else{
//  echo"database not create".mysqli_connect_error();   
// }
?>
