<?php

require_once 'config.php';

$prev = $_REQUEST['prev'];

if(!empty($_REQUEST['section_id'])){
    $section_id = $_REQUEST['section_id'];
} else {$section_id = '';}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"><title>Каталог товаров</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
		<script src="//api.bitrix24.com/api/v1/"></script>
    </head>
    
    <body>

		<div>
    		<div class="text-center mt-3">
        		<h3>Каталог оборудования</h3>
      		</div>
      		
      		<?php if(true): ?>
      		    <div id="icons" class="text-center">
    				<div class="icons_top">
    				    <a id='back'><img src='./img/undo.png'></a>
    				</div>
    				<input id="home" type="image" name="image" onclick="openApplication()" src="./img/home.png" alt="" style="vertical-align:middle"/>
    				<input id='section_name' type='text' value='' hidden>
			    </div>
      		<? endif; ?>
    
  		    <div id='sections' class='d-flex flex-column align-items-center mb-3'></div>
  		    
  		    <div class='container'>
  		        
  		        <div id='products' class='shadow mt-5 bg-body-tertiary'></div>
  		        
  		        <div class='shadow p-3 mt-5 bg-body-tertiary rounded'>
  		            
  		            <hr>
  		
              		<form action="log.php" method="POST">
              		    <div class='row g-3 align-items-center justify-content-center'>
              		        
              		        <div class="col-auto"><label>История за период: </label></div>
              		        <div class="col-auto"><input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>" required/></div>
              		        <div class="col-auto">
              		            <select class="form-select" name="doing">
                    				<option selected value="all">Приход/Списание</option>
                    				<option value="Приход">Приход</option>
                        			<option value="Списание">Списание</option>
                    			</select>
              		        </div>
              		        <div class="col-auto"><button class="btn btn-light border border-info" type="submit">Показать...</button></div>
              		        
                        </div>
                    </form>
                
                    <hr>
            
            		
        		    <form action="unexpected.php" method="POST">
                        <div class='row align-items-center justify-content-center mt-3 mb-3'>
                            
                            <div class="col-auto"><label>Непредвиденные расходы: </label></div>
                            <div class="col-auto">
                                <select class="form-select" name="status">
                    				<option selected value="1">Активные</option>
                    				<option value="0">Не активные</option>
                    			</select>
                            </div>
                            <div class="col-auto"><button class="btn btn-light border border-info" type="submit" name="submit">Показать...</button></div>
                             
                		</div>
                    </form>
  		            
  		        </div>
  		        
  		    </div>
  		    
		</div>
		
		
		
<!-- Modal STORE +/- -->
<div class="modal fade" id="storeModal" tabindex="-1" aria-labelledby="storeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="storeModalLabel">Приход / Списание</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeModal()"></button>
            </div>
          
            <form id='store' class="form-floating" onsubmit="return false;">
              
                <div class="modal-body">
                    
                    <div class="row g-3 mb-3">
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text">На складе</span>
                                <input type="number" class="form-control text-primary fs-3" readonly>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text">Из них в Киеве</span>
                                <input type="number" class="form-control text-primary fs-3" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="alert" role="alert" hidden></div>
            
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" placeholder="Контрагент..." required>
                      <label>Контрагент...</label>
                    </div>
                    
                    <div class="mb-3">
                        <input type="number" min="0" class="form-control" placeholder="На складе..." onchange='paste_quantity()' required>
                    </div>
                    
                    <div class="mb-3">
                        <input type="number" min="0" class="form-control" placeholder="Из них в Киеве..." onchange='paste_q_kiev()'>
                    </div>
            
                    <input name="product_id" hidden>
            
                </div>
                <div class="modal-footer">
                    <button id='adding' class="btn btn-light border border-info" onclick='add()' hidden='true'>Оприходовать</button>
                    <button class="btn btn-light border border-info" onclick='sub()'>Списать</button>
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value='Закрыть' onclick="closeModal()">
                </div>
              
            </form>
        </div>
    </div>
</div>

<input id="user" type="text" hidden>
		
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
		
