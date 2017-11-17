<?php

return [
	'driver'      => env('FCM_PROTOCOL', 'http'),
	'log_enabled' => true,

	'http' => [
		'server_key'       => env('FCM_SERVER_KEY', 'AIzaSyC8mPS-6RvU3d7Z68rLjYbjsUY3t1NQrN4'),
		'sender_id'        => env('FCM_SENDER_ID', '774570931076'),
		'server_send_url'  => 'https://fcm.googleapis.com/fcm/send',
		'server_group_url' => 'https://android.googleapis.com/gcm/notification',
		'timeout'          => 30.0, // in second
	]
];
