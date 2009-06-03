<?

if (extension_loaded('gettext')) {
	if (isset($_COOKIE['lang'])) {
		setlocale(LC_MESSAGES,  $_COOKIE['lang']);
//    setcookie("lang",  $_COOKIE['lang'], time()+2592000);
  } else {
			setlocale(LC_MESSAGES,  'en_US');
	}
	bindtextdomain('main','../i18n');
  textdomain('main');
}

function uptime () {
    global $text;
    $fd = fopen('/proc/uptime', 'r');
    $ar_buf = split(' ', fgets($fd, 4096));
    fclose($fd);

    $sys_ticks = trim($ar_buf[0]);

    $min = $sys_ticks / 60;
    $hours = $min / 60;
    $days = floor($hours / 24);
    $hours = floor($hours - ($days * 24));
    $min = floor($min - ($days * 60 * 24) - ($hours * 60));

    if ($days != 0) {
      $result = "$days days, ";
    }

    if ($hours < 2) {
      $result .= "$hours hour, ";
    } else {
      $result .= "$hours hours, ";
}
    if ($min < 2) {
    $result .= "$min minute";
    } else {
      $result .= "$min minutes";
}
    return $result;
  }

function getip() {
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
   $ip = getenv("HTTP_CLIENT_IP");

   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
   $ip = getenv("HTTP_X_FORWARDED_FOR");

   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
   $ip = getenv("REMOTE_ADDR");

   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
   $ip = $_SERVER['REMOTE_ADDR'];

   else
   $ip = "unknown";

   return($ip);
}

function file_parser($filename) {
        $file = file($filename);
        foreach ($file as $line) {
                if (preg_match("/^\s*([a-zA-Z0-9]+)\s*=\s*(.*)\s*([;#].*)?/",$line,$matches)) {
                        $conf[ $matches[1] ] = $matches[2];
                }
        }
        return $conf;
}

function file_parser_asterisk($filename) {
		$file = file($filename);
		foreach ($file as $line) {
			if (preg_match("/^\s*([a-zA-Z0-9]+)\s*\s*(.*)\s*([;#].*)?/",$line,$matches)) {
				$conf[ $matches[1] ] = $matches[2];
			}
		}
		return $conf;
}

function file_parser_sshd_conf($filename) {
		$file = file($filename);
		foreach ($file as $line) {
			if (preg_match("/^\s*([a-zA-Z0-9]+)\s* \s*(.*)\s*([;#].*)?/",$line,$matches)) {
				$conf[ $matches[1] ] = $matches[2];
			}
		}
		return $conf;
}

function file_parser_serial($filename) {
        $file = file($filename);
        $conf = $file[0];
        return $conf;
}

function returnMacAddress() {

$location = `which ifconfig`;
$location = rtrim($location);
$arpTable = `$location | grep eth0`;
$arpSplitted = split("\n",$arpTable);
$remoteIp = "HWaddr";
$remoteIp = str_replace(".", "\\.", $remoteIp);
foreach ($arpSplitted as $value) {
$valueSplitted = split(" ",$value);
foreach ($valueSplitted as $spLine) {
if (preg_match("/$remoteIp/",$spLine)) {
$ipFound = true;
}
if ($ipFound) {
reset($valueSplitted);
foreach ($valueSplitted as $spLine) {
if (preg_match("/[0-9a-f][0-9a-f][:-]".
"[0-9a-f][0-9a-f][:-]".
"[0-9a-f][0-9a-f][:-]".
"[0-9a-f][0-9a-f][:-]".
"[0-9a-f][0-9a-f][:-]".
"[0-9a-f][0-9a-f]/i",$spLine)) {
return $spLine;
}
}
}
$ipFound = false;
}
}
return false;
}

$ver_voiper = file_parser("/etc/ver_voiper");
$ver_asterisk = file_parser_asterisk("/etc/ver_asterisk");
$ver_vcti = file_parser("/etc/ver_vcti");
$serial = file_parser_serial("/etc/voiper_serial");
$sshd_conf = file_parser_sshd_conf("/etc/ssh/sshd_config");
?>
