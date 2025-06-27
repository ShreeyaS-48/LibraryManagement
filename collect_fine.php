<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if((!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"]!=true) && $_SESSION["role"]!="staff"){
    header("location: member_login.php");
    exit;
}
include 'partials/_dbconnect.php';
$showAlert = false;
$showError = false;
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['paid_fines'])){
        $staff_id = $_SESSION['id'];
        $member_id = (int) $_POST['member_id'];
        $paid_fines = $_POST['paid_fines'];
        $payment_date = date('Y-m-d');
        foreach ($paid_fines as $fine_id){
            $fine_id = (int)$fine_id;
            $sql = "SELECT fine_total FROM fine_due WHERE fine_id = $fine_id";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $amount = $row['fine_total'];
            $insert = "INSERT INTO fine_payment (member_id, fine_id, payment_date, payment_amount, collected_by_id)
                   VALUES ($member_id, $fine_id, '$payment_date', $amount, $staff_id)";
            mysqli_query($conn, $insert);
            $update = "UPDATE fine_due SET fine_status = 'Paid' WHERE fine_id = $fine_id";
            mysqli_query($conn, $update);
            $showAlert = "Fines paid.";
        }
    }

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Collect Fine</title>
  </head>
  <body>
    <?php require 'partials/_nav.php'?>
    <?php
        if($showAlert){
            echo '
            <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </symbol>
            </svg>
            <div class="alert alert-success alert-dismissible d-flex align-items-center fade show" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
            <div>'. htmlspecialchars($showAlert) .'</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div> ';
        }
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
    <div class="container my-4">
        <h1 class="mt-4 text-center">Collect Fine</h1>
        <div class="mt-4 text-center">
            <a href="calculate_fines.php">Refresh fines</a>
        </div>
        <form method="post">
        <div class="mb-3">
                <label for="email" class="form-label">Member Email</label>
                <input name="member_email" type="email" class="form-control" id="email" required pattern="^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$">
        </div>
        <button type="submit" class="btn btn-primary">View Fine Details</button>
        </form>
    </div>
    <?php
    // If member ID submitted, show their unpaid fines
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_email']) && !isset($_POST['paid_fines'])) {
        $member_email = $_POST['member_email'];
        $memberSql ="SELECT * FROM member WHERE email_id = '$member_email'";
        $memberResult = mysqli_query($conn, $memberSql);
        $num = mysqli_num_rows($memberResult);
        if($num>0){
            $memberRow = mysqli_fetch_assoc($memberResult);
            $member_id = $memberRow['member_id'];
            
            $fineSql = "SELECT fd.* 
            FROM fine_due fd
            JOIN book_issue bi ON fd.issue_id = bi.issue_id
            WHERE fd.member_id = $member_id 
              AND fd.fine_status = 'Pending'
              AND bi.return_status = 'Returned'";
            $fineResult = mysqli_query($conn, $fineSql);
            if (mysqli_num_rows($fineResult) > 0) {
                echo "<form method='POST' class='container mt-4'>";
                echo "<input type='hidden' name='member_id' value='$member_id'>";
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead class='table-dark'>
                    <tr>
                        <th>Select</th>
                        <th>Issue ID</th>
                        <th>Book Title</th>
                        <th>Fine Amount</th>
                    </tr>
                </thead>
                <tbody>";
    
                while ($row = mysqli_fetch_assoc($fineResult)) {
                    $issue_id = $row['issue_id'];
                    $bookSql = "SELECT book_title FROM `book` WHERE book_id = (SELECT book_id FROM `book_issue` WHERE issue_id = '$issue_id')";
                    $bookResult = mysqli_query($conn, $bookSql);
                    $bookRow = mysqli_fetch_assoc($bookResult);
            
                    echo "<tr>
                            <td><input type='checkbox' name='paid_fines[]' value='{$row['fine_id']}'></td>

                            <td>{$row['issue_id']}</td>
                            <td>{$bookRow['book_title']}</td>
                            <td>₹{$row['fine_total']}</td>
                        </tr>";
                }
    
                echo "</tbody></table>";
                echo "<button type='submit' class='btn btn-primary'>Collect Selected Fines</button>";
                echo "</form>";
            } else {
                $fineSql = "SELECT * FROM fine_due WHERE member_id = $member_id AND fine_status = 'Pending'";
                $fineResult = mysqli_query($conn, $fineSql);
                $num = mysqli_num_rows($fineResult);
                if($num>0){
                    echo "<div class='container mt-3 alert alert-danger'>Return books to pay your fines.</div>";
                }
                else{
                    echo "<div class='container mt-3 alert alert-sucess'>No unpaid fines.</div>";
                }
                
            }
        }
        else{
            echo "<div class='container mt-3 alert alert-danger'>Member not found.</div>";
        }   
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>