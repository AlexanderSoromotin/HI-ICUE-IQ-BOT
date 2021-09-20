<?php
error_reporting(E_ALL);
ob_start();
// Hi ICUE | Bot
include "db.php";
include "settings.php";

// Отправка сообщения (текст сообщение)
function sendMessage ($text) {
	global $access_token;
	global $chat_id;
	global $apiVer;

	$request_params = array(
		'message' => $text, 
		'peer_id' => $chat_id, 
		'access_token' => $access_token,
		'v' => $apiVer
	);

	$get_params = http_build_query($request_params); 
	file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
};

// Удаление из $string частички $cutPie
function cutString ($string, $cutPie) {
	return str_replace($cutPie, '', $string);
};

// Получение информации о пользователе из ВК
function getUserInfoFromVK ($type) {
	global $user_id;
	global $access_token;
	global $apiVer;
	global $connection;

	$request_params = array(
		'user_ids' => $user_id,
		'v' => $apiVer,
		'access_token' => $access_token
	);

	$get_params = http_build_query($request_params);
	$result = json_decode(file_get_contents('https://api.vk.com/method/users.get?'. $get_params));

	$first_name = addslashes($result -> response[0] -> first_name);
	$last_name = addslashes($result -> response[0] -> last_name);

	// $first_name = addcslashes($first_name);
	// $last_name = addcslashes($last_name);

	if ($type == 'first_name') {
		return $first_name;
	}
	else if ($type == 'last_name') {
		return $last_name;
	}
	else if ($type == 'both') {
		return $first_name . " " . $last_name;
	}
};

// Получение информации о пользователе из БД
function getUserInfoFromDB ($type) {
	global $connection;
	global $user_id;

	$user = mysqli_fetch_assoc( mysqli_query($connection, "SELECT * FROM `users` WHERE `user_id` = '$user_id' ") );

	if ($type == 'id') {
		return $user['id'];
	}
	if ($type == 'user_id') {
		return $user['user_id'];
	}
	if ($type == 'first_name') {
		return $user['first_name'];
	}
	if ($type == 'last_name') {
		return $user['last_name'];
	}
	if ($type == 'iq') {
		return $user['iq'];
	}
	if ($type == 'registration_date') {
		return $user['registration_date'];
	}
	if ($type == 'cmd_date') {
		return $user['cmd_date'];
	}
	if ($type == 'both_name') {
		return $user['first_name'] . ' ' . $user['last_name'];
	}
}

// Проверка регестрации пользователя
function checkUserRegestration ($action) {

	global $connection;
	global $user_id;
	global $chat_id;

	$result = mysqli_fetch_array( mysqli_query($connection, "SELECT * FROM `users` WHERE `user_id` = '$user_id'") );

	// Пользователь есть - True
	// Пользователя нет - False
	if ($action == 'check') {
		if (!is_null($result)) {
			// Пользователь есть в БД
			return true;
		} else {
			// Пользователя нет в БД
			return false;
		}
	}

	// Если пользователя нет, то его зарегистрирует
	if ($action == 'registration') {
		if (is_null($result)) {
			// sendMessage('пользователя нет');
			// Пользователя нет в БД
			// sendMessage('пользователя нет в бд');
			$first_name = getUserInfoFromVK('first_name');
			$last_name = getUserInfoFromVK('last_name');
			mysqli_query($connection, "INSERT INTO `users` (`user_id`, `first_name`, `last_name`) VALUES ('$user_id', '$first_name', '$last_name')");
		}
	}

	// Проверка, не изменил ли пользователь беседу
	if ($result['chat_id'] != $chat_id) {
		mysqli_query($connection, "UPDATE `users` SET `chat_id` = '$chat_id' WHERE `user_id` = '$user_id' ");
	}

	// Проверка, не изменил ли пользователь имя и фамилию
	if ($result['first_name'] != getUserInfoFromVK('first_name') or $result['last_name'] != getUserInfoFromVK('last_name')) {

		$first_name = getUserInfoFromVK('first_name');
		$last_name = getUserInfoFromVK('last_name');

		mysqli_query($connection, "UPDATE `users` SET `first_name` = '$first_name', `last_name` = '$last_name' WHERE `user_id` = '$user_id' ");
	}
}