<script>



    const section_id = '<?=$section_id?>';
    const start_section_id = '';
    const sections = document.querySelector('#sections');
    const products = document.querySelector('#products');
    const section_name = document.querySelector('#section_name');
    
    let current;
    
    let storeForm = document.querySelector('#store');
    let errorInfo = document.querySelector('.alert');
    
    let is_admin = 0;
    
    BX24.init(function(){
        if(BX24.isAdmin()){
            is_admin = 1;
            document.querySelector('#adding').hidden = false;
        }
    });
    
    function paste_quantity(){
		storeForm[4].value = null;
    }
    
    function paste_q_kiev(){
        storeForm[3].value = +storeForm[4].value;
    }
    
    function closeModal(){
        storeForm[3].value = null;
        storeForm[4].value = null;
        
        errorInfo.innerText = '';
        errorInfo.className = 'alert';
        errorInfo.hidden = true;
    }
    
    function prepare(elem){
        current = elem;
        document.querySelector('#storeModalLabel').innerText = elem.parentElement.parentElement.children[1].innerText;
        storeForm[0].value = +elem.parentElement.parentElement.children[2].innerText;
        storeForm[1].value = +elem.parentElement.parentElement.children[3].innerText;
        storeForm[5].value = elem.nextElementSibling.innerText;
    }
		    
    function add(){
        
        if(!storeForm.reportValidity()){return false;}
        
        let quantity = +storeForm[3].value;
        
        if(quantity == 0){return false;}
        
        errorInfo.innerText = 'Отправка данных...';
        errorInfo.classList.add('alert-warning');
        errorInfo.hidden = false;
        
        const url = "./save.php";
    	
    	let form = new FormData();
    	form.append('product_id', storeForm[5].value);
    	form.append('product', document.querySelector('#storeModalLabel').innerText);
    	form.append('company', storeForm[2].value);
    	form.append('tree', section_name.value);
    	form.append('quantity', quantity);
    	form.append('q_kiev', +storeForm[4].value);
    	form.append('product_id', storeForm[5].value);
    	form.append('user', document.querySelector('#user').value);
    	form.append('method', 'add');
    	
    	send(url, form).then(json => {
            if (json.data) {
                
                errorInfo.innerText = 'Данные успешно обновлены...';
                errorInfo.className = 'alert';
                errorInfo.classList.add('alert-success');
                errorInfo.hidden = false;
                
                storeForm[0].value = json.data.quantity;
                storeForm[1].value = json.data.q_kiev;
                
                current.parentElement.parentElement.children[2].innerText = json.data.quantity;
                current.parentElement.parentElement.children[3].innerText = json.data.q_kiev;
                
                storeForm[3].value = null;
                storeForm[4].value = null;
                
                updateBitrixProduct(json.data);
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
    
    function sub(){
        
        if(!storeForm.reportValidity()){return false;}
        
        let isQuantity = +storeForm[0].value;
        let isQKiev = +storeForm[1].value;
        let quantity = +storeForm[3].value;
        let q_kiev = +storeForm[4].value;
        
        if(quantity == 0){return false;}
        
        if(isQuantity - quantity < 0 || isQKiev - q_kiev < 0){
            errorInfo.innerText = 'Нельзя списать больше чем есть на складах!';
            errorInfo.classList.add('alert-danger');
            errorInfo.hidden = false;
            return false;
        }
        
        errorInfo.innerText = 'Отправка данных...';
        errorInfo.classList.add('alert-warning');
        errorInfo.hidden = false;
        
        const url = "./save.php";
    	
    	let form = new FormData();
    	form.append('product_id', storeForm[5].value);
    	form.append('product', document.querySelector('#storeModalLabel').innerText);
    	form.append('company', storeForm[2].value);
    	form.append('tree', section_name.value);
    	form.append('quantity', -quantity);
    	form.append('q_kiev', -q_kiev);
    	form.append('product_id', storeForm[5].value);
    	form.append('user', document.querySelector('#user').value);
    	form.append('method', 'sub');
    	
    	send(url, form).then(json => {
            if (json.data) {
                
                errorInfo.innerText = 'Данные успешно обновлены...';
                errorInfo.className = 'alert';
                errorInfo.classList.add('alert-success');
                errorInfo.hidden = false;
                
                storeForm[0].value = json.data.quantity;
                storeForm[1].value = json.data.q_kiev;
                
                current.parentElement.parentElement.children[2].innerText = json.data.quantity;
                current.parentElement.parentElement.children[3].innerText = json.data.q_kiev;
                
                storeForm[3].value = null;
                storeForm[4].value = null;
                
                updateBitrixProduct(json.data);
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
		    
    BX24.callMethod("crm.productsection.list", {
		        order: {NAME: "ASC"},
		        filter: {SECTION_ID: section_id},
		    }, function(result){
		        
                if(result.error()){console.error(result.error());} else {
                    
                    let arr = result.data();
                    
                    if(arr.length){
                        
                        arr.forEach(element => {
                            
                            let link = document.createElement('a');
                            link.setAttribute("href", `index.php?section_id=${element.ID}`);
                            link.setAttribute("id", "submit");
                            link.innerText = element.NAME;
                            
                            sections.append(link);
                            
                        });
                        
                    }
                    
                    let back = document.querySelector('#back');
                    
                    BX24.callMethod("crm.productsection.get", {id: section_id}, function(result){
                        let data = result.data();
                        if(!data.SECTION_ID){data.SECTION_ID = start_section_id;}
                        back.setAttribute("href", `index.php?section_id=${data.SECTION_ID}`);
                        section_name.value = data.NAME;
                    });
                    
                }
                    
            });
            
    BX24.callMethod("crm.product.list", {
		        order: {NAME: "ASC"},
		        filter: {SECTION_ID: section_id},
		        select: ['ID','NAME'],
		    }, function(result){
		        
                if(result.error()){console.error(result.error());} else {
                    
                    let arr = result.data();
                    
                    if(arr.length){
                        
                        const url = "./save.php";
    	
                    	let form = new FormData();
                    	form.append('products', JSON.stringify(arr));
                    	form.append('method', 'get_quantity');
                    	
                    	send(url, form).then(json => {
                            if (json.data) {
                                
                                products.innerHTML = `<table class="table table-hover align-middle text-center">
                            		<tr style='background-color:#cbf9ff'>
                              			<th>№ п/п</th>
                              			<th>Наименование</th>
                              			<th>На складе</th>
                              			<th>Из них в Киеве</th>
                              			<th></th>
                              		</tr></table>`;
                              		
                              	let table = products.children[0];
                              	
                              	let p = json.data;
                              	p.forEach((element, index) => {
                                    
                                    let tr = document.createElement('tr');
                                    
                                    tr.innerHTML = `<tr>
                          				<td>${index + 1}</td>
                          				<td class='text-start'>${element.NAME}</td>
                          				<td>${element.quantity}</td>
                          				<td>${element.q_kiev}</td>
                          				<td>
                          				    <button class="btn btn-light border border-info" data-bs-toggle="modal" data-bs-target="#storeModal" onclick='prepare(this)'><img src='img/storage.png'>
                          				    <span class='ms-2'>+ / -</span></button>
                          				    <span hidden>${element.ID}</span>
                          				    <a href="product_log.php?product_id=${element.ID}&product_name=${element.NAME}&is_admin=${is_admin}" target="_blank">
                          				        <button class="btn btn-light border border-info ms-2">история...</button>
                          				    </a>
                          				</td>
                          				    
                         			</tr>`;
                                    table.children[0].append(tr);
                                    
                                });
                                
                            }else{
                                console.log('Помилка серверу!');
                		        return false;
                            }
                        });
                        
                    }
                    
                }
                    
            });
            
    

    BX24.callMethod("profile", {}, function (res){
		let temp_user = res.answer.result.NAME + " " + res.answer.result.LAST_NAME;
		document.getElementById("user").value = temp_user;
	});
	
	function openApplication() {
		BX24.closeApplication();
		BX24.openApplication();
	}
	
	function updateBitrixProduct(data){
	    
	    BX24.callMethod("crm.product.update", { 
            id: data.id,
            fields: { 
                <?= FIELD_QUANTITY ?>: data.quantity, 
                <?= FIELD_Q_KIEV ?>: data.q_kiev
            }				
        }, 
        function(result){if(result.error()){console.error(result.error());} else{
                console.info(result.data());
                return true;
            }
        });
	    
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