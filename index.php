<?php
define('SITE_URL', 'https://kun.uz');
//include_once 'db.php';
include_once 'simple_html_dom.php';

//print_r($_SERVER);
function curlGetPage($url, $referer = 'https://google.com/')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, value: 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
$page = curlGetPage(SITE_URL.'/uz/news/list');
$html = str_get_html($page);

$posts = [];
foreach ($html->find('#news-list a') AS $element){
    $link = $element;
    $p = $element->find('.news-title', 0);
    $time = $element->find('.news-date', 0);
    $posts[] = [
      'link' => $link->href,
        'title' => $p->plaintext,
        'time' => $time->plaintext,
    ];
}


echo '<pre>';
print_r($posts);
echo '</pre>';

