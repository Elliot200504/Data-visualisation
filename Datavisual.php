<?php
session_start();

// Initialize demo data
if (!isset($_SESSION['data'])) {
    $_SESSION['data'] = [
        "Felanmalan" => 12,
        "ArbetsOrder" => 25,
        "Errors" => 8
    ];
}

if (!isset($_SESSION['details'])) {
    $_SESSION['details'] = [
        "Felanmalan" => ["labels"=>["Mon","Tue","Wed","Thu","Fri"], "values"=>[2,3,1,4,2]],
        "ArbetsOrder" => ["labels"=>["Mon","Tue","Wed","Thu","Fri"], "values"=>[5,6,4,7,3]],
        "Errors" => ["labels"=>["Mon","Tue","Wed","Thu","Fri"], "values"=>[1,2,3,1,1]]
    ];
}

if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        ["id"=>1,"name"=>"Anna Svensson","role"=>"Technician","last_login"=>"2025-09-11 08:42","logs"=>["Logged in","Visited Dashboard"]],
        ["id"=>2,"name"=>"Johan Karlsson","role"=>"Operator","last_login"=>"2025-09-11 09:05","logs"=>["Logged in","Reported error #204"]],
        ["id"=>3,"name"=>"Maria Lindgren","role"=>"Manager","last_login"=>"2025-09-11 07:58","logs"=>["Logged in","Reviewed reports"]]
    ];
}

// Handle AJAX POST for adding users/items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_user') {
        $name = $_POST['name'] ?? '';
        $role = $_POST['role'] ?? '';
        $id = count($_SESSION['users']) + 1;
        $_SESSION['users'][] = ["id"=>$id,"name"=>$name,"role"=>$role,"last_login"=>date("Y-m-d H:i"),"logs"=>["User created"]];
        echo json_encode(["success"=>true,"id"=>$id]);
        exit;
    }
    if ($action === 'add_item') {
        $category = $_POST['category'] ?? '';
        if(isset($_SESSION['data'][$category])) {
            $_SESSION['data'][$category]++;
            $day = date("D");
            if (!in_array($day, $_SESSION['details'][$category]['labels'])) {
                $_SESSION['details'][$category]['labels'][] = $day;
                $_SESSION['details'][$category]['values'][] = 1;
            } else {
                $index = array_search($day,$_SESSION['details'][$category]['labels']);
                $_SESSION['details'][$category]['values'][$index]++;
            }
            echo json_encode(["success"=>true]);
        } else {
            echo json_encode(["success"=>false]);
        }
        exit;
    }
}

// Calculate User Logs count dynamically
$userLogsCount = array_sum(array_map(fn($u)=>count($u['logs']), $_SESSION['users']));
$_SESSION['data']['User Logs'] = $userLogsCount;

