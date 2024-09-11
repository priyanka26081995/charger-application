<?php

	setcookie(COOKIE_USER_ID, $_SESSION['login_eviot_user_id'],  time()+86400);
	$_COOKIE[COOKIE_USER_ID] = $_SESSION['login_eviot_user_id'];
	setcookie(COOKIE_TOKEN, $sk_token,  time()+86400);
	$_COOKIE[COOKIE_TOKEN] = $sk_token;

?>