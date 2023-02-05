<?php

require_once 'DB.php';

class Rest{
    
    private $auth;
    private $db;
    
    function __construct($auth)
    {
        $this->auth = $auth;
        $this->db = new DB();
    }
    
    public function isAdmin()
    {
        return $this->send('user.admin', []);
    }
    
    public function getProduct($id)
    {
        return $this->send('crm.product.get', ['id' => $id]);
    }
    
    public function getProductProperty($id)
    {
        return $this->send('crm.product.property.get', ['id' => $id]);
    }
    
    public function getCatalogList()
    {
        return $this->send('crm.catalog.list');
    }
    
    public function getSection($section_id)
    {
        return $this->send('crm.productsection.get', ['id' => $section_id]);
    }
    
    public function getSections($section_id)
    {
        return $this->send('crm.productsection.list', [
            'order' => ["NAME" => "ASC"],
            'filter' => [
                //"CATALOG_ID" => $catalog_id,
                "SECTION_ID" => (int)$section_id
            ],   
        ]);
    }
    
    public function getProductList($section_id)
    {
        return $this->send('crm.product.list', [
            'order' => ["NAME" => "ASC"],
            'filter' => [
                //"CATALOG_ID" => $catalog_id,
                "SECTION_ID" => $section_id,
            ],   
        ]);
    }
    
    public function getProductMonthLog($product_id)
    {
        return $this->db->getProductMonthLog($product_id);
    }
    
    public function getProductQuantity($product_id)
    {
        return $this->db->getQuantity($product_id);
    }
    
    public function addProductData($product_id)
    {
        return $this->db->addProduct($product_id);
    }
    
    private function send($method, $params = [])
    {
        $queryUrl = "https://".$this->auth["domain"]."/rest/".$method;
    	$queryData = http_build_query(array_merge($params, array("auth" => $this->auth["access_token"])));
    
    	$curl = curl_init();
    
    	curl_setopt_array($curl, array(
    		CURLOPT_POST => 1,
    		CURLOPT_HEADER => 0,
    		CURLOPT_RETURNTRANSFER => 1,
    		CURLOPT_SSL_VERIFYPEER => 1,
    		CURLOPT_URL => $queryUrl,
    		CURLOPT_POSTFIELDS => $queryData,
    	));
    
    	$result = curl_exec($curl);
    	curl_close($curl);
    	
    	return json_decode($result, 1);
    }
    
}