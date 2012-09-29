<?php
global $admin_ip;
$admin_ip='127.0.0.1';
function checkip($fileid) {
	$ip=file_get_contents('./records/'.$fileid.'.rec');
	$ip=substr_replace($ip, '', 0, strpos($ip, '|~ip~|')+6);
	$ip=substr_replace($ip, '', strpos($ip, '|`ip`|'));
	if ($_SERVER['REMOTE_ADDR']==$ip or $_SERVER['REMOTE_ADDR']==$GLOBALS['admin_ip']) {
		return true;
	}else{
		return false;
	}
}
function showmessage($string) {
	return "<div class=\"dialog-round\">\n<span class=\"d1\"></span><span class=\"d2\"></span><span class=\"d3\"></span>\n<div><h2>$string</h2></div>\n<span class=\"d3\"></span><span class=\"d2\"></span><span class=\"d1\"></span>\n</div><br>";
}
if (!is_dir('./records')) {
	mkdir('./records');
}
function plusincount() {
	if (is_readable('./records/count')) {
	if (is_writable('./records/count')) {
		$count=file_get_contents('./records/count');
		unlink('./records/count');
		$handle = fopen('./records/count', 'w+');
		fwrite($handle, $count+1);
		fclose($handle);
		return $count+1;
	}else{
		unlink('./records/count');
		$handle=fopen('./records/count', 'w+');
		fwrite($handle, '0');
		fclose($handle);
		minusincount();
	}
	}else{
		$handle=fopen('./records/count', 'w+');
		fwrite($handle, '0');
		fclose($handle);
		minusincount();
	}
}
function minusincount() {
	if (is_readable('./records/count')) {
	if (is_writable('./records/count')) {
		$count=file_get_contents('./records/count');
		unlink('./records/count');
		$handle = fopen('./records/count', 'w+');
		fwrite($handle, $count-1);
		fclose($handle);
		return $count-1;
	}else{
		unlink('./records/count');
		$handle=fopen('./records/count', 'w+');
		fwrite($handle, '1');
		fclose($handle);
		minusincount();
	}
	}else{
		$handle=fopen('./records/count', 'w+');
		fwrite($handle, '1');
		fclose($handle);
		minusincount();
	}
}
function getfooter() {
	return "<div class=\"dialog-round\">\n<span class=\"d1\"></span><span class=\"d2\"></span><span class=\"d3\"></span>\n<div>\n<center>\n&copy; 2012, <a href=\"http://vk.com/id51195914\">iNikit</a>\n</center>\n</div>\n<span class=\"d3\"></span><span class=\"d2\"></span><span class=\"d1\"></span>\n</div><br>";
}
?>