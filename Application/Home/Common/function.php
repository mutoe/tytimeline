<?php

function get_t_height($w,$h,$ww = 200) {
	return floor($h * $ww / $w);
}