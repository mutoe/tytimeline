<?php

function is_super_admin() {
	$user_id = is_login();
	return $user_id == 1;
}