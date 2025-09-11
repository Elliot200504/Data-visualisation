<!-- 
/dashboard/
│
├─ index.php           # Main entry point, includes navbar and tabs
├─ data.php            # Stores and returns session data / handles AJAX actions
├─ users.php           # User-related table and add-user modal
├─ categories.php      # Category table and charts
├─ logs.php            # Pretty user logs section
├─ scripts.js          # JavaScript logic for charts, tabs, and modals
├─ styles.css          # Optional: custom CSS
└─ vendor/             # Optional: store Chart.js and Fomantic UI locally, or use CDN 
-->

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.3/dist/semantic.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.3/dist/semantic.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<style>
/* Make charts smaller and responsive */
canvas {
    max-height: 200px;   /* limit height */
    width: 100% !important;
    display: block;
    margin: 0 auto;
}
</style>

<div class="ui top attached menu">
  <a class="item active" data-tab="total">Total Bild</a>
  <a class="item" data-tab="users">Användare</a>
  <a class="item" data-tab="logs">Logs</a>
  
  <div class="right menu">
    <?php if(!isset($_SESSION['logged_in'])): ?>
      <a class="item" id="loginBtn">Login</a>
      <a class="item" id="registerBtn">Register</a>
    <?php else: ?>
      <span class="item">Hello, <?=htmlspecialchars($_SESSION['username'])?></span>
      <a class="item" id="logoutBtn">Logout</a>
    <?php endif; ?>
  </div>

<div class="ui bottom attached tab segment nav-section active" data-tab="total">
  <?php include 'categories.php'; ?>
</div>
<div class="ui bottom attached tab segment nav-section" data-tab="users">
  <?php include 'users.php'; ?>
</div>
<div class="ui bottom attached tab segment nav-section" data-tab="logs">
  <?php include 'logs.php'; ?>
</div>

<script src="scripts.js"></script>
</body>
</html>

