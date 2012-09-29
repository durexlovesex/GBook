<?php
session_start();
date_default_timezone_set('Europe/Moscow');
include_once('./functions.php');
if (!is_dir('./records')) {
	mkdir('./records');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>
Гостевая книга.
</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<style type="text/css">
body { background-color: black; color: lime; }
a:link, a:visited, a:active { text-decoration: underline; color: lime !important; }
a.hint:link, a.hint:visited, a.hint:active { text-decoration: none; color: lime !important; }
input, textarea { overflow: auto; color: lime; background-color: black; border: 1px solid lime; outline: none; }
hr { height: 1px; border: none; border-top: 1px solid lime; }
P.line { border-left: 2px solid lime; margin-left: 5px; padding-left: 10px; }
P.line2 { border-left: 2px solid lime; margin-left: 7px; margin-top: -16px; padding-left: 10px; }
.d1, .d2, .d3 { display: block; font-size: 0; overflow: hidden; height: 1px; }
.d2, .d3, .dialog-round div { border-left: 1px solid lime; border-right: 1px solid lime; }
.d1 { margin: 0 4px; background: lime; }
.d2 { margin: 0 2px; border-width: 2px; }
.d3 { margin: 0 1px; height: 2px; }
.dialog-round div { padding: 5px 10px; }
div.fiftyright { float: left; border: 0 solid lime; }
div.fiftyleft { border: 0 solid lime; }
div.center { border: 0 solid black; }
span.red { color: red !important; }
h2 { text-align: center; }
</style>
</head>
<body>
<?php
if (count($_GET)>0) {
if (isset($_GET['edit'])) {
	if (is_writable('./records/'.$_GET['edit'].'.rec')) {
		if (checkip($_GET['edit'])==true) {
			echo ("<div class=\"dialog-round\">\n<span class=\"d1\"></span><span class=\"d2\"></span><span class=\"d3\"></span>\n<div>\n");
			echo ("Вы собераетесь отредактировать запись №".$_GET['edit']."\n | <a href=\"?\">На главную</a><hr>\n");
			echo ("<form action=\"?\" method=\"post\">\n");
			$oldmessage=file_get_contents('./records/'.$_GET['edit'].'.rec');
			$oldmessage=substr_replace($oldmessage, '', 0, strpos($oldmessage, '|~message~|')+11);
			$oldmessage=substr_replace($oldmessage, '', strpos($oldmessage, '|`message`|'));
			echo ("<a href=\"\" class=\"hint\" title=\"Поле обязательно к заполнению.\">Текст сообщения<span class=\"red\">*</span></a>:<br>");
			echo ("\n<textarea name=\"newmessage\" rows=\"10\" cols=\"50\" style=\"width: 80%;\">".htmlspecialchars($oldmessage)."</textarea>\n");
			echo ("<div style=\"float: right; border: 0px solid lime;\">");
			echo ("<a href=\"\" class=\"hint\" title=\"Поле обязательно к заполнению.\">Каптча<span class=\"red\">*</span></a>:<br>");
			echo ("<img src=\"./kcaptcha/?".session_name()."=".session_id()."\" style=\"border: 1px solid lime !important;\" alt=\"Каптча! Не более 2ух кб.\"><br>");
			echo ("<input name=\"keystring\" style=\"width: 156px !important; margin-top: -1px;\">");
			echo ("</div><br>\n");
			echo ("<br><input type=\"hidden\" value=\"".$_GET['edit']."\" name=\"recordid\">");
			echo ("<input type=\"submit\" value=\"Сохранить\" name=\"submiteditmessage\" style=\"width: 100%;\">\n");
			echo ("</form>\n");
			echo ("</div>\n<span class=\"d3\"></span><span class=\"d2\"></span><span class=\"d1\"></span>\n</div><br>\n".getfooter());
			exit("\n</body>\n</html>");
		}else{
			echo (showmessage('Извините, но у Вас не хватает привилегий для редактирования этой записи'));
		}
	}else{
		echo (showmessage('Данной записи не существует'));
	}
}
if (isset($_GET['delete'])) {
	if (is_writable('./records/'.$_GET['delete'].'.rec')) {
		if (checkip($_GET['delete'])==true) {
			unlink('./records/'.$_GET['delete'].'.rec');
			echo ("<div class=\"dialog-round\">\n<span class=\"d1\"></span><span class=\"d2\"></span><span class=\"d3\"></span>\n<div>");
			echo ('<h2>Запись успешно удалёна</h2>');
			echo ("</div>\n<span class=\"d3\"></span><span class=\"d2\"></span><span class=\"d1\"></span>\n</div><br>");
			minusincount();
		}else{
			echo(showmessage('Узвините, но у Вас не хватает привилегий, чтобы удалить эту запись'));
		}
	}else{
		echo (showmessage('Данной записи не существует'));
	}
}
}
if (count($_POST)>0) {
if (!empty($_POST['submiteditmessage'])) {
	if (checkip($_POST['recordid'])==true) {
	if (strlen($_POST['newmessage'])<500) {
	if (isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] === $_POST['keystring']){
		$fullmessage=file_get_contents('./records/'.$_POST['recordid'].'.rec', 'w+');
		$fullmessage=substr_replace($fullmessage, '', strpos($fullmessage, '|~message~|'));
		$fullmessage.='|~message~|'.htmlspecialchars($_POST['newmessage']).'|`message`|';
		if ($handle=fopen('./records/'.$_POST['recordid'].'.rec', 'w')) {
			fwrite($handle, $fullmessage);
			fclose($handle);
		}else{
			echo (showmessage('Ты думаешь что ты такой умный, если умеешь редактировать исходники, или отправлять другие запросы?<br>А вот нифига!'));
		}
		echo (showmessage('Сообщение №'.$_POST['recordid'].' успешно отредактировано!'));
	}else{
		echo (showmessage('Капча введена не верно.'));
	}
	}else{
		echo (showmessage('Сообщение должно быть не более 500 символов.'));
	}
	}else{
		echo (showmessage('Ты думаешь что ты такой умный, если умеешь редактировать исходники?<br>А вот нифига!'));
	}
}
if (!empty($_POST['submitrecord'])) {
	if (!empty($_POST['email'])) {
		if (!empty($_POST['message'])) {
			if (isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] === $_POST['keystring']){
				if (strlen($_POST['message'])<=500) {
					$tmpcount=plusincount();
					$handle=fopen("./records/$tmpcount.rec", 'w+');
					if (!empty($_POST['author'])) {
						$tmpauthor=$_POST['author'];
					}else{
						$tmpauthor='Anonymous';
					}
					if ($GLOBALS['admin_ip']==$_SERVER['REMOTE_ADDR']) {
						fwrite($handle, "|~author~|<font color=\"red\">".htmlspecialchars($tmpauthor)."</font>|`author`|\n");
					}else{
						fwrite($handle, "|~author~|".htmlspecialchars($tmpauthor)."|`author`|\n");
					}
					fwrite($handle, "|~date~|".date('d.m.Y')."|`date`|\n");
					fwrite($handle, "|~time~|".date('g:i:s')."|`time`|\n");
					if (!empty($_POST['website'])) {
						fwrite($handle, "|~website~|".$_POST['website']."|`website`|\n");
					}else{
						fwrite($handle, "|~website~||`website`|\n");
					}
					fwrite($handle, "|~ip~|".$_SERVER['REMOTE_ADDR']."|`ip`|\n");
					fwrite($handle, "|~message~|".htmlspecialchars($_POST['message'])."|`message`|\n");
					fclose($handle);
				}else{
					echo (showmessage('Сообщение должно быть не более 500 символов.'));
				}
			}else{
				echo (showmessage("Каптча введена не верно.<br>Вы ввели ".$_POST['keystring']));
			}
		}else{
			echo (showmessage('Вы не указали текст сообщения'));
		}
	}else{
		echo (showmessage('Вы не указали e-mail'));
	}
}
}
?>
<form action="?" method="post">
	<div class="dialog-round"><span class="d1"></span><span class="d2"></span><span class="d3"></span><div>
	<b>Ваш IP: </b><?echo($_SERVER['REMOTE_ADDR']);?><br>
	<b>Оставить комментарий:</b>
	<hr>
	<div class="fiftyright">
		<br>
		<table>
		<tr>
			<td>Имя:</td>
			<td><input name="author"></td>
		</tr>
		<tr>
			<td><a href="" class="hint" title="Скрыто от пользователей, поле обязательно к заполнению.">Ваш e-mail</a><span class="red">*</span>:</td>
			<td><input name="email"></td>
		</tr>
		<tr>
			<td>Ваш сайт:</td>
			<td><input name="website"></td>
		</tr>
		</table>
	</div>
	<div class="fiftyright">
		<a href="" class="hint" title="Поле обязательно к заполнению.">Текст сообщения<span class="red">*</span></a>:<br>
		<textarea rows="5" cols="90" name="message"></textarea>
	</div>
	<div class="fiftyright">
		<a href="" class="hint" title="Поле обязательно к заполнению.">Каптча<span class="red">*</span></a>:<br>
		<img src="kcaptcha/?<?php echo session_name()?>=<?php echo session_id()?>" style="border: 1px solid lime !important;" alt="Каптча! Не более 2ух кб."><br>
		<input name="keystring" style="width: 156px !important; margin-top: -1px;">
	</div>
	<input type="submit" value="Отправить" style="width: 100%;" name="submitrecord">
	</div><span class="d3"></span><span class="d2"></span><span class="d1"></span></div><br>
