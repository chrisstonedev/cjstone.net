<?php
	$host = "ACCESS_DENIED";
	$user = "ACCESS_DENIED";
	$pass = "ACCESS_DENIED";
	
	$audience = htmlspecialchars($_GET["aud"]);
	$category = htmlspecialchars($_GET["cat"]);
	$sort = htmlspecialchars($_GET["srt"]);
	
	try {
		$con = new PDO($host, $user, $pass);
		$sql = "SELECT * FROM series WHERE CATEGORY";
		if ($category == "X") {
			$sql = $sql . " IN ('A','B','C') AND NEXT_DATE <= '" . date("Y-m-d", time() + 60*60*24)."'";
		} else {
			$sql = $sql . "='$category'";
		}
		$sql = $sql . " AND AUDIENCE='$audience' ORDER BY ";
		if ($sort == "T") {
			$sql = $sql . "TITLE";
		} else {
			$sql = $sql . "NEXT_DATE";
		}
		$result = $con->query($sql);
		
		$int_num_field = $result->columnCount();
		$json = array();
		
		//array_push($json,$sql);
		while($row = $result->fetch()){
			$arr_col = array();
			for($i=0; $i<$int_num_field; $i++){
				$arr_col[$result->getColumnMeta($i)['name']] = $row[$i];
			}
			array_push($json,$arr_col);
		}
		$con = null;
		header('Content-type: text/json');
		echo json_encode($json);
	} catch ( PDOException $e ) {
		$con = null;
		return $e->getMessage();
	}
?>