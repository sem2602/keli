<?php

require_once 'DB.php';

$db = new DB();



$data = [
    'company' => $_POST['company'],
    'amount' => $_POST['amount'] . ' грн',
    'description' => $_POST['description'],
    'user' => $_POST['user'],
    'company_id' => $_POST['company_id'],
];


if($_POST['doing'] === 'create'){
    
    $db->setUnexpected($data);
    echo json_encode(['data' => true]);
    
}


if($_POST['doing'] === 'update'){
    
    $db->updateUnexpected($data);
    echo json_encode(['data' => true]);
    
}

if($_POST['doing'] === 'delete'){
    
    $db->deleteUnexpected($data);
    echo json_encode(['data' => true]);
    
}