<?php

require_once 'config.php';

class DB
{
    
    private $pdo;
    
    function __construct()
    {
        $this->pdo = new PDO("mysql:host=localhost:3306;dbname=".DBNAME.";charset=utf8;", USERNAME, PASSWORD);
    }
    
    public function updateCompanyLog($data)
    {
        $sql = 'UPDATE log SET company = :company WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':id' => $data['company_id'],
            ':company' => $data['company'],
        ];
        $stmt->execute($params);
        
        return true;
    }
    
    public function getLogs($date, $sort)
    {
        $sql = 'SELECT * FROM log WHERE datetime BETWEEN :datetime AND DATE_ADD(:datetime, INTERVAL 1 DAY)';
        $params = [':datetime' => $date];
        if($sort != 'all'){
            $sql .= ' AND doing = :doing';
            $params[':doing'] = $sort;
        }
        $sql .= ' ORDER BY datetime DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLogByProductId($product_id)
    {
        $sql = 'SELECT * FROM log WHERE product_id = :product_id ORDER BY datetime DESC';
        $params = [':product_id' => $product_id];
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProductMonthLog($product_id)
    {
        $sql = 'SELECT * FROM log WHERE MONTH(`datetime`) = MONTH(NOW()) AND YEAR(`datetime`) = YEAR(NOW()) AND product_id = :product_id';
        $params = [':product_id' => $product_id];
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function setLogs($data)
    {
        $sql = 'INSERT log SET product = :product, product_id = :product_id, tree = :tree, company = :company, username = :username, doing = :doing, count = :count, kiev_count = :kiev_count';
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':product' => $data['product'],
            ':product_id' => $data['product_id'],
            ':tree' => $data['tree'],
            ':company' => $data['company'],
            ':username' => $data['user'],
            ':doing' => $data['doing'],
            ':count' => $data['quantity'],
            ':kiev_count' => $data['q_kiev'],
        ];
        $stmt->execute($params);
    }
    
    public function getQuantity($ext_id)
    {
        $sql = 'SELECT * FROM products WHERE ext_id = :ext_id';
        $stmt = $this->pdo->prepare($sql);
        $params = [':ext_id' => $ext_id];
        $stmt->execute($params);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateQuantities($ext_id, $curr_count, $curr_kiev_count)
    {
        $sql = 'UPDATE products SET quantity = :quantity, q_kiev = :q_kiev WHERE ext_id = :ext_id';
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':ext_id' => $ext_id,
            ':quantity' => $curr_count,
            ':q_kiev' => $curr_kiev_count,
        ];
        $stmt->execute($params);
    }
    
    public function addProduct($ext_id)
    {
        $sql = 'INSERT INTO products (ext_id) VALUES (:ext_id)';
        $stmt = $this->pdo->prepare($sql);
        $params = [':ext_id' => $ext_id];
        $stmt->execute($params);
    }
    
    public function getUnexpected($status)
    {
        $sql = 'SELECT * FROM unexpected WHERE status = :status ORDER BY datetime DESC';
        $stmt = $this->pdo->prepare($sql);
        $params = [':status' => (int)$status];
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function setUnexpected($data)
    {
        $sql = 'INSERT INTO unexpected (company, amount, description, status) VALUES (:company, :amount, :description, 1)';
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':company' => $data['company'],
            ':amount' => $data['amount'],
            ':description' => $data['description'],
        ];
        $stmt->execute($params);
        
        $company_id = $this->pdo->lastInsertId();
        
        $this->addUnexpectedLog($data, $company_id, 'создание');
       
    }
    
    public function updateUnexpected($data)
    {
        $sql = 'UPDATE unexpected SET company = :company, amount = :amount, description = :description WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':id' => $data['company_id'],
            ':company' => $data['company'],
            ':amount' => $data['amount'],
            ':description' => $data['description'],
        ];
        $stmt->execute($params);
        
        $this->addUnexpectedLog($data, $data['company_id'], 'обновление');
    }
    
    public function deleteUnexpected($data)
    {
        $sql = 'UPDATE unexpected SET status = 0 WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':id' => $data['company_id'],
        ];
        $stmt->execute($params);
        
        $this->addUnexpectedLog($data, $data['company_id'], 'удаление');
       
    }
    
    public function addUnexpectedLog($data, $company_id, $doing_type)
    {
        $sql = 'INSERT INTO unexpected_log (company, amount, description, user, company_id, doing_type) VALUES (:company, :amount, :description, :user, :company_id, :doing_type)';
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':company' => $data['company'],
            ':amount' => $data['amount'],
            ':description' => $data['description'],
            ':user' => $data['user'],
            ':company_id' => $company_id,
            ':doing_type' => $doing_type,
        ];
        $stmt->execute($params);
        
    }
    
    public function getUnexpectedLog($company_id)
    {
        $sql = 'SELECT * FROM unexpected_log WHERE company_id = :company_id ORDER BY datetime DESC';
        $stmt = $this->pdo->prepare($sql);
        $params = [':company_id' => $company_id];
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}