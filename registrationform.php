<html>
<body>

<form action="registrationform.php" method="post">
Firstname: <input type="text" name="firstname">
Lastname: <input type="text" name="lastname">
Age: <input type="text" name="age">
<input type="submit">
</form>

</body>
</html>

<?php
require_once('db.php');

if(isset($_POST['firstname'])) {
	$db = new DB;
	$result = $db->query('SELECT * from core_user');
	echo"<pre>";
	die(print_r($result->rows));

}
?>