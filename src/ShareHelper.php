<?php

namespace App;

class ShareHelper
{
    /**
     * Generiert die Teilen-Links für verschiedene Plattformen.
     */
    public static function getLinks(string $text, string $author, string $currentUrl): array
    {
        $fullQuote = '"' . $text . '"';
        if (!empty($author)) {
            $fullQuote .= ' – ' . $author;
        }
        
        $shareText = $fullQuote . ' | ' . $currentUrl;
        $encodedText = urlencode($shareText);
        $encodedUrl = urlencode($currentUrl);

        return [
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $encodedUrl,
            'twitter'  => 'https://twitter.com/intent/tweet?text=' . urlencode($fullQuote) . '&url=' . $encodedUrl,
            'threads'  => 'https://www.threads.net/intent/post?text=' . $encodedText,
            'mastodon' => 'https://mastoshare.s3cr.net/?text=' . $encodedText,
            'bluesky'  => 'https://bsky.app/intent/compose?text=' . $encodedText,
            'whatsapp' => 'https://api.whatsapp.com/send?text=' . $encodedText
        ];
    }
}