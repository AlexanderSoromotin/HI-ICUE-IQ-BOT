<?php
// Mega IQ | Bot

// Подключение к локальной бд
// $db_url = "localhost";
// $db_username = "root";
// $db_password = "root";
// $db_name = "iq";

// Подключение к БД на MACHost
$db_url = "a369307.mysql.mchost.ru";
$db_username = "a369307_1";
$db_password = "8v6368516Ani";
$db_name = "a369307_1";

// Подключение к БД на RemoteMySQL
// $db_url = "www.remotemysql.com";
// $db_username = "AispycQsAC";
// $db_password = "MrltA1zLrd";
// $db_name = "AispycQsAC";

// Соединение с БД
$connection = mysqli_connect($db_url, $db_username, $db_password, $db_name);
$connection -> set_charset("utf8");

// Строка, которую должен вернуть сервер
$confirmation_token = '33bc11bc';

// Токен группы
$access_token = '589b84396d5d257372844de9597ccf559f2c92c81af1e0f4547dcbecfb650663b306abc38eaa89ee4aff3';

$user_token = 'cfaf2ad204a1ad807cf53e014628d199eecec32e0050f954dd7bdb4c24ba1257db7579317ac100c75afe7';

// ID Группы
$group_id = '205976257';

// Версия VK API
$apiVer = '5.87';



// $rows = mysqli_query($connection, "SELECT * FROM `users`");

// while ($row = mysqli_fetch_array($rows)) {
// 	echo "<br>USERS | ID : " . $row['id'];
// 	echo "<br>USERS | FIRST NAME : " . $row['first_name'];
// 	echo "<br>USERS | LAST NAME : " . $row['last_name'];
// 	echo "<br>USERS | IQ : " . $row['iq'];
	
// };

?>