$jsonData = json_encode($_SESSION['data']);
$jsonUsers = json_encode($_SESSION['users']);
$jsonDetails = json_encode($_SESSION['details']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard with Navbar</title>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.3/dist/semantic.min.css">
<script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.3/dist/semantic.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{padding:15px;background:#fafafa;}
canvas{max-height:220px;}
.clickable{color:#2185d0;cursor:pointer;}
.clickable:hover{text-decoration:underline;}
.nav-section{display:none;margin-top:20px;}
.nav-section{display:none;margin-top:20px;}
</style>
</head>
<body>

<!-- Navbar -->
<div class="ui top attached tabular menu">
  <a class="item active" data-tab="total">Total Bild</a>
  <a class="item" data-tab="users">Användare</a>
  <a class="item" data-tab="logs">Logs</a>
</div>

<!-- Total Bild Section -->
<div class="ui bottom attached tab segment nav-section active" data-tab="total">
  <div class="ui two column stackable grid">
    <div class="column">
      <div class="ui fluid card">
        <div class="content"><div class="header">Summary Doughnut</div></div>
        <div class="content"><canvas id="pieChart"></canvas></div>
      </div>
    </div>
    <div class="column">
      <div class="ui fluid card">
        <div class="content"><div class="header">Summary Bar</div></div>
        <div class="content"><canvas id="barChart"></canvas></div>
      </div>
    </div>
  </div>

  <h3 class="ui header" style="margin-top:20px;">📂 Categories</h3>
  <table class="ui very compact celled striped table">
  <thead><tr><th>Category</th><th>Total</th></tr></thead>
  <tbody>
  <?php foreach($_SESSION['data'] as $cat=>$val): ?>
  <tr>
  <td><span class="clickable category" data-category="<?=$cat?>"><?=$cat?></span></td>
  <td><?=$val?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
  </table>
</div>

<!-- Users Section -->
<div class="ui bottom attached tab segment nav-section" data-tab="users">
  <button class="ui tiny green button" id="addUserBtn">Add User</button>
  <table class="ui very compact celled striped table" style="margin-top:10px;">
  <thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Last Login</th></tr></thead>
  <tbody>
  <?php foreach($_SESSION['users'] as $u): ?>
  <tr>
  <td><?=$u['id']?></td>
  <td><span class="clickable user" data-id="<?=$u['id']?>"><?=$u['name']?></span></td>
  <td><?=$u['role']?></td>
  <td><?=$u['last_login']?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
  </table>
</div>

<!-- Logs Section -->
<div class="ui bottom attached tab segment nav-section" data-tab="logs">
  <h3 class="ui header">📋 User Logs</h3>
  <?php foreach($_SESSION['users'] as $u): ?>
  <div class="ui segment">
    <h4 class="ui dividing header"><?=$u['name']?> <span style="font-size:0.8em;color:gray">(<?=$u['role']?>)</span></h4>
    <div class="ui relaxed divided list">
      <?php foreach($u['logs'] as $log): ?>
      <div class="item"><i class="history icon"></i><div class="content"><?=$log?></div></div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="ui small modal">
  <div class="header" id="categoryTitle"></div>
  <div class="content"><canvas id="categoryChart"></canvas></div>
  <div class="actions"><div class="ui approve button">Close</div></div>
</div>

<script>
let data=<?=$jsonData?>;
let users=<?=$jsonUsers?>;
let details=<?=$jsonDetails?>;

function renderSummaryCharts(){
  new Chart($("#pieChart"),{
    type:'doughnut',
    data:{
      labels:Object.keys(data),
      datasets:[{
        data:Object.values(data),
        backgroundColor:['#2185d0','#21ba45','#db2828','#f39c12']
      }]
    },
    options:{plugins:{legend:{position:'bottom'}},responsive:true}
  });

  new Chart($("#barChart"),{
    type:'bar',
    data:{
      labels:Object.keys(data),
      datasets:[{
        data:Object.values(data),
        backgroundColor:['#2185d0','#21ba45','#db2828','#f39c12'] 
      }]
    },
    options:{plugins:{legend:{display:false}},responsive:true,scales:{y:{beginAtZero:true}}}
  });
}

$(function(){
renderSummaryCharts();

// User logs modal
$(".user").click(function(){
const uid=$(this).data("id");
const u=users.find(x=>x.id==uid);
$("#modalTitle").text(u.name+" — Activity Logs");
$("#logContent").empty();
u.logs.forEach(log=>$("#logContent").append(`<div class="item"><i class="history icon"></i><div class="content">${log}</div></div>`));
$("#logModal").modal("show");
});

// Category charts
let categoryChartInstance=null;
$(".category").click(function(){
const cat=$(this).data("category");
$("#categoryTitle").text(cat+" — Details");
if(categoryChartInstance) categoryChartInstance.destroy();
let ctx=document.getElementById("categoryChart").getContext("2d");
let chartData;
if(cat==="User Logs"){
    chartData={labels:users.map(u=>u.name),data:users.map(u=>u.logs.length)};
}else{
    chartData={labels:details[cat].labels,data:details[cat].values};
}
categoryChartInstance=new Chart(ctx,{
    type:(cat==="User Logs"?"bar":"line"),
    data:{
        labels:chartData.labels,
        datasets:[{label:cat,data:chartData.data,borderColor:"#2185d0",backgroundColor:"rgba(33,133,208,0.3)",fill:true}]
    },
    options:{responsive:true,scales:{y:{beginAtZero:true}}}
});
$("#categoryModal").modal("show");
});
});
</script>
</body>
</html>
