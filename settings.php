<?php
	$break = mysqli_fetch_assoc( mysqli_query($connection, "SELECT * FROM `settings`") )['break'];
	// $break = 0;
	$admins = array(235359833, 295082014);

	// 235359833 : Соромотин Александр
	// 295082014 : Сушилов Евгений
?>