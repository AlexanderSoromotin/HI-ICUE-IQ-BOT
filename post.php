<?php

include 'db.php';

function addPost ($type) {

	global $access_token;
	global $user_token;
	global $group_id;
	global $apiVer;
	global $connection;

	if ($type == 'topUsers') {
		$users = mysqli_query($connection, "SELECT * FROM `users` ORDER BY `iq` DESC LIMIT 0, 10");

		$text = 'Топ умничей на сегодня:

';
		$count = 1;
		while ($user = mysqli_fetch_assoc($users)) {
			$first_name = $user['first_name'];
			$last_name = $user['last_name'];
			$iq = $user['iq'];

			// $title = getChatInfoFromVK($user['chat_id'], 'title');

			$text = $text . $count . '. ' . $first_name . ' ' . $last_name . ' - ' . $iq . ' iq 
';
			$count++;
		}
	}

	$url = sprintf('https://api.vk.com/method/wall.post?');
  	$ch = curl_init();
  	curl_setopt_array( $ch, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_SSL_VERIFYPEER => FALSE,
    CURLOPT_SSL_VERIFYHOST => FALSE,
    CURLOPT_POSTFIELDS => array(
    	'owner_id' => '-' . $group_id,
    	'from_group' => 1,
    	'message' => $text,
    	'access_token' => $user_token,
    	'v' => $apiVer
    ),
    CURLOPT_URL => $url,
	));
	
	$query = curl_exec($ch);
	curl_close($ch);
	echo $query;
}

addPost('topUsers');



