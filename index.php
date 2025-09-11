<?php
session_start();
$loggedIn = $_SESSION['logged_in'] ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<!-- Fomantic UI CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui/dist/semantic.min.css">

<!-- Night mode CSS, initially inactive -->
<link rel="stylesheet" href="styles.css" id="nightModeCSS" media="none">




<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/fomantic-ui/dist/semantic.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>

<div class="ui top attached menu">
  <a class="item active" data-tab="total">Total Bild</a>
  <a class="item" data-tab="users">Användare</a>
  <a class="item" data-tab="logs">Logs</a>

  <div class="right menu">
    <?php if(!$loggedIn): ?>
      <a class="item" id="loginBtn">Login</a>
      <a class="item" id="registerBtn">Register</a>
    <?php else: ?>
      <span class="item">Hello, <?=htmlspecialchars($_SESSION['username'])?></span>
      <a class="item" id="logoutBtn">Logout</a>
    <?php endif; ?>
        <div class="item">
        <div class="ui toggle button" id="nightModeToggle">Night Mode</div>
    </div>
  </div>
</div>

<?php if($loggedIn): ?>
<div class="ui bottom attached tab segment nav-section active" data-tab="total"><?php include 'categories.php'; ?></div>
<div class="ui bottom attached tab segment nav-section" data-tab="users"><?php include 'users.php'; ?></div>
<div class="ui bottom attached tab segment nav-section" data-tab="logs"><?php include 'logs.php'; ?></div>
<?php endif; ?>

<?php include 'auth_modals.php'; ?>
<script src="scripts.js"></script>

<?php if(!$loggedIn): ?>
<script>$(function(){ $('#loginModal').modal({closable:false}).modal('show'); });</script>
<?php endif; ?>

</body>
</html>
