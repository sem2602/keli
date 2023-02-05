<?php

require_once 'DB.php';

$db = new DB();

$logs = $db->getUnexpectedLog($_GET['company_id']);

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
		<form class="icons_top" action="unexpected.php" method="POST">
			<input type="image" name="image" src="./img/undo.png" alt="" style="vertical-align:middle"/>
			<input typy='text' name='status' value='1' hidden/>
		</form>
		<input id="home" type="image" name="image" onclick="openApplication()" src="./img/home.png" alt="" style="vertical-align:middle"/>
	</div>

	<div class="header">
		<h3>История изменения</h3>
  	</div>
  	
	<div class='container'>
	    
		<table class="table table-hover align-middle text-start">
    		<tr style='background-color:#cbf9ff'>
      			<th>№ п/п</th>
    			<th>Тип действия</th>
      			<th>Пользователь</th>
    			<th>Время</th>
      			<th>Контрагент</th>
      			<th>Сумма</th>
      			<th>Описание</th>
      		</tr>
      		
      		<?php foreach($logs as $key => $log): ?>
      		
      		    <tr>
      				<td><?= $key + 1 ?></td>
    				<td><?= $log['doing_type'] ?></td>
    				<td><?= $log['user'] ?></td>
    				<td><?= $log['datetime'] ?></td>
    				<td><?= $log['company'] ?></td>
      				<td><?= $log['amount'] ?></td>
      				<td><?= $log['description'] ?></td>
 				</tr>
      		
      		<?php endforeach; ?>
      			
      	</table>
	</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
		
<script>

	function openApplication() {
		BX24.closeApplication();
		BX24.openApplication();
	}

</script>
</body></html>