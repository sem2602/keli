<?php

require_once 'DB.php';

$status = $_POST['status'];

switch ($status) {
    case "1":
		$status_name = 'Активные';
		$next_status = 'Не активные';
		$temp_status = 0;
        break;
    case "0":
		$status_name = 'Не активные';
		$next_status = 'Активные';
		$temp_status = 1;
        break;
    default:
        $status_name = 'Активные';
		$next_status = 'Не активные';
		$temp_status = 0;
        break;
}

$db = new DB();

$unexpected = $db->getUnexpected($status);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"><title></title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
	<script src="//api.bitrix24.com/api/v1/"></script>
</head>

<body>

<div class="wrapper">

	<div id="icons">
		<form class="icons_top" action="index.php" method="POST">
			<input type="image" name="image" src="./img/undo.png" alt="" style="vertical-align:middle"/>
		</form>
		<input id="home" type="image" name="image" onclick="openApplication()" src="./img/home.png" alt="" style="vertical-align:middle"/>
	</div>

	<div class="header">
		<h3>Непредвиденные расходы</h3>
  	</div>
  		
	<div class="d-flex justify-content-center align-items-end mb-3">
		<span class='display-6'><?= $status_name ?></span>
		
		<form action="unexpected.php" method="POST">
			<input type="hidden" name="status" value="<?= $temp_status ?>">
			<input class="btn btn-secondary ms-3" type="submit" name="submit" value="<?= $next_status ?>">
		</form>
		
		<button type="button" class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#unexpectedModal">
          Добавить
        </button>
		
  	</div>
  	
	<div class='container'>
	    
		<table class="table table-hover align-middle text-center">
    		<tr class="bg-info">
      			<th>№</th>
    			<th>Контрагент</th>
      			<th>Сумма</th>
    			<th>Описание</th>
      			<th>Действия</th>
      			<th>Обновления</th>
      		</tr>
      		
      		<?php foreach($unexpected as $key => $item): ?>
      		
      		    <tr>
      				<td><?= $key + 1 ?></td>
    				<td><?= $item['company'] ?></td>
    				<td><?= $item['amount'] ?></td>
    				<td><?= $item['description'] ?></td>
    				<td>
    				    <div class='d-flex'>
    				        <button onclick="edit(this.parentElement, '<?= $item['id'] ?>')" data-bs-toggle="modal" data-bs-target="#unexpectedModalEdit"><img src="./img/edit.png" alt="" style="vertical-align:middle"></button>
    					    <button onclick="delete_item(this.parentElement, '<?= $item['id'] ?>')" data-bs-toggle="modal" data-bs-target="#unexpectedModalDelete"><img src="./img/del.png" alt="" style="vertical-align:middle"></button>
    				    </div>
    				</td>
				
      				<td>
      				    <a href="unexpected_log.php?company_id=<?= $item['id'] ?>"><button>Подробнее...</button></a>
    				</td>
 				</tr>
      		
      		<?php endforeach; ?>
      			
      	</table>
	</div>

</div>



<!-- Modal CREATE-->
<div class="modal fade" id="unexpectedModal" tabindex="-1" aria-labelledby="unexpectedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="unexpectedModalLabel">Добавление непредвиденных расходов</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          
            <form id='create' class="form-floating" onsubmit="return false;">
              
                <div class="modal-body">
            
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" placeholder="Контрагент..." required>
                      <label>Контрагент...</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" placeholder="Описание..." required>
                      <label>Описание...</label>
                    </div>
                    
                    <div class="mb-3">
                        <input type="number" class="form-control" placeholder="Сумма..." required>
                    </div>
            
                    
            
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value='Закрыть'>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
              
            </form>
        </div>
    </div>
</div>

<!-- Modal EDIT-->
<div class="modal fade" id="unexpectedModalEdit" tabindex="-1" aria-labelledby="unexpectedModalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="unexpectedModalLabel">Редактирование непредвиденных расходов</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          
            <form id='edit' class="form-floating" onsubmit="return false;">
              
                <div class="modal-body">
            
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" placeholder="Контрагент..." required>
                      <label>Контрагент...</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" placeholder="Описание..." required>
                      <label>Описание...</label>
                    </div>
                    
                    <div class="mb-3">
                        <input type="number" class="form-control" placeholder="Сумма..." required>
                    </div>
            
                    <input type="text" name='company_id' value='' hidden>
            
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value='Закрыть'>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
              
            </form>
        </div>
    </div>