</form>
<?php
	foreach (glob("./records/*.rec") as $filename) {
		if (!empty($filename) && $filename!="./records/empty.rec") {
			echo ("<div class=\"dialog-round\">\n<span class=\"d1\"></span><span class=\"d2\"></span><span class=\"d3\"></span>\n<div>");
			$tmpstr=file_get_contents($filename);
			$author=$tmpstr;
			$author=substr_replace($author, '', 0, strpos($author, '|~author~|')+10);
			$author=substr_replace($author, '', strpos($author, '|`author`|'));
			$date=$tmpstr;
			$date=substr_replace($date, '', 0, strpos($date, '|~date~|')+8);
			$date=substr_replace($date, '', strpos($date, '|`date`|'));
			$time=$tmpstr;
			$time=substr_replace($time, '', 0, strpos($time, '|~time~|')+8);
			$time=substr_replace($time, '', strpos($time, '|`time`|'));
			$website=$tmpstr;
			$website=substr_replace($website, '', 0, strpos($website, '|~website~|')+11);
			$website=substr_replace($website, '', strpos($website, '|`website`|'));
			$ip=$tmpstr;
			$ip=substr_replace($ip, '', 0, strpos($ip, '|~ip~|')+6);
			$ip=substr_replace($ip, '', strpos($ip, '|`ip`|'));
			$message=$tmpstr;
			$message=substr_replace($message, '', 0, strpos($message, '|~message~|')+11);
			$message=substr_replace($message, '', strpos($message, '|`message`|'));
			$number=$filename;
			$number=substr_replace($number, '', 0, 10);
			$number=substr_replace($number, '', strpos($number, '.'));
			if ($_SERVER['REMOTE_ADDR']==$ip or $_SERVER['REMOTE_ADDR']==$GLOBALS['admin_ip'])
				echo ("<p class=\"line\" style=\"margin: 0;\"><a href=\"?edit=$number\">Редактировать</a> | <a href=\"?delete=$number\">Удалить</a></p>");
			if (empty($website)) {
				echo ("<p class=\"line\"><b>Автор:</b> $author, $date <b>в</b> $time</p>");
			}else{
				echo ("<p class=\"line\"><b>Автор:</b> <a href=\"$website\">$author</a>, $date <b>в</b> $time</p>");
			}
			echo ("<p class=\"line2\"><b>Сообщение:</b>\n<br>\n$message");
			echo ("\n<hr>ID сообщения: $number");
			echo ("</div>\n<span class=\"d3\"></span><span class=\"d2\"></span><span class=\"d1\"></span>\n</div><br>");
		} else {
			echo ("<div class=\"dialog-round\">\n<span class=\"d1\"></span><span class=\"d2\"></span><span class=\"d3\"></span>\n<div>");
			echo ("Увы, но тут нет записей.");
			echo ("</div>\n<span class=\"d3\"></span><span class=\"d2\"></span><span class=\"d1\"></span>\n</div><br>");
		}
	}
?>
<?php
	echo (getfooter());
?>
</body>
</html>