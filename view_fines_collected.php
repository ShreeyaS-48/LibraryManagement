<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if((!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"]!=true) && $_SESSION["role"]!="staff"){
    header("location: member_login.php");
    exit;
}
$showError = false;
include 'partials/_dbconnect.php';
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Fines Collected</title>
  </head>
  <body>
    <?php require 'partials/_nav.php'?>
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
    <h1 class="mt-4 text-center">Fines Collected</h1>
    <div class="mt-4 text-center">
        <?php
            $fineSql = "SELECT SUM(payment_amount) AS total_fine FROM `fine_payment`";
            $fineResult = mysqli_query($conn, $fineSql);
            $fineRow = mysqli_fetch_assoc($fineResult);
            $total_fine = $fineRow['total_fine'];
            echo "<p>Total fine collected: <strong>₹". htmlspecialchars($total_fine) ."</strong></p>"
        ?>
    </div>
<?php
$sql = "SELECT * FROM `fine_payment`";
$result = mysqli_query($conn, $sql);
if($result){
    $numExistsRows = mysqli_num_rows($result);
    if($numExistsRows > 0){
        echo '<div class="containter mt-4">';
        echo '<table class="table table-bordered table-striped">';
        echo '<thead class="table-dark">
                <tr>
                    <th scope="col">Payment Id</th>
                    <th scope="col">Member Id</th>
                    <th scope="col">Member Name</th>
                    <th scope="col">Staff Id</th>
                    <th scope="col">Payment Amount</th>
                    <th scope="col">Payment Date</th>
                </tr>
            </thead>';
        echo '<tbody>';
        
            while ($row = mysqli_fetch_assoc($result)) {
                $fine_payment_id = $row['fine_payment_id'];
                $member_id = $row['member_id'];
                $collected_by_id = $row['collected_by_id'];
                $payment_amount = $row['payment_amount'];
                $payment_date = $row['payment_date'];
                
                $memberSql = "SELECT first_name, last_name FROM `member` WHERE member_id = $member_id";
                $memberResult = mysqli_query($conn, $memberSql);
                $memberRow = mysqli_fetch_assoc($memberResult);
                $memberName = $memberRow['first_name'] . ' ' . $memberRow['last_name'];
                    
                echo "<tr>";
                echo "<td>" . htmlspecialchars($fine_payment_id) . "</td>";
                echo "<td>" . htmlspecialchars($member_id) . "</td>";
                echo "<td>" . htmlspecialchars($memberName) . "</td>";
                echo "<td>" . htmlspecialchars($collected_by_id) . "</td>";
                echo "<td>" . htmlspecialchars($payment_amount) . "</td>"; 
                echo "<td>" . htmlspecialchars($payment_date) . "</td>";
                
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