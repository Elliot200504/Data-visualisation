<?php
session_start();

// Demo users storage
if(!isset($_SESSION['auth_users'])) {
    $_SESSION['auth_users'] = [
        ['username'=>'admin','password'=>password_hash('admin123', PASSWORD_DEFAULT)]
    ];
}

$action = $_POST['action'] ?? '';

if($action === 'register'){
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    foreach($_SESSION['auth_users'] as $u){
        if($u['username']===$username){
            echo json_encode(['success'=>false,'msg'=>'Username exists']); exit;
        }
    }

    $_SESSION['auth_users'][] = ['username'=>$username,'password'=>password_hash($password,PASSWORD_DEFAULT)];
    echo json_encode(['success'=>true]); exit;
}

if($action === 'login'){
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    foreach($_SESSION['auth_users'] as $u){
        if($u['username']===$username && password_verify($password,$u['password'])){
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            echo json_encode(['success'=>true]); exit;
        }
    }
    echo json_encode(['success'=>false,'msg'=>'Invalid credentials']); exit;
}

if($action === 'logout'){
    session_destroy();
    echo json_encode(['success'=>true]); exit;
}
