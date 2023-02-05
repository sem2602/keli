<?php

require_once 'DB.php';

$db = new DB();

$product_id = $_REQUEST['product_id'];
$product_name = $_REQUEST['product_name'];
$is_adm = (int)$_REQUEST['is_admin'];

// var_dump($is_adm);
// exit;

if(empty($product_id) || empty($product_name)){exit;}

$logs = $db->getLogByProductId($product_id);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"><title>Каталог товаров</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    </head>
    
    <body>

		<div class="container">

    		

    		
        	<h4 class='text-center mt-3 mb-3'>История списания и прихода:   <em><?= $product_name ?></em></h4>
      		
      		
		    <div>
		    
		        <?php if($logs): ?>
		    
            		<table class="table table-bordered table-striped align-middle text-center">
                	
                		<tr style='background-color:#cbf9ff'>
                  			<th>№</th>
                			<th>Каталог</th>
                  			<th>Наименование</th>
                			<th>Контрагент</th>
                  			<th>Пользователь</th>
                  			<th>Действие</th>
                  			<th>На складе</th>
                			<th>Из них в Киеве</th>
                			<th>Дата и время</th>
                  		</tr>
  		
  		
              		    <?php foreach($logs as $key => $log): ?>
              		        <tr>                  				
              		            <td><?= $key + 1 ?></td>
                				<td><?= $log['tree'] ?></td>
                				<td><?= $log['product'] ?></td>
                				<td>
                				    
                				    <div class='d-flex justify-content-between align-items-center'>
                				        <span><?= $log['company'] ?></span>
                				    
                    				    <?php if($is_adm): ?>
                    				        <button class="btn btn-light border border-info p-1 ms-1" data-bs-toggle="modal" data-bs-target="#companyModal" onclick="prepareModal(this)">edit</button>
                    				    <?php endif; ?>
                    				    
                    				    <span hidden><?= $log['id'] ?></span>
                				    </div>
                				    
                				</td>
                  				<td><?= $log['username'] ?></td>
                				<td><?= $log['doing'] ?></td>
                  				<td><?= $log['count'] ?></td>
                				<td><?= $log['kiev_count'] ?></td>
                  				<td><?= $log['datetime'] ?></td>
             			    </tr>
              		    <?php endforeach; ?>
  	
  		            </table>
  		
          		<?php else: ?>
          		
          		    <div class="alert alert-info" role="alert">
                      <h5 class='text-center'>Нет истории по данному товару</h5>
                    </div>
          		    
            	<?php endif; ?>
  		
		    </div>
	
		</div>
		
		<!-- Modal EDIT COMPANY -->
<div class="modal fade" id="companyModal" tabindex="-1" aria-labelledby="companyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="companyModalLabel">Контрагент</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeModal()"></button>
            </div>
          
            <form id='company' class="form-floating" onsubmit="return false;">
              
                <div class="modal-body">
                    
                    <div class="alert" role="alert" hidden></div>
            
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" placeholder="Контрагент..." required>
                      <label>Контрагент...</label>
                    </div>
            
                    <input name="company_id" hidden>
            
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light border border-info" onclick='edit_company()'>Сохранить...</button>
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value='Закрыть' onclick="closeModal()">
                </div>
              
            </form>
        </div>
    </div>
</div>
		
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
		
<script>

const table = document.querySelector('.table');

let companyForm = document.querySelector('#company');
let errorInfo = document.querySelector('.alert');
let current;

function prepareModal(elem){
    current = elem;
    companyForm[0].value = elem.previousElementSibling.innerText;
    companyForm[1].value = elem.nextElementSibling.innerText;
    
}

function edit_company(){
    
    if(!companyForm.reportValidity()){return false;}
        
    errorInfo.innerText = 'Отправка данных...';
    errorInfo.classList.add('alert-warning');
    errorInfo.hidden = false;
    
    const url = "./save.php";
    	
	let form = new FormData();
	form.append('company_id', companyForm[1].value);
	form.append('company', companyForm[0].value);
	form.append('method', 'edit_company');
	
	send(url, form).then(json => {
        if (json.data) {
            
            errorInfo.innerText = 'Данные успешно обновлены...';
            errorInfo.className = 'alert';
            errorInfo.classList.add('alert-success');
            errorInfo.hidden = false;
            
            current.parentElement.parentElement.children[0].children[0].innerText = companyForm[0].value;
            
            //location.reload();
        } else {
            errorInfo.innerText = 'Ошибка передачи данных!';
            errorInfo.className = 'alert';
            errorInfo.classList.add('alert-danger');
            errorInfo.hidden = false;
            console.log('Помилка серверу!');
	        return false;
        }
    });
    
}

function closeModal(){
    errorInfo.innerText = '';
    errorInfo.className = 'alert';
    errorInfo.hidden = true;
}

async function send(url, body = null) {
        
    if (body) {
        let response = await fetch(url, {
            method: 'POST',
            body: body,
            //headers: {"Content-type": "application/json"}
        });
        return await response.json();
    } else {
        let response = await fetch(url, {method: 'GET'});
        return await response.json();
    }
}


</script>
	</body>
</html>