<?php

require_once 'DB.php';

$db = new DB();

if($_POST['method'] == 'get_quantity'){
    $products = json_decode($_POST['products'], true);
    
    foreach ($products as $key => $product){
        $q = $db->getQuantity($product['ID']);
        if(!$q){
            $db->addProduct($product['ID']);
            $q['quantity'] = 0;
            $q['q_kiev'] = 0;
        }
        
        $products[$key]['quantity'] = $q['quantity'];
        $products[$key]['q_kiev'] = $q['q_kiev'];
    }
    
    echo json_encode(['data' => $products]);
}

if($_POST['method'] == 'add'){
    
    $quantity = $db->getQuantity($_POST['product_id']);
    
    $cur_quantity = (int)$quantity['quantity'] + (int)$_POST['quantity'];
    $cur_q_kiev = (int)$quantity['q_kiev'] + (int)$_POST['q_kiev'];
    
    $db->updateQuantities($_POST['product_id'], $cur_quantity, $cur_q_kiev);
    
    $_POST['doing'] = 'Приход';
    
    $db->setLogs($_POST);

    echo json_encode(['data' => ['id' => $_POST['product_id'],'quantity' => $cur_quantity, 'q_kiev' => $cur_q_kiev]]);
  
}

if($_POST['method'] == 'sub'){
    
    $quantity = $db->getQuantity($_POST['product_id']);
    
    $cur_quantity = (int)$quantity['quantity'] + (int)$_POST['quantity'];
    $cur_q_kiev = (int)$quantity['q_kiev'] + (int)$_POST['q_kiev'];
    
    $db->updateQuantities($_POST['product_id'], $cur_quantity, $cur_q_kiev);
    
    $_POST['doing'] = 'Списание';
    
    $db->setLogs($_POST);

    echo json_encode(['data' => ['id' => $_POST['product_id'],'quantity' => $cur_quantity, 'q_kiev' => $cur_q_kiev]]);
  
}

if($_POST['method'] == 'edit_company'){
    
    $result = $db->updateCompanyLog($_POST);

    echo json_encode(['data' => $result]);
  
}

?>