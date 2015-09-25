<?php
include_once("config.php");

if ( isset($_POST['action']) and $_POST['action'] != "") {
	if ($_POST['action'] == "watch") {
		try {
			$con = new PDO($host, $user, $pass);
			$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$query = "SELECT * FROM series WHERE TITLE = :title";
			$stmt = $con->prepare( $query );
			$stmt->bindValue( "title", $_POST['title'], PDO::PARAM_INT );
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($result == null)
				throw new Exception('Series does not exist in database.');
			$new_ep = $result["NEXT_EP"] + 1;
			$new_date = strtotime($result["NEXT_DATE"]);
			if ($_POST['title'] == "@midnight with Chris Hardwick" or $_POST['title'] == "Nightly Show with Larry Wilmore, The" or $_POST['title'] == "Daily Show with Trevor Noah, The") {
				if (date("D", $new_date) == "Fri")
					$new_date = $new_date + 60*60*24*4;
				else
					$new_date = $new_date + 60*60*24;
			} else if ($_POST['title'] == "Late Show with Stephen Colbert, The") {
				if (date("D", $new_date) == "Sat")
					$new_date = $new_date + 60*60*24*3;
				else
					$new_date = $new_date + 60*60*24;
			} else if ($result["AUDIENCE"] == "C" and $result["CATEGORY"] == "B") {
				$freq = (strtotime($result["SSN_FINALE"]) - time())/60/60/24;
				$freq = $freq / ($result["TOTAL_EPS"] - $new_ep + 2);
				$freq = substr($freq, 0, strpos($freq, '.') + 2);
				if (strlen($freq) > 4) {
					$freq = "99.9";
				}
				$freq = substr($freq, 0, strpos($freq, '.'));
				if ($freq > 7) {
					$freq = 7;
				} else if ($freq < 1) {
					$freq = 1;
				}
				$freq = $freq + round((time() - strtotime($result["NEXT_DATE"]))/60/60/24);
				$new_date = $new_date + ($freq * 24*60*60);
			}
			else if ($result["CATEGORY"] == "C") {
				$new_date = $new_date + ((round((time() - strtotime($result["NEXT_DATE"]))/60/60/24) + 7) * 24*60*60);
			} else {
				$new_date = $new_date + (7 * 24*60*60);
			}
			
			if ($result["NEXT_EP"] == $result["TOTAL_EPS"]) {
				if ($result["CATEGORY"] == "A") {
					throw new Exception('TRIGGER INACTIVE');
				} else if ($result["CATEGORY"] == "B") {
					throw new Exception('TRIGGER ACTIVE');
				} else {
					throw new Exception('TRIGGER PAST');
				}
			}
			$sql = "UPDATE series SET NEXT_EP = :next_ep, NEXT_DATE = :next_date WHERE TITLE = :title";
			$stmt = $con->prepare( $sql );
			$stmt->bindValue( "next_ep", $new_ep, PDO::PARAM_INT );
			$stmt->bindValue( "next_date", date("Y-m-d", $new_date), PDO::PARAM_STR );
			$stmt->bindValue( "title", $result["TITLE"], PDO::PARAM_STR );
			$stmt->execute();
			
			if ($result["TITLE"] == "Key & Peele") {
				$new_date = $new_date + 7;
				$sql = "UPDATE series SET NEXT_DATE = :next_date WHERE TITLE = :title";
				$stmt = $con->prepare( $sql );
				$stmt->bindValue( "next_date", date("Y-m-d", $new_date), PDO::PARAM_STR );
				$stmt->bindValue( "title", $result["TITLE"]." Classic", PDO::PARAM_STR );
				$stmt->execute();
			}
			if ($result["TITLE"] == "QI XL") {
				$new_date = $new_date + 7;
				$sql = "UPDATE series SET NEXT_DATE = :next_date WHERE TITLE = :title";
				$stmt = $con->prepare( $sql );
				$stmt->bindValue( "next_date", date("Y-m-d", $new_date), PDO::PARAM_STR );
				$stmt->bindValue( "title", substr($result["TITLE"], 0, 2), PDO::PARAM_STR );
				$stmt->execute();
			}
			$con = null;
		} catch ( PDOException $e ) {
			$con = null;
			echo $e->getMessage();
		} catch ( Exception $e) {
			$con = null;
			echo $e->getMessage();
		}
	} else if ($_POST['action'] == "watch_extra") {
		try {
			$con = new PDO($host, $user, $pass);
			$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$query = "SELECT * FROM series WHERE TITLE = :title";
			$stmt = $con->prepare( $query );
			$stmt->bindValue( "title", $_POST['title'], PDO::PARAM_INT );
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($result == null)
				throw new Exception('Series does not exist in database.');
			$new_ep = $result["NEXT_EP"] + 1;
			$new_date = strtotime($result["NEXT_DATE"]) + (7 * 24*60*60);
			$total_eps = $result["TOTAL_EPS"];
			$ssn_finale = -1;
			if ($result["SSN_FINALE"] != null) {
				$ssn_finale = strtotime($result["SSN_FINALE"]);
				$new_date = $ssn_finale;
			} else {
				$ssn_finale = $new_date;
			}
			
			if ($_POST['dateshift'] == ord("D") or $_POST['dateshift'] == ord("E")) {
				$new_ep = (round(($new_ep / 100) + 1) * 100) + 1;
				$ssn_finale = null;
				$total_eps = -1;
			} else if ($_POST['dateshift'] == ord("A")) {
				$new_ep = (round(($new_ep / 100) + 1) * 100) + 1;
				$ssn_finale = null;
				$total_eps = -1;
			}
			$sql = "UPDATE series SET NEXT_EP = :next_ep, NEXT_DATE = :next_date, CATEGORY = :category, TOTAL_EPS = :total_eps, SSN_FINALE = :ssn_finale WHERE TITLE = :title";
			$stmt = $con->prepare( $sql );
			$stmt->bindValue( "next_ep", $new_ep, PDO::PARAM_INT );
			$stmt->bindValue( "next_date", date("Y-m-d", $new_date), PDO::PARAM_STR );
			$stmt->bindValue( "category", chr($_POST['dateshift']), PDO::PARAM_STR );
			if ($total_eps <= 0) {
				$stmt->bindValue( "total_eps", null, PDO::PARAM_NULL );
			} else {
				$stmt->bindValue( "total_eps", $total_eps, PDO::PARAM_INT );
			}
			if ($ssn_finale == null) {
				$stmt->bindValue( "ssn_finale", null, PDO::PARAM_NULL );
			} else {
				$stmt->bindValue( "ssn_finale", date("Y-m-d", $ssn_finale), PDO::PARAM_STR );
			}
			$stmt->bindValue( "title", $result["TITLE"], PDO::PARAM_STR );
			$stmt->execute();
			
			$con = null;
		} catch ( PDOException $e ) {
			$con = null;
			echo $e->getMessage();
		} catch ( Exception $e) {
			$con = null;
			echo $e->getMessage();
		}
	} else if ($_POST['action'] == "dateshift") {
		try {
			$con = new PDO($host, $user, $pass);
			$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$query = "SELECT * FROM series WHERE TITLE = :title";
			$stmt = $con->prepare( $query );
			$stmt->bindValue( "title", $_POST['title'], PDO::PARAM_INT );
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($result == null)
				throw new Exception('Series does not exist in database.');
			$new_date = strtotime($result["NEXT_DATE"]) + (60*60*24 * $_POST['dateshift']);
			
			$sql = "UPDATE series SET NEXT_DATE = :next_date WHERE TITLE = :title";
			$stmt = $con->prepare( $sql );
			$stmt->bindValue( "next_date", date("Y-m-d", $new_date), PDO::PARAM_STR );
			$stmt->bindValue( "title", $result["TITLE"], PDO::PARAM_STR );
			$stmt->execute();
			
			$con = null;
		} catch ( PDOException $e ) {
			$con = null;
			echo $e->getMessage();
		} catch ( Exception $e) {
			$con = null;
			echo $e->getMessage();
		}
	} else if ($_POST['action'] == "update") {
		try {
			$con = new PDO($host, $user, $pass);
			$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			
			$sql = "UPDATE series SET CATEGORY = :category WHERE TITLE = :title";
			$stmt = $con->prepare( $sql );
			$stmt->bindValue( "category", chr($_POST['dateshift']), PDO::PARAM_STR );
			$stmt->bindValue( "title", $_POST['title'], PDO::PARAM_STR );
			$stmt->execute();
			
			$con = null;
		} catch ( PDOException $e ) {
			$con = null;
			echo $e->getMessage();
		} catch ( Exception $e) {
			$con = null;
			echo $e->getMessage();
		}
	}
} else {
	echo 'Could not establish connection to database.';
}
