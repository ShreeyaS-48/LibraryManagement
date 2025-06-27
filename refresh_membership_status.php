<?php
include 'partials/_dbconnect.php';
$sql = "SELECT * FROM `member_status`";
$result = mysqli_query($conn, $sql);
if ($result) {
    $numExistsRows = mysqli_num_rows($result);
    if ($numExistsRows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $today = date('Y-m-d');
            if ($today > $row['membership_end_date']) {
                $active_status_id = $row['active_status_id'];
                $updateSql = "UPDATE `member_status` SET account_status = 'Expired' WHERE active_status_id = $active_status_id";
                mysqli_query($conn, $updateSql); 
            }
        }
    }
}
header("location: manage_members.php");
    exit;
?>