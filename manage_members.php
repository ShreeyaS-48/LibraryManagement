<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if((!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"]!=true) && $_SESSION["role"]=="staff"){
    header("location: member_login.php");
    exit;
}
$showError = false;
include 'partials/_dbconnect.php';
$sql = "SELECT COUNT(member_id) AS total_members FROM `member`";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$totmembers = $row['total_members'];

$sql = "SELECT COUNT(member_id) AS active_members FROM `member` NATURAL JOIN `member_status` WHERE account_status = 'Active'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$active_members = $row['active_members'];
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>View Members</title>
  </head>
  <body>
    <?php require 'partials/_nav.php'?>
    <h1 class="mt-4 text-center">Members</h1>
    
    <?php
        if($showError){
            echo '
            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </symbol>
            </svg>
            <div class="alert alert-danger alert-dismissible d-flex align-items-center fade show" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#exclamation-triangle-fill"/></svg>
            <div>'. htmlspecialchars($showError) .'</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div> ';
        }        
    ?>
    <div class="mt-4 text-center">
        <p class="mb-0">Total members: <strong><?php echo htmlspecialchars($totmembers); ?></strong></p>
        <p class="mb-0">Active members: <strong><?php echo htmlspecialchars($active_members); ?></strong></p>
        <a href="refresh_membership_status.php">Refresh member account status</a>
    </div>
    
    <?php
    $sql = "SELECT * FROM `member`";
$result = mysqli_query($conn, $sql);
if($result){
    $numExistsRows = mysqli_num_rows($result);
    if($numExistsRows > 0){
        echo '<div class="containter mt-4">';
        echo '<table class="table table-bordered table-striped">';
        echo '<thead class="table-dark">
                <tr>
                    <th scope="col">Member ID</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Mobile</th>
                    <th scope="col">Email</th>
                    <th scope="col">Membership Start</th>
                    <th scope="col">Membership End</th>
                    <th scope="col">Account Status</th>
                </tr>
            </thead>';
        echo '<tbody>';
        
            while ($row = mysqli_fetch_assoc($result)) {
                $active_status_id = $row['active_status_id'];
                $member_id = $row['member_id'];
                $statusSql = "SELECT account_status, membership_start_date, membership_end_date 
                      FROM `member_status`
                      WHERE active_status_id = $active_status_id";
                $statusResult = mysqli_query($conn, $statusSql);
                $statusRow =  mysqli_fetch_assoc($statusResult);
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['member_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['mobile_no']) . "</td>"; 
                echo "<td>" . htmlspecialchars($row['email_id']) . "</td>";
                echo "<td>" . htmlspecialchars($statusRow['membership_start_date']) . "</td>";
                echo "<td>" . htmlspecialchars($statusRow['membership_end_date']) . "</td>";
                echo "<td>" . htmlspecialchars($statusRow['account_status']) . "</td>";
                echo "</tr>";
        }

        echo '</tbody></table></div>';
    }
    else{
        $showError = "No books to display.";
    }
}
else{
    $showError = mysqli_error($conn);
}
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>