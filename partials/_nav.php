<?php

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
    $loggedin = true;
} else {
    $loggedin = false;
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Library</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
      aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <?php
          if (isset($_SESSION["role"]) && $_SESSION["role"] == "staff") {
              echo '<a class="nav-link active" aria-current="page" href="/php_project/staff_dashboard.php">Home</a>';
          } else {
              echo '<a class="nav-link active" aria-current="page" href="/php_project/member_dashboard.php">Home</a>';
          }
        ?>
      </div>

      <?php if (!$loggedin): ?>
        <div class="navbar-nav">
          <a class="nav-link active" href="/php_project/member_login.php">Login</a>
        </div>
        <div class="navbar-nav">
          <a class="nav-link active" href="/php_project/signup.php">Sign Up</a>
        </div>
      <?php else: ?>
        <div class="navbar-nav">
          <a class="nav-link active" href="/php_project/logout.php">Logout</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>
