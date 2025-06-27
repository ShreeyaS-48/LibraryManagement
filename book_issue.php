<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if((!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"]!=true) && $_SESSION["role"]!="staff"){
    header("location: member_login.php");
    exit;
}
$showAlert = false;
$showError = false;
include 'partials/_dbconnect.php';
$bookSql = "SELECT book_id, book_title FROM book";
$bookResult = mysqli_query($conn, $bookSql);
$staff_id =  (int) $_SESSION['id'];
if($_SERVER["REQUEST_METHOD"] == "POST"){
        $book_id = $_POST['book_id']; 
        
        $member_email = $_POST['member_email'];
        $memberSql ="SELECT * FROM member WHERE email_id = '$member_email'";
        $memberResult = mysqli_query($conn, $memberSql);
        $memberRow = mysqli_fetch_assoc($memberResult);
        $active_status_id = $memberRow['active_status_id'];
        $member_id = $memberRow['member_id'];
        if($memberResult){
            $accountStatusSql = "SELECT * FROM `member_status` WHERE active_status_id = $active_status_id";
            $accountStatusResult = mysqli_query($conn, $accountStatusSql);
            $accountStatusRow = mysqli_fetch_assoc($accountStatusResult);
            $return_date = date('Y-m-d', strtotime('+1 month'));
            if($accountStatusRow['account_status'] == "Active" && $accountStatusRow['membership_end_date']>$return_date){
                
                $checkCountSql = "SELECT * FROM `book_issue` WHERE member_id = $member_id AND return_status = 'Pending'";
                $checkCountResult = mysqli_query($conn, $checkCountSql);
                $numExistsRows = mysqli_num_rows($checkCountResult);
                if($numExistsRows < 3){
                    $checkBookSql = "SELECT * FROM `book_issue` WHERE member_id = $member_id AND book_id = $book_id AND return_status = 'Pending'";
                $checkBookResult = mysqli_query($conn, $checkBookSql);
                $numExistsRows = mysqli_num_rows($checkBookResult);
                if($numExistsRows == 0){
                    $bookAvailableSql = "SELECT copies_available FROM `book` WHERE book_id = $book_id";
                    $bookAvailableResult = mysqli_query($conn, $bookAvailableSql);
                    $bookAvailableRow = mysqli_fetch_assoc($bookAvailableResult);
                    $copies_available = $bookAvailableRow['copies_available'];
                    if($copies_available>0){
                        $issue_date = date('Y-m-d');
                        $bookIssueSql = "INSERT INTO `book_issue` (`book_id`, `member_id` , `issue_date`, `return_date`, `issued_by_id`) VALUES ('$book_id', '$member_id', '$issue_date', '$return_date', '$staff_id')";
                        $bookIssueResult = mysqli_query($conn, $bookIssueSql);
                        $new_copies_available = $copies_available - 1;
                        $updateCopiesSql = "UPDATE `book` SET copies_available = '$new_copies_available' WHERE book_id = $book_id";
                        $updateCopiesResult= mysqli_query($conn, $updateCopiesSql);
                        if($bookIssueResult && $updateCopiesResult){
                            $showAlert = "Book Issued! Return by ". $return_date; 
                        }
                        else{
                            $showError = mysqli_error($conn);
                        }
                    }
                    else{
                        $showError = "Book Unavailable";
                    }
                }
                else{
                    $showError = "Cannot issue same book again.";
                }
                }
                else{
                    $showError = "Cannot issue more than 3 books.";
                }
                

                
            }
            else{
                if($accountStatusRow['account_status'] != "Active"){
                    $showError = "Membership Expired.";
                }
                else{
                    $showError = "Extentd membership to issue book.";
                }
            }
        }
        else{
            $showError = "Not a member.";
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

    <title>Issue Book</title>
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
        <h1 class="text-center">Issue a Book</h1>
        <form action="/php_project/book_issue.php" method="post">
        <div class="mb-3">
                <label for="email" class="form-label">Member Email</label>
                <input name="member_email" type="email" class="form-control" id="email" required pattern="^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$">
        </div>
        <div class="mb-3">
            <select class="form-select" name="book_id" id="book_id" required>
                <option value="" selected disabled>Choose a book</option>
                <?php while ($row = mysqli_fetch_assoc($bookResult)) { ?>
                    <option value="<?= $row['book_id'] ?>">
                        <?= htmlspecialchars($row['book_title']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        
            <button type="submit" class="btn btn-primary">Issue Book</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>