<!DOCTYPE html>
<html>
<body>
<?php
for ($i = 65; $i <=70; $i++) {
	echo $i . ' - ' . chr($i) . "<br>";
}
$new_ep = 417;
echo '[' . $new_ep . ']';
$new_ep = (round(($new_ep / 100) + 1) * 100) + 1;
echo '[' . $new_ep . ']';
?>
	<form name="postaction" method="post" action="postaction.php">
		Action: <input type="text" name="action"><br>
		Title: <input type="text" name="title"><br>
		Dateshift: <input type="number" name="dateshift"><br>
		<input type="submit" name="submit" value="Submit">
	</form>
</body>
</html>