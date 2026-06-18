<?php
 // Needed to use $_SESSION

$servername = "localhost";
$username = "root";     // or your DB username
$password = "";         // or your DB password
$dbname = "service_booking";  // your database name

$conn =  mysqli_connect($servername, $username, $password, $dbname);
if ($conn) {
    $conn->set_charset('utf8mb4');
    $conn->query("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        description VARCHAR(255) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// if ($conn) {
//     echo"database successful";
// }
// else{
//  echo"database not create".mysqli_connect_error();   
// }
?>
