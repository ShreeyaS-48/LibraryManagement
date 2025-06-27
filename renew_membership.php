<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if((!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"]!=true) && $_SESSION["role"]!="member"){
    header("location: member_login.php");
    exit;
}
include 'partials/_dbconnect.php';
$member_id = $_SESSION['id'];
$sql = "SELECT * FROM `member` WHERE member_id = $member_id";
$result = mysqli_query($conn, $sql);
if($result){
    $row = mysqli_fetch_assoc($result);
    $today = date('Y-m-d');
    $expiry = date('Y-m-d', strtotime('+1 year'));
    $accStatus = "Active";
    $active_status_id = $row['active_status_id'];
    $updateSql = "UPDATE `member_status` SET membership_start_date = '$today', membership_end_date = '$expiry' , account_status = '$accStatus' WHERE active_status_id = $active_status_id";
    $result = mysqli_query($conn, $updateSql);
}
header("location: member_dashboard.php");
exit;
?>
