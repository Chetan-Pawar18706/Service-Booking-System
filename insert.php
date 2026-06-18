<?php
include 'db.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Insert Data</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="card" style="max-width:500px;margin:30px auto">
        <h2 class="h1">Add Record</h2>
        <?php 
        $msg = '';
        if(isset($_POST['btnsub'])){
            $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $number = isset($_POST['number']) ? trim($_POST['number']) : '';
            
            if (!empty($firstname) && !empty($email) && !empty($number)) {
                $stmt = $conn->prepare("INSERT INTO admin (firstname, email, number) VALUES (?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param('sss', $firstname, $email, $number);
                    if ($stmt->execute()) {
                        $msg = '<div class="alert success">Data saved successfully!</div>';
                    } else {
                        $msg = '<div class="alert error">Error: ' . htmlspecialchars($conn->error) . '</div>';
                    }
                    $stmt->close();
                }
            } else {
                $msg = '<div class="alert error">Please fill all fields.</div>';
            }
        }
        echo $msg;
        ?>
        <form method="post" class="form">
            <label class="small">First Name</label>
            <input type="text" name="firstname" placeholder="firstname" required>
            <label class="small">Email</label>
            <input type="email" name="email" placeholder="email" required>
            <label class="small">Number</label>
            <input type="text" name="number" placeholder="number" required>
            <div style="margin-top:14px"><button type="submit" name="btnsub" class="btn" value="save">Save</button></div>
        </form>
    </div>
</div>
</body>
</html>