<?php
    session_start();
    if((!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"]!=true)){
        header("location: member_login.php");
        exit;
    }
    $showError = false;
    include 'partials/_dbconnect.php';
    $member_id = (int) $_SESSION['id'];
    $profileSql = "SELECT * FROM `member` WHERE member_id = $member_id";
    $profileResult = mysqli_query($conn, $profileSql);
    $profileRow = mysqli_fetch_assoc($profileResult);
    $email = $profileRow['email_id'];
    $mobile = $profileRow['mobile_no'];
    $active_status_id = $profileRow['active_status_id'];
    $membershipSql = "SELECT * FROM `member_status` WHERE active_status_id = $active_status_id";
    $membershipResult = mysqli_query($conn, $membershipSql);
    $membershipRow = mysqli_fetch_assoc($membershipResult);
    $membership_start_date = $membershipRow['membership_start_date'];
    $membership_end_date = $membershipRow['membership_end_date'];
    $account_status = $membershipRow['account_status'];
    if($account_status == "Expired"){
        $showError = "Membership Expired. Please Renew.";
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .box-card {
            height: 150px;
            transition: transform 0.3s ease, background-color 0.3s ease;
            cursor: pointer;
        }

        .box-card:hover {
            transform: scale(1.05);
        }

        .disabled-link {
            pointer-events: none;
            opacity: 0.6;
            text-decoration: none;
        }
    </style>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Dashboard</title>
    
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
    <h1 class="mt-4 text-center">Welcome <?php echo htmlspecialchars($_SESSION["first-name"]); ?></h1>
    <div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8"> 
        <div class="row g-4 justify-content-center text-center">
            <div class="col-md-5">
            <?php
                $statusClass = ($account_status === "Expired") ? "text-danger" : "text-success";
            ?>
            <p class="mb-0 <?php echo $statusClass; ?>">Account Status: <?php echo htmlspecialchars($account_status); ?></p>
            </div>
        </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8"> 
        <div class="row g-4 justify-content-center">
            <div class="col-md-5">
            <p class="mb-0">Mobile: <?php echo htmlspecialchars($mobile); ?></p>
            </div>
            <div class="col-md-5">
            <p class="mb-0">Email: <?php echo htmlspecialchars($email); ?></p>
            </div>
        </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8"> 
        <div class="row g-4 justify-content-center">
            <div class="col-md-5">
            <p class="mb-0">Membership Start: <?php echo htmlspecialchars($membership_start_date); ?></p>
            </div>
            <div class="col-md-5">
            <p class="mb-0">Membership End: <?php echo htmlspecialchars($membership_end_date); ?></p>
            </div>
        </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8"> 
        <div class="row g-4 justify-content-center text-center">
            <div class="col-md-5">
            <?php
                $isExpired = ($account_status === "Expired");
                $linkClass = $isExpired ? "text-primary" : "text-muted disabled-link";
                $linkHref = $isExpired ? "renew_membership.php" : "#";
                ?>

                <a href="<?php echo $linkHref; ?>" class="<?php echo $linkClass; ?>">
                Renew Membership
                </a>
            </div>
        </div>
        </div>
    </div>
    </div>
    <div class="container mt-4">
    <!-- Row 1 -->
        <div class="row g-4">
        <div class="col-md-6">
            <a href="view_books.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">Explore Books</h5>
                    </div>
                </div>
            </a>
            
            </div>
            <div class="col-md-6">
            <a href="issue_request.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">Request Book</h5>
                    </div>
                </div>
            </a>
            </div>
            
        </div>

    <!-- Row 2 -->
        <div class="row mt-4 g-4">
            
            <div class="col-md-6">
            <a href="member_books_issued.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">Books Issued</h5>
                    </div>
                </div>
            </a>
            </div>
            <div class="col-md-6">
            <a href="member_books_overdue.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">Books Overdue</h5>
                    </div>
                </div>
            </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>