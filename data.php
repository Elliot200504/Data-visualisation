<?php
session_start();

// Initialize demo data
if (!isset($_SESSION['data'])) {
    $_SESSION['data'] = ["Felanmalan"=>12, "ArbetsOrder"=>25, "Errors"=>8];
}
if (!isset($_SESSION['details'])) {
    $_SESSION['details'] = [
        "Felanmalan"=>["labels"=>["Mon","Tue","Wed","Thu","Fri"],"values"=>[2,3,1,4,2]],
        "ArbetsOrder"=>["labels"=>["Mon","Tue","Wed","Thu","Fri"],"values"=>[5,6,4,7,3]],
        "Errors"=>["labels"=>["Mon","Tue","Wed","Thu","Fri"],"values"=>[1,2,3,1,1]]
    ];
}
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        ["id"=>1,"name"=>"Anna Svensson","role"=>"Technician","last_login"=>"2025-09-11 08:42","logs"=>["Logged in","Visited Dashboard"]],
        ["id"=>2,"name"=>"Johan Karlsson","role"=>"Operator","last_login"=>"2025-09-11 09:05","logs"=>["Logged in","Reported error #204"]],
        ["id"=>3,"name"=>"Maria Lindgren","role"=>"Manager","last_login"=>"2025-09-11 07:58","logs"=>["Logged in","Reviewed reports"]]
    ];
}

// Handle AJAX requests
$action = $_REQUEST['action'] ?? '';
if ($action === 'add_user') {
    $name = $_POST['name'] ?? '';
    $role = $_POST['role'] ?? '';
    $id = count($_SESSION['users']) + 1;
    $_SESSION['users'][] = ["id"=>$id,"name"=>$name,"role"=>$role,"last_login"=>date("Y-m-d H:i"),"logs"=>["User created"]];
    echo json_encode(["success"=>true]);
    exit;
}

if ($action === 'add_item') {
    $category = $_POST['category'] ?? '';
    if(isset($_SESSION['data'][$category])) {
        $_SESSION['data'][$category]++;
        $day = date("D");
        if (!in_array($day,$_SESSION['details'][$category]['labels'])) {
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

// Fetch data
if ($action === 'fetch_data') {
    $userLogsCount = array_sum(array_map(fn($u)=>count($u['logs']), $_SESSION['users']));
    $_SESSION['data']['User Logs'] = $userLogsCount;
    echo json_encode([
        'data'=>$_SESSION['data'],
        'details'=>$_SESSION['details'],
        'users'=>$_SESSION['users']
    ]);
    exit;
}
