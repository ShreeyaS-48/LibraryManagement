<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if((!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"]!=true) && $_SESSION["role"]!="member"){
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

    <title>Overdue Books</title>
  </head>
  <body>
    <?php require 'partials/_nav.php'?>
    <h1 class="mt-4 text-center">Books Overdue</h1>

<?php
$member_logged_in = $_SESSION['id'];
$today = date('Y-m-d');
$sql = "SELECT * FROM `book_issue` WHERE member_id = $member_logged_in AND return_status = 'Pending'AND return_date<'$today'";
$result = mysqli_query($conn, $sql);

if($result){
    $numExistsRows = mysqli_num_rows($result);
    if($numExistsRows > 0){
        echo '<div class="containter mt-4">';
        echo '<table class="table table-bordered table-striped">';
        echo '<thead class="table-dark">
                <tr>
                    <th scope="col">Book Title</th>
                    <th scope="col">Issue Date</th>
                    <th scope="col">Return Date</th>
                    <th scope="col">Fine</th>
                </tr>
            </thead>';
        echo '<tbody>';
        
            while ($row = mysqli_fetch_assoc($result)) {
            
                    $issue_id = $row['issue_id'];
                    $book_id = $row['book_id'];
                    $issue_date = $row['issue_date'];
                    $return_date = $row['return_date'];

                    $bookSql = "SELECT book_title FROM `book` WHERE book_id = $book_id";
                    $bookResult = mysqli_query($conn, $bookSql);
                    $bookRow = mysqli_fetch_assoc($bookResult);
                    $book_title = $bookRow['book_title'] ;

                    $fineSql = "SELECT fine_total FROM `fine_due` WHERE member_id = $member_logged_in AND issue_id = $issue_id AND fine_status = 'Pending'";
                    $fineResult = mysqli_query($conn, $fineSql);
                    $fineRow = mysqli_fetch_assoc($fineResult);
                    $fine = $fineRow['fine_total'] ;

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($book_title) . "</td>";
                    echo "<td>" . htmlspecialchars($issue_date) . "</td>";
                    echo "<td>" . htmlspecialchars($return_date) . "</td>";
                    echo "<td class='text-danger'>" . htmlspecialchars($fine) . "</td>";
                    echo "</tr>";
                
        }

        echo '</tbody></table></div>';
    }
    else{
        echo '
        <div class = "mt-4 text-center">
            <p>No books overdue.</p>
        </div>';
    }
}
else{
    $showError = mysqli_error($conn);
}
?>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>