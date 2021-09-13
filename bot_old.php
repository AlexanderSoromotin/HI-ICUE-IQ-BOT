<?php
// Mega IQ | Bot
include "settings.php";
include "db.php";

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
		return addslashes($user['first_name']);
	}
	if ($type == 'last_name') {
		return addslashes($user['last_name']);
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
		return addslashes($user['first_name']) . ' ' . addslashes($user['last_name']);
	}
}

// function getChatInfoFromVK () {
// 	// Не работает!!!!!!!!!
// 	global $apiVer;
// 	global $user_token;
// 	global $group_token;
// 	global $group_id;
// 	global $access_token;

// 	$request_params = array(
// 		'chat_id' => '1',
// 		'v' => $apiVer,
// 		'access_token' => $user_token,
// 		'fields' => 'nickname'
// 	);

// 	$get_params = http_build_query($request_params);
// 	// $result = json_decode(file_get_contents('https://api.vk.com/method/messages.getChat?'. $get_params));
// 	$result = file_get_contents('https://api.vk.com/method/messages.getChat?'. $get_params);

// 	// if ($type == 'title') {
// 	// 	return $result -> response[0] -> title;;
// 	// }
// 	// echo $access_token;
// 	print_r($result);
// 	return $result;
// }

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





$data = json_decode(file_get_contents('php://input'));

switch ($data->type) {  

	// Событие: Подтверждение
	case 'confirmation': 
		echo $confirmation_token; 
	break;  
	
	// Событие: Новое сообщение
	case 'message_new': 

		$message_text = mb_strtolower($data -> object -> text); 	// Сообщение пользователя
		$chat_id = $data -> object ->  peer_id; 					// Идентификатор назначения
		$user_id = $data -> object -> from_id; 


		if (strpos($user_id, '-') !== false) {
			break;
			echo "ok";
		}

		global $admins;

		// sendMessage(array_search($user_id, $admins));
		if ($break == true and gettype(array_search($user_id, $admins)) == "boolean") {

			break;
			echo "ok";
		}
	
		

		// Манипуляции с iq
		if ($message_text == "!iq" or $message_text == "-iq") {
			checkUserInTheChat();

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
				$type = rand(1, 4);

				if ($iq <= 0) {
					$type = 1;
				}

				if ($type == 4) {
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

		// Отображение рейтинга
		if ($message_text == "!топ" or $message_text == "!top" or $message_text == "-топ" or $message_text == "-top") {
			checkUserInTheChat();

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

			// echo $text;
		}

		// Общий топ
// 		else if ($message_text == "!топ все" or $message_text == "!top all") {
// 			checkUserInTheChat();

// 			$users = mysqli_query($connection, "SELECT * FROM `users` ORDER BY `iq` DESC LIMIT 0, 10");

// 			$text = 'Общий рейтинг:
// ';
// 			$count = 1;
// 			while ($user = mysqli_fetch_assoc($users)) {
// 				$first_name = $user['first_name'];
// 				$last_name = $user['last_name'];
// 				$iq = $user['iq'];

// 				$text = $text . $count . '. ' . $first_name . ' ' . $last_name . ' - ' . $iq . ' iq 
// ';
// 				$count++;
// 			}

// 			sendMessage($text);
// 		}

		// Общая составляющая интеллекта беседы
		else if ($message_text == "!чат" or $message_text == "!chat" or $message_text == "-чат" or $message_text == "-chat") {
			checkUserInTheChat();

			sendMessage('Общий интеллект вашей группы: ' . updateTotalIqInChat() . ' iq');
		}

		// Отображение iq пользователя
		else if ($message_text == "!my iq" or $message_text == "!мой iq" or $message_text == "!мой интеллект" or $message_text == "!мой" or $message_text == "!отобрази мой интеллект" or $message_text == '!покажи мой интеллект' or $message_text == "-my iq" or $message_text == "-мой iq" or $message_text == "-мой интеллект" or $message_text == "-мой" or $message_text == "-my") {
			checkUserInTheChat();

			sendMessage(getUserInfoFromDB('both_name') . ', ваш интеллект составляет ' . getUserInfoFromDB('iq') . ' iq');
		}

		// Информация о возможностях бота
		else if ($message_text == "!info" or $message_text == "!инфо" or $message_text == "!information" or $message_text == "!информация" or $message_text == "!help" or $message_text == "!помощь" or $message_text == "-info" or $message_text == "-инфо" or $message_text == "-help" or $message_text == "-помощь") {
			sendMessage('

&#128073;&#127995; -iq  (Повышение IQ (!iq))
&#128073;&#127995; -мой  (Узнать, сколько у вас IQ (!мой))
&#128073;&#127995; -чат  (Общий интеллект беседы (!чат))
&#128073;&#127995; -инфо  (Список возможностей (!инфо))
');
		}






		// 
		// else if ($message_text == "XXXX" or $message_text == "/XXXX") {
		// 	sendMessage('');
		// }
		//
		// if (strpos($message_text, 'XXXX') !== false) {
		// 	sendMessage('');
		// }

	echo 'ok';
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
Для получения информации о командах напишите !инфо ');
		}

		


		echo 'ok';
	break;
};

