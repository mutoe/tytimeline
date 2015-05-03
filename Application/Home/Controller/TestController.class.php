<?php
namespace Home\Controller;
use Think\Controller;

class TestController extends Controller {

	public function index($w = 1440, $h = 900) {
		$wlist = ["1024x768", "1152x864", "1280x768", "1280x800", "1280x1024", "1440x900", "1600x900", "1680x1050", "1920x1080",
							"1600x1200", "1366x768", "1920x1440", "1920x1440"];
		$data = $w. "x". $h;
		echo in_array($data, $wlist)?1:0;
	}
}
