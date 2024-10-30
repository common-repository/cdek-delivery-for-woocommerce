<?php
header('Content-Type: application/json; charset=utf-8');
	$json = file_get_contents('city-code.json');
	$new_json = array();
	if(isset($_GET['term']['term'])){
		$json = json_decode($json);

		foreach ($json as $item) {
		    if (stripos(mb_strtolower($item->text),mb_strtolower($_GET['term']['term']))  !== false ) {

		        $new_json[] = array(
		        	'id' => $item->id,
		        	'name' => $item->text.' ( Регион: '.$item->region.')'
		        )
		        ;
		       
		    }
		}

		$arr['items'] = json_encode($new_json);
	echo $arr['items'];
	}


		?>
