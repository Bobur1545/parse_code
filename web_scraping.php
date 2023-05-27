<?php
$url = "https://storage.kun.uz/source/9/miT4pl6HNjjZstNxByV-P8Iny93osZXw.jpg"; // Rasm URL manzili
$savePath = __DIR__ . "/images/image.jpg"; // Saqlash uchun papka va fayl nomi

// Rasmni yuklab olish
$imageData = file_get_contents($url);

// Rasmni saqlash
file_put_contents($savePath, $imageData);

echo "Rasm muvaffaqiyatli yuklandi va saqlandi!";

