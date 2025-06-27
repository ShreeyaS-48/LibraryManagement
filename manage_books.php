<?php
    session_start();
    if((!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"]!=true) && $_SESSION["role"]=="staff"){
        header("location: member_login.php");
        exit;
    }
    include 'partials/_dbconnect.php';
    $sql = "SELECT c.category_name, COUNT(b.book_id) AS total_books
        FROM category c
        LEFT JOIN book b ON c.category_id = b.category_id
        GROUP BY c.category_name";

    $result = mysqli_query($conn, $sql);

    $categories = [];
    $bookCounts = [];

    while($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row['category_name'];
        $bookCounts[] = $row['total_books'];
    }

    $sql = "SELECT SUM(copies_total) AS total_books FROM `book`";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $totbooks = $row['total_books'];
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .box-card {
            height: 100px;
            transition: transform 0.3s ease, background-color 0.3s ease;
            cursor: pointer;
        }

        .box-card:hover {
            transform: scale(1.05);
        }
    </style>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Manage Books</title>
    
  </head>
  <body>
    <?php require 'partials/_nav.php'?>

    <div class="container mt-4">
    <!-- Row 1 -->
        <div class="row g-4">

            <div class="col-md-6">
                <a href="add_book.php" class="text-decoration-none">
                    <div class="card box-card bg-dark text-white text-center shadow">
                        <div class="card-body d-flex align-items-center justify-content-center">
                        <h5 class="card-title">Add Books</h5>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6">
                <a href="delete_book.php" class="text-decoration-none" >
                    <div class="card box-card bg-dark text-white text-center shadow">
                        <div class="card-body d-flex align-items-center justify-content-center">
                        <h5 class="card-title">Remove Books</h5>
                    </div>
                    </div>
                </a>
            </div>

        </div>
        <div class="row mt-4 g-4">
            <div class="col-12">
                <a href="view_books.php" class="text-decoration-none">
                <div class="card box-card bg-dark text-white text-center shadow">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <h5 class="card-title mb-2">View All Books</h5>
                        <p class="mb-0">Total books in the library: <strong><?php echo htmlspecialchars($totbooks); ?></strong></p>
                    </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="container my-4">
            <h4 class="text-center">Books by Category</h4>
            <canvas id="booksChart" height="100"></canvas>
        </div>
        <script>
    const ctx = document.getElementById('booksChart').getContext('2d');

    const booksChart = new Chart(ctx, {
        type: 'bar', // You can change this to 'pie'
        data: {
            labels: <?php echo json_encode($categories); ?>,
            datasets: [{
                label: 'Books per Category',
                data: <?php echo json_encode($bookCounts); ?>,
                backgroundColor: [
                    '#007bff', '#dc3545', '#28a745', '#ffc107', '#6f42c1',
                    '#fd7e14', '#20c997', '#0dcaf0', '#6610f2', '#198754'
                ],
                borderColor: '#ffffff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Number of Books in Each Category'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Number of Books'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Categories'
                    }
                }
            }
        }
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>