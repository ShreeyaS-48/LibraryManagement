<?php
    session_start();
    if((!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"]!=true) && $_SESSION["role"]!="staff"){
        header("location: member_login.php");
        exit;
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
            height: 125px;
            transition: transform 0.3s ease, background-color 0.3s ease;
            cursor: pointer;
        }

        .box-card:hover {
            transform: scale(1.05);
        }
    </style>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Dashboard</title>
    
  </head>
  <body>
    <?php require 'partials/_nav.php'?>
    <h1 class="mt-4 text-center">Welcome <?php echo htmlspecialchars($_SESSION["first-name"]); ?></h1>
    <div class="container mt-4">
    <!-- Row 1 -->
        <div class="row g-4">
            <div class="col-md-4">
            <a href="manage_books.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">Manage Books</h5>
                    </div>
                </div>
            </a>
            </div>
            <div class="col-md-4">
                <a href="manage_members.php" class="text-decoration-none">
                    <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">Manage Members</h5>
                    </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
            <a href="view_all_issued_books.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">View All Issued Books</h5>
                    </div>
                </div>
            </a>
            </div>
        </div>

    <!-- Row 2 -->
        <div class="row mt-4 g-4">
            <div class="col-md-4">
            <a href="book_issue.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">Issue Book</h5>
                    </div>
                </div>
            </a>
            </div>
            <div class="col-md-4">
            <a href="book_return.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">Return Book</h5>
                    </div>
                </div>
            </a>
            </div>
            <div class="col-md-4">
            <a href="view_all_overdue_books.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">View All Overdue Books</h5>
                    </div>
                </div>
            </a>
            </div>
        </div>
        <div class="row mt-4 g-4">
            <div class="col-md-6">
            <a href="collect_fine.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">Collect Fine</h5>
                    </div>
                </div>
            </a>
            </div>
            <div class="col-md-6">
            <a href="view_fines_collected.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex align-items-center justify-content-center">
                    <h5 class="card-title fs-3">View Fines Paid</h5>
                    </div>
                </div>
            </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>