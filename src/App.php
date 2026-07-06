<?php

namespace App;

class App
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $realPath = realpath($basePath);
        $this->basePath = $realPath ? rtrim($realPath, '/') : rtrim($basePath, '/');
    }

    /**
     * Führt die Kernlogik aus und gibt die Daten für die View zurück.
     */
    public function run(): array
    {
        // Absoluter, fixer Pfad im Docker-Container
        $quotesFile = $this->basePath . '/data/quotes.php';
        $quotes = null;

        if (file_exists($quotesFile)) {
            $quotes = require $quotesFile;
        }

        // Sollte das Array aus irgendeinem Grund dennoch nicht ladbar sein, 
        // bricht die App nicht ab, sondern zeigt diesen Fehler-Spruch direkt im Design an.
        if (!is_array($quotes) || empty($quotes)) {
            $quotes = [
                1 => [
                    'text' => 'Fehler: Die data/quotes.php konnte nicht ausgelesen werden. Bitte Image neu bauen.', 
                    'author' => 'System'
                ]
            ];
        }

        // 2. Hintergrundbilder ermitteln
        $bgDir = $this->basePath . '/public/assets/bg';
        $images = [];
        
        if (is_dir($bgDir) && is_readable($bgDir)) {
            $files = scandir($bgDir);
            foreach ($files as $file) {
                if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'avif'])) {
                    $images[] = $file;
                }
            }
        }

        // Deterministische Auswahl basierend auf dem Tag des Jahres
        $dayOfYear = (int)date('z');
        $quoteValues = array_values($quotes);
        $quoteCount = count($quoteValues);
        $quoteIndex = $dayOfYear % $quoteCount;
        $selectedQuote = $quoteValues[$quoteIndex];

        if (!empty($images)) {
            $imageCount = count($images);
            $imageIndex = $dayOfYear % $imageCount;
            $selectedImage = '/assets/bg/' . $images[$imageIndex];
        } else {
            // Datenschutzkonformer Fallback via Unsplash (ohne Tracker)
            $selectedImage = 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1920&q=80';
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $currentUrl = $protocol . ($_SERVER['HTTP_HOST'] ?? 'localhost') . strtok($_SERVER['REQUEST_URI'] ?? '', '?');

        $shareLinks = ShareHelper::getLinks($selectedQuote['text'], $selectedQuote['author'], $currentUrl);

        return [
            'quote' => $selectedQuote,
            'bg_image' => $selectedImage,
            'share_links' => $shareLinks
        ];
    }
}