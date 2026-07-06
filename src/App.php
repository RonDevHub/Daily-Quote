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
        // Priorität 1: Standard-Pfad im Container
        $quotesFile = $this->basePath . '/data/quotes.php';
        $quotes = null;

        // Kaskadierende Pfad-Prüfung, falls Volumes den Standardpfad blockieren
        if (file_exists($quotesFile) && is_readable($quotesFile)) {
            $quotes = require $quotesFile;
        } else {
            // Priorität 2: Fallback parallel zu src (falls Verzeichnis verschoben)
            $fallbackPath1 = dirname(__DIR__) . '/data/quotes.php';
            // Priorität 3: Fallback innerhalb von src (absolut ausbruchssicher)
            $fallbackPath2 = __DIR__ . '/../data/quotes.php';

            if (file_exists($fallbackPath1) && is_readable($fallbackPath1)) {
                $quotes = require $fallbackPath1;
            } elseif (file_exists($fallbackPath2) && is_readable($fallbackPath2)) {
                $quotes = require $fallbackPath2;
            } else {
                // Wenn alles fehlschlägt, erzeugen wir ein lesbares In-Memory-Array mit klarer Fehlermeldung
                $quotes = [
                    1 => [
                        'text' => 'Rechte- oder Mountfehler! Die quotes.php ist im Container nicht lesbar. Bitte prüfe die lokalen Dateirechte auf dem Host-System.', 
                        'author' => 'System-Sicherheitsblockade'
                    ]
                ];
            }
        }

        if (!is_array($quotes) || empty($quotes)) {
            $quotes = [
                1 => ['text' => 'Keine Sprüche konfiguriert oder Format in quotes.php fehlerhaft.', 'author' => 'System']
            ];
        }

        // 2. Hintergrundbilder ermitteln
        $bgDir = $this->basePath . '/public/assets/bg';
        if (!is_dir($bgDir)) {
            $bgDir = dirname(__DIR__) . '/public/assets/bg';
        }

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
            // Datenschutzkonformer Unsplash-Fallback ohne Tracker
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