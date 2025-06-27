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
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        include 'partials/_dbconnect.php';
        $ISBN_Code = $_POST["ISBN_Code"] ?? '';
        $book_title = $_POST["book_title"]?? '';
        $category_id = $_POST["category_id"]?? '';
        $publisher_id = $_POST["publisher_id"]?? '';
        $author_id1 = $_POST["author_id1"]?? '';
        $author_id2 = $_POST["author_id2"]?? '';
        $copies = (int) ($_POST["copies"] ?? 0);

        $existsSql = "SELECT * FROM `book` WHERE ISBN_Code = '$ISBN_Code'";
        $result  =  mysqli_query($conn, $existsSql);
        $numExistsRows = mysqli_num_rows($result);
        if($numExistsRows > 0){
            $row = mysqli_fetch_assoc($result);
            $new_total = $row['copies_total'] + $copies;
            $new_available = $row['copies_available'] + $copies;
            $book_id = $row['book_id'];
            $updateSql = "UPDATE `book` SET `copies_total` = $new_total , `copies_available` = $new_available  WHERE `book_id` = $book_id";
            $result  =  mysqli_query($conn, $updateSql);
            if($result){
                $showAlert = "Records updated";
            }
            else{
                $showError = mysqli_error($conn);
            }
        }
        else {
            $sql = "INSERT INTO `book` (`ISBN_Code`, `book_title`, `category_id`, `publisher_id`, `copies_total`,`copies_available` ) VALUES ('$ISBN_Code', '$book_title', '$category_id', '$publisher_id', $copies, $copies)";
            $result  =  mysqli_query($conn, $sql);
            if ($result) {
                $book_id = mysqli_insert_id($conn); 
                $authorSql = "INSERT INTO `book_author` (`book_id`, `author_id`) VALUES('$book_id', '$author_id1')";
                $result = mysqli_query($conn, $authorSql);
                if($result && !empty($author_id2)){
                    $authorSql = "INSERT INTO `book_author` (`book_id`, `author_id`) VALUES('$book_id', '$author_id2')";
                    $result = mysqli_query($conn, $authorSql);
                }
                if($result){
                    $showAlert = "New book added";
                }
                else{
                    $showError = mysqli_error($conn);
                }
            } else {
                $showError = mysqli_error($conn);
            }

            
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

    <title>Add Books</title>
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
        <h1 class="text-center">Add Books</h1>
        <form action="/php_project/add_book.php" method="post">
            <div class="mb-3">
                <label for="ISBN_Code" class="form-label">ISBN Code</label>
                <input name="ISBN_Code" type="text" class="form-control" id="ISBN_Code" required>
            </div>
            <div class="mb-3">
                <label for="book_title" class="form-label">Book Title</label>
                <input name="book_title" type="text" class="form-control" id="book_title" required pattern="^[A-z][A-z0-9-_]{3,23}$">
            </div>
            <div class="mb-3">
                <select class="form-select" name="category_id" required>
                    <option value="" disabled selected>Select a category</option>
                    <option value="1">Mystery</option>
                    <option value="2">Horror</option>
                    <option value="3">Classics</option>
                    <option value="4">Kids Books</option>
                    <option value="5">Short Stories</option>
                    <option value="6">Science Fiction</option>
                    <option value="7">Biography</option>
                </select>
            </div>
            <div class="mb-3">
                <select class="form-select" name="publisher_id" required>
                    <option value="" disabled selected>Select a publisher</option>
                    <option value="1">HarperCollins</option>
                    <option value="2">Scribner</option>
                    <option value="3">Penguin Classics</option>
                    <option value="4">Puffin Books</option>
                    <option value="5">Simon & Schuster</option>
                </select>
            </div>
            <div class="mb-3">
                <select class="form-select" name="author_id1" required>
                    <option value="" disabled selected>Select the author</option>
                    <option value="1">Agatha Christie</option>
                    <option value="2">Stephen King</option>
                    <option value="3">Jane Austen</option>
                    <option value="4">Charles Dickens</option>
                    <option value="5">Roald Dahl</option>
                    <option value="6">Ernest Hemingway</option>
                    <option value="7">James Joyce</option>
                    <option value="8">H.G. Wells</option>
                    <option value="9">Aldous Huxley</option>
                    <option value="10">Walter Isaacson</option>
                </select>
            </div>
            <div class="mb-3">
                <select class="form-select" name="author_id2">
                    <option value="" disabled selected>Select second author(optional)</option>
                    <option value="1">Agatha Christie</option>
                    <option value="2">Stephen King</option>
                    <option value="3">Jane Austen</option>
                    <option value="4">Charles Dickens</option>
                    <option value="5">Roald Dahl</option>
                    <option value="6">Ernest Hemingway</option>
                    <option value="7">James Joyce</option>
                    <option value="8">H.G. Wells</option>
                    <option value="9">Aldous Huxley</option>
                    <option value="10">Walter Isaacson</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="copies" class="form-label">Copies</label>
                <input name="copies" type="number" class="form-control" id="copies" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Books</button>
            </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>