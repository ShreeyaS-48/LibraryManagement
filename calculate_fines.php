<?php
include 'partials/_dbconnect.php';

$finePerDay = 5;

$sql = "SELECT issue_id, member_id, return_date 
        FROM book_issue 
        WHERE return_date < CURDATE() AND return_status = 'Pending'";

$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $issue_id = $row['issue_id'];
    $member_id = $row['member_id'];
    $return_date = $row['return_date'];

    
    $daysOverdue = floor((strtotime(date('Y-m-d')) - strtotime($return_date)) / (60 * 60 * 24));
    $fine = $daysOverdue * $finePerDay;

    
    $checkSql = "SELECT * FROM fine_due WHERE issue_id = $issue_id";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        
        $updateSql = "UPDATE fine_due 
                      SET fine_total = $fine
                      WHERE issue_id = $issue_id";
        mysqli_query($conn, $updateSql);
    } else {
        
        $insertSql = "INSERT INTO fine_due (member_id, issue_id ,fine_total)
                      VALUES ($member_id, $issue_id, $fine)";
        mysqli_query($conn, $insertSql);
    }
}
header("location: collect_fine.php");
    exit;
?>
