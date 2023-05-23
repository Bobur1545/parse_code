<?php
// Include required libraries
require 'vendor/autoload.php';

// Database credentials
$dbHost = 'localhost';
$dbName = 'parser_site';
$dbUser = 'root';
$dbPassword = 'root';

try {
    // Connect to the database using PDO
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create a new Guzzle client
    $client = new GuzzleHttp\Client();

    // Target website URL
    $targetUrl = 'https://kun.uz/news/2023/05/23/uzum-ekotizimi-yil-brendi-2022-taqdirlash-marosimi-doirasida-eng-kop-sovrinlarga-ega-boldi';

    // Send an HTTP GET request to the target URL
    $response = $client->request('GET', $targetUrl);
    $statusCode = $response->getStatusCode();

    if ($statusCode === 200) {
        $html = $response->getBody();
        // Process the HTML content
    } else {
        echo "HTTP Request Error - Status Code: " . $statusCode;
    }
//    var_dump($response);

    // Create a new DOMDocument instance
    $dom = new DOMDocument();

    // Load the HTML content
    libxml_use_internal_errors(true); // Disable libxml errors/warnings
    $dom->loadHTML($html);
    libxml_clear_errors();

    // Find and parse the news articles
    $articles = $dom->getElementsByTagName('article');
    foreach ($articles as $article) {
        $imageElement = $article->getElementsByTagName('img')->item(0);
        $image = $imageElement ? $imageElement->getAttribute('src') : '';

        $titleElement = $article->getElementsByTagName('h2')->item(0);
        $title = $titleElement ? $titleElement->textContent : '';

        $textElement = $article->getElementsByTagName('p')->item(0);
        $text = $textElement ? $textElement->textContent : '';

        $dateElement = $article->getElementsByTagName('time')->item(0);
        $date = $dateElement ? $dateElement->getAttribute('datetime') : '';

        // Store the extracted information in the database
        $stmt = $db->prepare("INSERT INTO articles (image_url, title, text, date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$image, $title, $text, $date]);
    }
} catch (PDOException $e) {
    // Handle database connection errors
    echo "Database Error: " . $e->getMessage();
} catch (GuzzleHttp\Exception\GuzzleException $e) {
    // Handle HTTP request errors
    echo "HTTP Request Error: " . $e->getMessage();
}
