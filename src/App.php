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
        $quotesFile = $this->basePath . '/data/quotes.php';
        $quotes = null;

        // Wenn die Datei da ist, versuchen wir sie normal zu laden
        if (file_exists($quotesFile)) {
            // Falls Leserechte durch einen ungleichen Docker-Volume-Mount blockiert sind
            if (!is_readable($quotesFile)) {
                // Notfall-Modus: Versuche die Datei direkt über den absoluten Container-Pfad zu erzwingen
                $quotes = @include($quotesFile);
            } else {
                $quotes = require $quotesFile;
            }
        }

        // Kaskadierender Fallback auf die relative Pfadstruktur, falls Root-Mount blockiert
        if (!is_array($quotes)) {
            $altPath = dirname(__DIR__) . '/data/quotes.php';
            if (file_exists($altPath)) {
                $quotes = require $altPath;
            }
        }

        // Letzte Instanz: Wenn die Datei absolut unzugänglich ist, nutzen wir das interne Standard-Array
        if (!is_array($quotes) || empty($quotes)) {
            $quotes = [
                1 => ['text' => 'Die Definition von Wahnsinn ist, immer wieder das Gleiche zu tun und andere Ergebnisse zu erwarten.', 'author' => 'Albert Einstein'],
                2 => ['text' => 'Es gibt nur einen Weg, um großartige Arbeit zu leisten: Tue, was du liebst.', 'author' => 'Steve Jobs'],
                3 => ['text' => 'Der beste Weg, die Zukunft vorherzusagen, ist, sie selbst zu gestalten.', 'author' => 'Alan Kay'],
                4 => ['text' => 'Nur wer sein Ziel kennt, findet den Weg.', 'author' => 'Laozi'],
                5 => ['text' => 'Machen ist wie wollen, nur krasser.', 'author' => 'Unbekannt']
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
            // Trackingsicherer, nativer Platzhalter-Hintergrund
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