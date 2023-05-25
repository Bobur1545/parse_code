<?php
define('SITE_URL', 'https://kun.uz');
include_once 'db.php';
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

//bu saytni addressini topib olish uchun
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

//yuqoridagi topib olingan saytlardan faqat bittasini tutib olish uchun
foreach ($posts as $post) {
    $birinchi_sayt[] = SITE_URL . $post['link'];
//    $link1 = $birinchi_sayt[2];
//    break;
}

foreach ($birinchi_sayt as $link1) {
    $page_into_site = curlGetPage($link1);
    $html_into_site = str_get_html($page_into_site);

    $posts_into_site = [];
    foreach ($html_into_site->find('.single-layout__center') as $element_site) {
        $date = $element_site->find('.date', 0);
        $title = $element_site->find('.single-header__title', 0);
        $img = $element_site->find('.main-img img', 0);
        $text = $element_site->find('.single-content', 0);

        $data = [
            'date' => ($date) ? $date->plaintext : '',
            'title' => ($title) ? $title->plaintext : '',
            'img' => ($img) ? $img->src : '',
            'text' => ($text) ? $text->plaintext : '',
        ];

        if (!$data['date'])
            continue;

        $posts_into_site[] = $data;
    }

    // Ma'lumotlarni saqlash yoki ko'rsatish kabi $posts_into_site bilan biror narsa qilish

    // Shunnab chiqarsa boladi: Joriy $link1 uchun ma'lumotlarni chop etish
    echo "Data for $link1:<br>";
    foreach ($posts_into_site as $post) {
        echo "Date: {$post['date']}<br>";
        echo "Title: {$post['title']}<br>";
        echo "Image: {$post['img']}<br>";
        echo "Text: {$post['text']}<br><br>";
    }
    echo "<hr>";
}


//
////o'sha tutib olingan saytdagi malumotlarni ekranga chiqarish uchun
//    $page_into_site = curlGetPage($link1);
//    $html_into_site = str_get_html($page_into_site);
//
//    $posts_into_site = [];
//    foreach ($html_into_site->find('.single-layout__center') AS $element_site){
//        $date = $element_site->find('.date', 0);
//        $title = $element_site->find('.single-header__title', 0);
//        $img = $element_site->find('.main-img img', 0);
//        $text = $element_site->find('.single-content', 0);
//
//        $data = [
//            'date' => $date->plaintext,
//            'title' => $title->plaintext,
//            'img' => $img->src,
//            'text' => $text->plaintext,
//        ];
//
//        if (!$data['date'])
//            continue;
//
//        $posts_into_site[] = $data;
//    }

//echo '<pre>';
//print_r($posts_into_site);
//echo '</pre>';