// Проверка регестрации беседы
function checkChatRegestration ($action) {

	global $connection;
	global $user_id;
	global $chat_id;

	$result = mysqli_fetch_array( mysqli_query($connection, "SELECT * FROM `chats` WHERE `chat_id` = '$chat_id'") );

	if ($action == 'check') {
		if ($result != '') {
			// Час есть в БД
			return true;
		} else {
			// Часа нет в БД
			return false;
		}
	}

	// Если пользователя нет, то его зарегистрирует
	if ($action == 'registration') {
		if ($result == '') {
			// Чата нет в БД
			// sendMessage('чата нет в бд');
			mysqli_query($connection, "INSERT INTO `chats` (`chat_id`) VALUES ('$chat_id')");
		}
	}
}

// Проверка наличия пользователя в чате
// 1. Проверка: Зарегистрирован ли пользователь в БД
// 2. Проверка: Зарегистрирована ли Беседа в БД
// 3. Поверка: Записано ли в БД о том, что пользователь находится в этой беседе
function checkUserInTheChat () {

	global $connection;
	global $user_id;
	global $chat_id;

	checkUserRegestration('registration');
	checkChatRegestration('registration');

	$result = mysqli_fetch_array( mysqli_query($connection, "SELECT `users` FROM `chats` WHERE `chat_id` = '$chat_id'") )[0];
	// sendMessage('############################ begin - ' . $result);
	$arr = unserialize($result);
	$arr2 = unserialize($result);

	if (empty($arr)) {
		$arr = array();
		array_push($arr, $user_id);
		// sendMessage('array is empty, add: ' . $user_id);
	} else {

		$flag = 0;
		foreach ($arr as $value) {
			if ($value == $user_id) {
				$flag++;
			}
			// sendMessage('foreach array - ' . $value);
		}

		if ($flag == 0) {
			array_push($arr, $user_id);
			// sendMessage('flag is 0');
		}
	}

	// sendMessage('(############################ end [0] - ' . $arr[0]);
	$result = serialize($arr);

	if ($flag == 0 or empty($arr2)) {
		// sendMessage('Load to DB');
		mysqli_query($connection, "UPDATE `chats` SET `users` = '$result' WHERE `chat_id` = '$chat_id' ");
	}
}

function updateTotalIqInChat () {

	global $connection;
	global $chat_id;
	global $user_id;

	// Обновляем значение total_iq
	// Получаем список пользователей беседы
	$result = mysqli_fetch_array( mysqli_query($connection, "SELECT `users` FROM `chats` WHERE `chat_id` = '$chat_id'") )[0];
	$arr = unserialize($result);

	// Считаем сумму iq всех участников
	$count = 0;
	foreach ($arr as $value) {
		$iq = mysqli_fetch_assoc( mysqli_query($connection, "SELECT `iq` FROM `users` WHERE `user_id` = '$value' ") )['iq'];
		// sendMessage($iq);
		$count += $iq;
	};
	mysqli_query($connection, "UPDATE `chats` SET `total_iq` = '$count' WHERE `chat_id` = '$chat_id' ");

	return $count;
}
function userIsAdmin () {
	global $user_id;
	global $admins;

	if (gettype(array_search($user_id, $admins)) == "boolean") {
		return false;
	} else {
		return true;
	}
}




$data = json_decode(file_get_contents('php://input'));