</div>

<!-- Modal DELETE-->
<div class="modal fade" id="unexpectedModalDelete" tabindex="-1" aria-labelledby="unexpectedModalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="unexpectedModalDeleteLabel">Вы хотите удалить запись?</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          
            <form id='delete' class="form-floating" onsubmit="return false;">
              
                <div class="modal-body">
                    
                    <input type="text" name='company' value='' hidden>
                    <input type="text" name='description' value='' hidden>
                    <input type="number" name='amount' value='' hidden>
                    <input type="text" name='company_id' value='' hidden>
            
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value='Закрыть'>
                    <button type="submit" class="btn btn-primary">Удалить</button>
                </div>
              
            </form>
        </div>
    </div>
</div>

<input id='user' type="text" hidden>
		

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
		
<script>

	BX24.callMethod("profile", {}, function (res){
		temp_user = res.answer.result.NAME + " " + res.answer.result.LAST_NAME;
		document.getElementById("user").value = temp_user;
	});

	function openApplication() {
		BX24.closeApplication();
		BX24.openApplication();
	}
	
	//let unexpectedModal = document.querySelector('#unexpectedModal');
	
	let add_form = document.querySelector('#create');
	let edit_form = document.querySelector('#edit');
	let delete_form = document.querySelector('#delete');
    
    add_form.addEventListener('submit', () => {
        
        const url = "./save_unexpected_log.php";
    	
    	let form = new FormData();
    	form.append('company', add_form[0].value);
    	form.append('description', add_form[1].value);
    	form.append('amount', add_form[2].value);
    	form.append('user', document.getElementById("user").value);
    	form.append('doing', 'create');
    	
    	add_form[4].outerHTML = `<button class="btn btn-primary" type="button" disabled>
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Отправка данных...
        </button>`;
        
        send(url, form).then(json => {
            if (json.data) {
                location.reload();
            }else{
                console.log('Помилка серверу!');
		        return false;
            }
        });
    });
    
    edit_form.addEventListener('submit', () => {
        
        const url = "./save_unexpected_log.php";
    	
    	let form = new FormData();
    	form.append('company', edit_form[0].value);
    	form.append('description', edit_form[1].value);
    	form.append('amount', edit_form[2].value);
    	form.append('company_id', edit_form[3].value);
    	form.append('user', document.getElementById("user").value);
    	form.append('doing', 'update');
    	
    	edit_form[5].outerHTML = `<button class="btn btn-primary" type="button" disabled>
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Отправка данных...
        </button>`;
        
        send(url, form).then(json => {
            if (json.data) {
                //console.dir(json.data);
                location.reload();
            }else{
                console.log('Помилка серверу!');
		        return false;
            }
        });
    });
    
    delete_form.addEventListener('submit', () => {
        
        const url = "./save_unexpected_log.php";
    	
    	let form = new FormData();
    	form.append('company', delete_form[0].value);
    	form.append('description', delete_form[1].value);
    	form.append('amount', delete_form[2].value);
    	form.append('company_id', delete_form[3].value);
    	form.append('user', document.getElementById("user").value);
    	form.append('doing', 'delete');
    	
    	delete_form[5].outerHTML = `<button class="btn btn-primary" type="button" disabled>
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Отправка данных...
        </button>`;
        
        send(url, form).then(json => {
            if (json.data) {
                //console.dir(json.data);
                location.reload();
            }else{
                console.log('Помилка серверу!');
		        return false;
            }
        });
        
    });
    
    
    function edit(elem, id){
        
        edit_form[0].value = elem.parentElement.parentElement.children[1].outerText;
        edit_form[1].value = elem.parentElement.parentElement.children[3].outerText;
		edit_form[2].value = parseFloat(elem.parentElement.parentElement.children[2].outerText);
		edit_form[3].value = id;
        
    }
    
    function delete_item(elem, id){
        
        delete_form[0].value = elem.parentElement.parentElement.children[1].outerText;
        delete_form[1].value = elem.parentElement.parentElement.children[3].outerText;
		delete_form[2].value = parseFloat(elem.parentElement.parentElement.children[2].outerText);
		delete_form[3].value = id;
        
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
</body></html>