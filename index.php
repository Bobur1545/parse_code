<?php
define('SITE_URL', 'https://kun.uz');
include_once 'db.php';
include_once 'simple_html_dom.php';

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
    $posts[] = [ 'link' => $link->href ];
}

//yuqoridagi topib olingan saytlardan faqat bittasini tutib olish uchun
foreach ($posts as $post) {
    $birinchi_sayt[] = SITE_URL . $post['link'];
}

foreach ($birinchi_sayt as $link1) {
    $page_into_site = curlGetPage($link1);
    $html_into_site = str_get_html($page_into_site);

    $posts_into_site = [];
    foreach ($html_into_site->find('.single-layout__center') as $element_site) {
        $date = $element_site->find('.date', 0);
        $title = $element_site->find('.single-header__title', 0);
        $img = $element_site->find('.main-img img, .image img', 0);
        $text = $element_site->find('.single-content', 0);

        $data = [
            'date' => $date->plaintext,
            'title' => $title->plaintext,
            'img' => $img->src,
            'text' => $text->plaintext,
        ];

        if (!$data['date'])
            continue;

        $posts_into_site[] = $data;

    }

    // Shunnab chiqarsa boladi: Joriy $link1 uchun ma'lumotlarni chop etish
    echo "malumotlar shu linkdagi: $link1:<br>";
    foreach ($posts_into_site as $post) {

        //bu kod vaqtini togri boshqarish uchun
        $dateTimeParts = explode(" / ", $post['date']); // Qiymatni vaqt va sanaga bo'lib ajratib olamiz
        $timePart = trim($dateTimeParts[0]); // Vaqt qismi
        $datePart = trim($dateTimeParts[1]); // Sana qismi
        if (strpos($timePart, ":") !== false && $datePart) {
            // Vaqt va sana kelsa
            $datetime = date("Y-m-d H:i:s", strtotime("$datePart $timePart"));
        } else  {
            // Faqat vaqt kelsa
            $currentDate = date("Y-m-d"); // Joriy sana
            $datetime = date("Y-m-d H:i:s", strtotime("$currentDate $timePart"));
        }

        echo "Date: {$post['date']}<br>";
        echo "Title: {$post['title']}<br>";
        echo "Image: {$post['img']} <br>";
        echo "Text: {$post['text']}<br><br>";

//        bu kod rasmlarni toza nom barib images degan papkaga saqlagan holda ularni bazaga yozish uchun
        if (!empty($post['img'])) {
            $url = $post['img']; // Rasm URL manzili
            $imageName = "image_". $datetime . ".jpg"; // Fayl nomi generatsiyalash
            $savePath = __DIR__ . "/images/$imageName"; // Saqlash uchun papka va fayl nomi
            if (file_exists($savePath)) //agar shu papkada yuklab olingan nomli rasm bolsa uni qayta yuklamay olish uchun
            {
                unlink($savePath); // Faylni o'chirib ketish
            }
            $imageData = file_get_contents($url);
            file_put_contents($savePath, $imageData);

            $db->query("INSERT IGNORE INTO posts (`data`, `title`, `img`, `text`)
                VALUES ('$datetime', '{$post['title']}', '$imageName', '{$post['text']}' )");
        }

    }
    echo "<hr>";
}