switch ($data->type) {  

	// Событие: Подтверждение
	case 'confirmation': 
		echo $confirmation_token; 
	break;  
	
	// Событие: Новое сообщение
	case 'message_new': 
		echo "ok";
		$message_text = mb_strtolower($data -> object -> text); 	// Сообщение пользователя
		$chat_id = $data -> object ->  peer_id; 					// Идентификатор назначения
		$user_id = $data -> object -> from_id; 

		// Если боту пишет какое-то сообщество, то происходит игнор
		if (strpos($user_id, '-') !== false) {
			// echo "ok";
			// exit();
			break;
		}

		function increase_iq () {
			global $user_id;
			global $chat_id;
			global $message_text;
			global $connection;

			// Время прошлого использования команды /iq
			$cmd_date = explode(', ', getUserInfoFromDB('cmd_date'));
			$cmd_d = $cmd_date[0];
			$cmd_m = $cmd_date[1];
			$cmd_y = $cmd_date[2];

			// Время на данный момент
			$current_date = date('d, m, Y');
			$current_d = date('d');
			$current_m = date('m');
			$current_y = date('Y');

			$flag = 0;

			// Проверка даты
			if (getUserInfoFromDB('cmd_date') != '') {
				if ($current_y > $cmd_y) {
					$flag = 1;
				}
				if ($current_m > $cmd_m) {
					$flag = 1;
				}
				if ($current_d > $cmd_d) {
					$flag = 1;
				}
			}
			
			// sendMessage($current_d . ' - ' . $cmd_d);
			if (getUserInfoFromDB('cmd_date') == '' or $flag == 1) {
				$iq = getUserInfoFromDB('iq');
				$type = rand(1, 3);

				if ($iq <= 10) {
					$type = 1;
				}

				if ($type == 3) {
					// Не повезло, iq уменьшился;
					$rand = rand(1, 9);
					if ($iq < $rand) {
						$rand = $iq - 1;
					}
					$iq -= $rand;
					sendMessage(getUserInfoFromDB('both_name') . ', ваш IQ понизился на ' . $rand . '. Теперь он составляет ' . $iq);

				} else {
					// Повезло, iq увеличился;
					$rand = rand(2, 10);
					$iq += $rand;
					sendMessage(getUserInfoFromDB('both_name') . ', ваш IQ повысился на ' . $rand . '. Теперь он составляет ' . $iq);
				}	

				mysqli_query($connection, "UPDATE `users` SET `iq` = '$iq', `cmd_date` = '$current_date' WHERE `user_id` = '$user_id' ");

				updateTotalIqInChat();
			} else {
				sendMessage(getUserInfoFromDB('both_name') . ', Вы сегодня уже использовали эту команду');
			}
		}

		function display_top () {
			global $user_id;
			global $chat_id;
			global $message_text;
			global $connection;

			// sendMessage($chat_id);

			// Получение массива пользователей беседы
			// Формат ( ID1, ID2, ... IDn )
			$result = mysqli_fetch_array( mysqli_query($connection, "SELECT `users` FROM `chats` WHERE `chat_id` = '$chat_id'") )[0];
			// sendMessage('############################ begin - ' . $result);
			$arr = unserialize($result);

			// Создаём и заполняем новый массив пользователей с количеством iq
			// Формат ( [ID1] => IQ1, [ID2] => IQ2, ... [IDn] => IQn )
			$arr2 = array();
			foreach ($arr as $value) {
				$iq = mysqli_fetch_assoc( mysqli_query($connection, "SELECT `iq` FROM `users` WHERE `user_id` = '$value' ") )['iq'];

				// sendMessage($value);
				// echo 'value - ' . $value . ' | iq - ' . $iq . ' ## ';

				$arr2[$value] = $iq;
			};

			$text = 'Умничи беседы:
';

			// Сортируем массив
			arsort($arr2);
			// print_r($arr2);

			// Формируем список
			$count = 1;
			foreach ($arr2 as $key => $value) {
				// sendMessage('key: ' . $key . ' | value: ' . $value);
				$user = mysqli_fetch_assoc( mysqli_query($connection, "SELECT * FROM `users` WHERE `user_id` = '$key' ") );
				$first_name = $user['first_name'];
				$last_name = $user['last_name'];

				$text = $text . $count . '. ' . $first_name . ' ' . $last_name . ' - ' . $value . ' iq
';
			 	$count++;
			};
			sendMessage($text);
		}

		function display_full_top () {
			global $user_id;
			global $chat_id;
			global $message_text;
			global $connection;

			$users = mysqli_query($connection, "SELECT * FROM `users` ORDER BY `iq` DESC LIMIT 0, 50");

			$text = 'Общий рейтинг (1 - 50):
';
			$count = 1;
			while ($user = mysqli_fetch_assoc($users)) {
				$first_name = $user['first_name'];
				$last_name = $user['last_name'];
				$iq = $user['iq'];
				$user_id_local = $user['user_id'];

				if ($user['joined'] == true) {
					$join_status = "&#9989;";
				} else {
					$join_status = "&#9940;";
				}

				$text = $text . $join_status . $count . '. [id' . $user_id_local . '|' . $first_name . ' ' . $last_name . '] - ' . $iq . ' iq 
';
				$count++;
			}

			sendMessage($text);
		}

		function display_general_iq () {
			global $user_id;
			global $chat_id;
			global $message_text;
			global $connection;

			sendMessage('Общий интеллект вашей группы: ' . updateTotalIqInChat() . ' iq');
		}

		function display_user_iq () {
			global $user_id;
			global $chat_id;
			global $message_text;
			global $connection;

			sendMessage(getUserInfoFromDB('both_name') . ', ваш интеллект составляет ' . getUserInfoFromDB('iq') . ' iq');
		}

		function display_info () {
			global $user_id;
			global $chat_id;
			global $message_text;
			global $connection;

			sendMessage('

&#128073;&#127995; -iq  (Повышение IQ (!iq))
&#128073;&#127995; -мой  (Узнать, сколько у вас IQ (!мой))
&#128073;&#127995; -чат  (Общий интеллект беседы (!чат))
&#128073;&#127995; -инфо  (Список возможностей (!инфо))
');
		}

























		// Список функций
		$functions = array(
			"increase_iq" => array("!iq", "-iq"),
			"display_top" => array("!топ", "-топ", "!top", "-top"),
			"display_general_iq" => array("!чат", "-чат", "!chat", "-chat"),
			"display_user_iq" => array("!мой", "-мой", "!my", "-my", "!my iq", "-my iq"),
			"display_info" => array("!инфо", "-инфо", "!info", "-info", "!помощь", "-помощь", "!help", "-help", "Начать"),
			
			// For admins
			"active_break" => array("!тех вкл", "-тех вкл", "!включить перерыв", "-включить перерыв"),
			"deactive_break" => array("!тех выкл", "-тех выкл", "!выключить перерыв", "-выключить перерыв"),
			"display_bot_status" => array("!тех статус", "-тех статус"),
			"display_info_for_admins" => array("!тех", "-тех"),
			"display_full_top" => array("!тех топ", "-тех топ")
		);

		function findTextInArray ($text) {
		    global $functions;
		    
		    foreach ($functions as $function_name => $tags) {
		        // echo $function_name . "\n";
			    foreach($functions[$function_name] as $key2 => $value2) {
			       // echo "... " . $value2 . "\n";
			        if ($value2 == $text) {
			           // echo "... " . $value2 . "\n";
			           //echo $function_name;
		        		return $function_name;
			        }
			    }
		    }
		}
		// Обращение к боту подтверждено
		if (findTextInArray($message_text) != "") {
			checkUserInTheChat();
		}

		// Если перерыв и пользователь не является админом, то break;
		if ($break == 1 and !userIsAdmin()) {
			if (findTextInArray($message_text) != "") {
					$function_name = findTextInArray($message_text);

				mysqli_query($connection, "INSERT INTO `acts_during_break` (`user_id`, `function`, `chat_id`) VALUES ('$user_id', '$function_name', '$chat_id') ");

				sendMessage('На данный момент на сервере ведутся тех. работы. Обычно, это занимает пару часов. Ваше обращение записано, после окончания тех. работ оно будет обработано, приносим извинения за представленные неудобства.');
			}
			// echo "ok";
			// exit();
			break;
		}

		if (!userIsAdmin()) {
			// break;
		}

		// Включение перерыва (Тех. работ)
		if (findTextInArray($message_text) == "active_break" and userIsAdmin()) {
			mysqli_query($connection, "UPDATE `settings` SET `break` = 1 ");
			sendMessage('Технические работы активированы, бот отвечает только админам. Записываются обращения пользователей.');
		}
		// Отключение перерыва (Тех. работ)
		if (findTextInArray($message_text) == "deactive_break" and userIsAdmin()) {
			mysqli_query($connection, "UPDATE `settings` SET `break` = 0 ");

			$array = mysqli_query($connection, "SELECT * FROM `acts_during_break` WHERE `status` = 1");
			
			$users_array = array();
			$users_array_with_functions = array();
			// для избежания повторяющихся запросов пройдём через два фильтра

			// Создание массива пользователей, обратившихся к боту ($users_array)
			$count = 0;
			while ($r = mysqli_fetch_assoc($array)) {
				$count++;
				if (!in_array($r['user_id'], $users_array)) {
					array_push($users_array, $r['user_id']);
				}
			}

			// Создание второго массива, ассоциация пользователя с функциями, которые первый запросил у бота (&users_array_with_functions)
			foreach ($users_array as $value) {
				$local_functions = mysqli_query($connection, "SELECT * FROM `acts_during_break` WHERE `user_id` = '$value'");
				$users_array_with_functions[$value]['functions'] = array();
				
				// sendMessage( $value . " | chatId: " . $users_array_with_functions[$value]['chat_id']);

				while ($l = mysqli_fetch_assoc($local_functions)) {
					$users_array_with_functions[$value]['chat_id'] = $l['chat_id'];
					if ( !in_array( $l['function'], $users_array_with_functions[$l['user_id']]['functions'] ) ) {
						// sendMessage($l['function'] . " pushed in " . $l['user_id']);
						array_push( $users_array_with_functions[$l['user_id']]['functions'], $l['function'] );
					}
				}
			}

			// sendMessage($users_array_with_functions);
			// var_dump($users_array_with_functions);

			sendMessage('Технические работы остановлены, бот отвечает всем. В обработке ' . $count . ' обращений.');

			// Исполнение всех неповторяющихся запросов пользователей
			foreach ($users_array_with_functions as $key => $value) {

				$GLOBALS['user_id'] = $key;
				$GLOBALS['chat_id'] = $value['chat_id'];
				

				foreach ($value['functions'] as $functionName) {

					if ($functionName == 'increase_iq') {
						increase_iq();
					}
					if ($functionName == 'display_top') {
						display_top();
					}
					if ($functionName == 'display_general_iq') {
						display_general_iq();
					}
					if ($functionName == 'display_user_iq') {
						display_user_iq();
					}
					if ($functionName == 'display_info') {
						display_info();
					}
				}
			}
			mysqli_query($connection, "DELETE FROM `acts_during_break`");








			// $act = mysqli_query($connection, "SELECT * FROM `acts_during_break`");
			

			

			// while ($g = mysqli_fetch_assoc($act)) {

				

				// $id = $g['id'];
				// $GLOBALS['user_id'] = $g['user_id'];
				// $GLOBALS['chat_id'] = $g['chat_id'];
				// $function = $g['function'];

				// if ($function == 'increase_iq') {
					// increase_iq();
				// }
				// if ($function == 'display_top') {
					// display_top();
				// }
				// if ($function == 'display_general_iq') {
					// display_general_iq();
				// }
				// if ($function == 'display_user_iq') {
					// display_user_iq();
				// }
				// if ($function == 'display_info') {
					// display_info();
				// }

				// mysqli_query($connection, "DELETE FROM `acts_during_break` WHERE `id` = '$id'");
				
			// }

			// sendMessage('Технические работы остановлены, бот отвечает всем. Выполнено ' . $count . ' обращений, оставленных во время перерыва.');
		}
		// Общие параметры бота и группы
		if (findTextInArray($message_text) == "display_bot_status" and userIsAdmin()) {
			$text = "";
			$usersCount = mysqli_fetch_array( mysqli_query($connection, "SELECT COUNT(*) FROM `users` ") )[0];
			$usersAreJoinedInGroup = mysqli_fetch_array( mysqli_query($connection, "SELECT COUNT(*) FROM `users` WHERE `joined` = 'true' ") )[0];
			
			if ($break == 1) {
				$text = $text . "Технические работы активированы.
";
			} else {
				$text = $text . "Технические работы остановлены.
";
			}
			$text = $text . "Пользователи: " . $usersCount . " чел.
Подписаны на группу: " . $usersAreJoinedInGroup . "/" . $usersCount . " чел.";

			sendMessage($text);
		}
		if (findTextInArray($message_text) == "display_info_for_admins" and userIsAdmin()) {
			sendMessage('-тех вкл (вкл. перерыв)
-тех выкл (выкл. перерыв)
-тех статус (общие параметры)
-тех (справка по командам)');
		}

		
		// Манипуляции с iq
		if (findTextInArray($message_text) == "increase_iq") {
			increase_iq();
		}
		// Отображение рейтинга
		if (findTextInArray($message_text) == "display_top") {
			display_top();
		}
		// Общий топ
		if (findTextInArray($message_text) == "display_full_top") {
			display_full_top();
		}

		// Общая составляющая интеллекта беседы
		else if (findTextInArray($message_text) == "display_general_iq") {
			display_general_iq();
		}

		// Отображение iq пользователя
		else if (findTextInArray($message_text) == "display_user_iq") {
			display_user_iq();
		}

		// Информация о возможностях бота
		else if (findTextInArray($message_text) == "display_info") {
			display_info();
		}

		if (strpos($message_text, 'jkl') !== false) {
			$array = mysqli_query($connection, "SELECT * FROM `acts_during_break`");

			$users_array = array();
			$users_array_with_functions = array();
			while ($r = mysqli_fetch_assoc($array)) {
				// sendMessage($r['user_id']);
				if (!in_array($r['user_id'], $users_array)) {
					array_push($users_array, $r['user_id']);
				}
			}

			// sendMessage("1 array: " . json_encode($users_array));
			// var_dump($users_array);

			foreach ($users_array as $value) {
				$functions = mysqli_query($connection, "SELECT * FROM `acts_during_break` WHERE `user_id` = '$value'");
				$users_array_with_functions[$value] = array();
				// sendMessage("GG - " . $value);
				while ($l = mysqli_fetch_assoc($functions)) {

					if ( !in_array( $l['function'], $users_array_with_functions[$l['user_id']] ) ) {
						// sendMessage($l['function'] . " pushed in " . $l['user_id']);
						array_push( $users_array_with_functions[$l['user_id']], $l['function'] );
					}
				}
			}
		}







		// 
		// else if ($message_text == "XXXX" or $message_text == "/XXXX") {
		// 	sendMessage('');
		// }
		//
		// if (strpos($message_text, 'XXXX') !== false) {
		// 	sendMessage('');
		// }

	// echo 'ok';
	break;

	// Новая запись на стене сообщества
	case 'wall_post_new':
		$wall_id = $data -> object -> id;
		$owner_id = $data -> object -> owner_id;
		$wall_text = $data -> object -> text;

		if (strpos($wall_text, 'умничей на сегодня') !== false or strpos($wall_text, '#meme') !== false) {
			break;
		}

		$chat_ids = mysqli_query($connection, "SELECT `chat_id` FROM `chats`");

		while ($i = mysqli_fetch_assoc($chat_ids)) {
			$chat_id = $i['chat_id'];

			if (strlen($chat_id) >= 10) {
				$request_params = array(
					'message' => 'На стене сообщества появилась новая запись, может что-то интересное!', 
					'peer_id' => $chat_id, 
					'access_token' => $access_token,
					'attachment' => 'wall' . $owner_id . '_' . $wall_id,
					'v' => $apiVer
				);

				$get_params = http_build_query($request_params); 
				file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
			}
		}

		

		echo 'ok';
	break;

	case 'group_join':
		$user_id = $data -> object -> user_id;
		$chat_id = $user_id;

		checkUserRegestration('registration');
		$joined = mysqli_fetch_assoc(mysqli_query($connection, "SELECT `joined` FROM `users` WHERE `user_id` = '$user_id' "))['joined'];

		if ($joined == '') {

			mysqli_query($connection, "UPDATE `users` SET `iq` = `iq` + 20, `joined` = 'true' WHERE `user_id` = '$user_id' ");

			sendMessage('Добро пожаловать в клуб анонимных интеллектуалов, вам начислено 20 iq! 
Для получения информации о командах напишите -инфо ');
		}

		


		echo 'ok';
	break;
};

