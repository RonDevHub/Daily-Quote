<?php

/**
 * Single Entry Point
 * Datenschutzkonform - Keine Cookies, Keine Sessions, Keine Logs.
 */

declare(strict_types=1);

$nonce = base64_encode(random_bytes(16));

// CSP erweitert um deine Badges: mail-shield.net und mini-badges.rondev.de
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' https://cdn.jsdelivr.net 'nonce-$nonce'; img-src 'self' data: https://images.unsplash.com/ https://mail-shield.net https://mini-badges.rondev.de; font-src 'self';");

$rootDir = dirname(__DIR__);

// Ressourcensparender, nativer .env-Parser für klassisches Shared Hosting (z.B. All-Inkl)
if (file_exists($rootDir . '/.env')) {
    $lines = file($rootDir . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Kommentare ignorieren
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!getenv($name)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
        }
    }
}

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

$app = new \App\App(dirname(__DIR__));
$data = $app->run();

// Auslesen der Docker-Umgebungsvariablen mit Fallbacks für das Impressum
$ownerName   = getenv('APP_OWNER_NAME')   ?: '[Dein Name / Betreibername]';
$ownerStreet = getenv('APP_OWNER_STREET') ?: '[Deine Straße und Hausnummer]';
$ownerCity   = getenv('APP_OWNER_CITY')   ?: '[Deine PLZ und Ort]';
$ownerEmail  = getenv('APP_OWNER_EMAIL')  ?: '[Deine E-Mail-Adresse]';
$showImpressumEnv = getenv('APP_LINK_IMPRESSUM')   ?: ($_ENV['APP_LINK_IMPRESSUM']   ?? 'true');

// Schalter für Impressum auswerten (Alles außer 'true' oder 1 wird als false gewertet)
$showImpressum = (filter_var($showImpressumEnv, FILTER_VALIDATE_BOOLEAN));

// Logik für die intelligente Erkennung der E-Mail / des Kontakt-Links
$emailHtml = '';
$trimmedEmail = trim($ownerEmail);

if (filter_var($trimmedEmail, FILTER_VALIDATE_URL)) {
    // Wenn es eine URL ist (z.B. MailShield), generieren wir einen sicheren Link
    $emailHtml = '<a href="' . htmlspecialchars($trimmedEmail, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer"><img src="https://mail-shield.net/badge" alt="Protected by MailShield"></a>';
} else {
    // Wenn es eine normale E-Mail ist, machen wir sie unkopierbar und drehen sie per CSS um (Spambot-Schutz)
    $reversedEmail = strrev($trimmedEmail);
    $emailHtml = 'E-Mail: <span class="inline-block select-none pointer-events-none" style="direction: rtl; unicode-bidi: bidi-override;">' . htmlspecialchars($reversedEmail, ENT_QUOTES, 'UTF-8') . '</span>';
}
?>
<!DOCTYPE html>
<html lang="de" class="min-h-dvh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spruch des Tages</title>
    <link rel="icon" type="image/png" href="assets/favicon/handshake.png">
    <meta name="description" content="Jeden Tag ein neuer Motivationsspruch oder ein inspirierendes Zitat. Lass dich täglich neu inspirieren!">
    <meta name="keywords" content="Motivation, Zitate, Inspiration, täglicher Spruch, Erfolg, Durchhaltevermögen">
    <meta name="author" content="Ronny Melzer - RonDev">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style nonce="<?php echo $nonce; ?>">
        body {
            background-image: url('<?php echo htmlspecialchars($data['bg_image'], ENT_QUOTES, 'UTF-8'); ?>');
        }
    </style>
</head>

<body class="min-h-dvh bg-cover bg-center bg-no-repeat bg-fixed flex flex-col justify-between text-white relative antialiased selection:bg-white/20">

    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm z-0"></div>

    <header class="relative z-10 w-full p-6 flex justify-between items-center">
        <h1 class="text-xl font-bold tracking-wider uppercase opacity-80">Daily Quote</h1>
    </header>

    <main class="relative z-10 max-w-4xl mx-auto px-6 text-center flex flex-col items-center justify-center my-auto">
        <div class="transition-all duration-500 ease-in-out transform scale-100">
            <p class="text-3xl md:text-5xl font-extrabold leading-tight tracking-tight drop-shadow-md">
                »<?php echo htmlspecialchars($data['quote']['text'], ENT_QUOTES, 'UTF-8'); ?>«
            </p>
            <?php if (!empty($data['quote']['author'])): ?>
                <p class="mt-4 text-lg md:text-xl font-medium tracking-wide opacity-75 italic drop-shadow">
                    &mdash; <?php echo htmlspecialchars($data['quote']['author'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="mt-12 flex flex-wrap justify-center gap-4">
            <a href="<?php echo $data['share_links']['mastodon']; ?>" target="_blank" rel="noopener noreferrer" class="px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 transition-all text-sm font-medium border border-white/20 backdrop-blur-md">Mastodon</a>
            <a href="<?php echo $data['share_links']['bluesky']; ?>" target="_blank" rel="noopener noreferrer" class="px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 transition-all text-sm font-medium border border-white/20 backdrop-blur-md">Bluesky</a>
            <a href="<?php echo $data['share_links']['whatsapp']; ?>" target="_blank" rel="noopener noreferrer" class="px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 transition-all text-sm font-medium border border-white/20 backdrop-blur-md">WhatsApp</a>
            <a href="<?php echo $data['share_links']['threads']; ?>" target="_blank" rel="noopener noreferrer" class="px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 transition-all text-sm font-medium border border-white/20 backdrop-blur-md">Threads</a>
            <a href="<?php echo $data['share_links']['twitter']; ?>" target="_blank" rel="noopener noreferrer" class="px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 transition-all text-sm font-medium border border-white/20 backdrop-blur-md">X (Twitter)</a>
            <a href="<?php echo $data['share_links']['facebook']; ?>" target="_blank" rel="noopener noreferrer" class="px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 transition-all text-sm font-medium border border-white/20 backdrop-blur-md">Facebook</a>
        </div>
    </main>

    <footer class="relative z-10 w-full p-6 text-center text-xs opacity-60 flex flex-wrap justify-center gap-x-6 gap-y-3">
        <div class="w-full flex justify-center gap-6">
            <?php if ($showImpressum): ?>
                <button id="btn-impressum" class="hover:underline cursor-pointer">Impressum</button>
            <?php endif; ?>
            <button id="btn-datenschutz" class="hover:underline cursor-pointer">Datenschutz</button>
            <button id="btn-donate" class="hover:underline cursor-pointer">Spenden</button>
        </div>
        <div class="w-full text-center mt-2">
            <span class="text-emerald-400 font-bold">Erstellt mit ❤️ und ☕️ von <a href="https://rondev.de" target="_blank" rel="noopener noreferrer" class="hover:underline">RonDev</a> - <a href="https://github.com/RonDevHub/Daily-Quote" target="_blank" rel="noopener noreferrer" class="hover:underline">Github</a></span>
        </div>
    </footer>

    <?php if ($showImpressum): ?>
        <div id="modal-impressum" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md">
            <div class="bg-zinc-900 border border-zinc-800 text-zinc-100 max-w-lg w-full rounded-2xl p-6 shadow-2xl relative">
                <h2 class="text-xl font-bold mb-4 border-b border-zinc-800 pb-2">Impressum</h2>
                <div class="space-y-2 text-sm overflow-y-auto max-h-[60vh]">
                    <p class="font-bold">Angaben gemäß § 5 TMG:</p>
                    <p>
                        <?php echo htmlspecialchars($ownerName, ENT_QUOTES, 'UTF-8'); ?><br>
                        <?php echo htmlspecialchars($ownerStreet, ENT_QUOTES, 'UTF-8'); ?><br>
                        <?php echo htmlspecialchars($ownerCity, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p class="font-bold mt-4">Kontakt:</p>
                    <p><?php echo $emailHtml; ?></p>
                </div>
                <button id="close-impressum" class="mt-6 w-full py-2 bg-white/10 hover:bg-white/20 rounded-xl font-medium transition-colors cursor-pointer">Schließen</button>
            </div>
        </div>
    <?php endif; ?>

    <div id="modal-datenschutz" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md">
        <div class="bg-zinc-900 border border-zinc-800 text-zinc-100 max-w-lg w-full rounded-2xl p-6 shadow-2xl relative">
            <h2 class="text-xl font-bold mb-4 border-b border-zinc-800 pb-2">Datenschutzerklärung</h2>
            <div class="space-y-4 text-sm overflow-y-auto max-h-[60vh] text-zinc-300">
                <div>
                    <h3 class="font-bold text-white">1. Datenschutz auf einen Blick</h3>
                    <p class="mt-1">Diese Applikation arbeitet nach dem Prinzip der absoluten Datensparsamkeit. Es werden keine personenbezogenen Daten erhoben, verarbeitet oder dauerhaft gespeichert.</p>
                </div>
                <div>
                    <h3 class="font-bold text-white">2. Cookies & Tracking</h3>
                    <p class="mt-1">Diese Seite verwendet keine Cookies, keine Sessions und führt keinerlei Tracking oder Nutzeranalysen durch.</p>
                </div>
                <div>
                    <h3 class="font-bold text-white">3. Server-Log-Files</h3>
                    <p class="mt-1">Der Server wurde so konfiguriert, dass standardmäßig keine Verbindungs-Logs (Access-Logs) geschrieben werden. IP-Adressen werden nicht aufgezeichnet.</p>
                </div>
                <div>
                    <h3 class="font-bold text-white">4. Externe Links / Teilen</h3>
                    <p class="mt-1">Die Social-Media-Links sind reine Textverknüpfungen. Daten an die jeweiligen Plattformen werden erst und nur dann übertragen, wenn Sie aktiv auf den entsprechenden Button klicken.</p>
                </div>
            </div>
            <button id="close-datenschutz" class="mt-6 w-full py-2 bg-white/10 hover:bg-white/20 rounded-xl font-medium transition-colors cursor-pointer">Schließen</button>
        </div>
    </div>

    <div id="modal-donate" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md">
        <div class="bg-zinc-900 border border-zinc-800 text-zinc-100 max-w-lg w-full rounded-2xl p-6 shadow-2xl relative">
            <h2 class="text-xl font-bold mb-4 border-b border-zinc-800 pb-2">Unterstützung / Spenden</h2>
            <div class="space-y-2 text-sm text-center py-4">
                <p>Wenn dir diese werbefreie und datenschutzfreundliche App gefällt, kannst du das Projekt hier unterstützen:</p>
                <p class="pt-4 font-bold text-lg text-emerald-400"><a href="https://rondev.de/donate" target="_blank" rel="nofollow">Donate</a></p>
            </div>
            <button id="close-donate" class="mt-6 w-full py-2 bg-white/10 hover:bg-white/20 rounded-xl font-medium transition-colors cursor-pointer">Schließen</button>
        </div>
    </div>

    <script nonce="<?php echo $nonce; ?>">
        document.addEventListener('DOMContentLoaded', () => {
            const modals = {
                <?php if ($showImpressum): ?> 'btn-impressum': {
                        modal: 'modal-impressum',
                        close: 'close-impressum'
                    },
                <?php endif; ?> 'btn-datenschutz': {
                    modal: 'modal-datenschutz',
                    close: 'close-datenschutz'
                },
                'btn-donate': {
                    modal: 'modal-donate',
                    close: 'close-donate'
                }
            };

            function toggle(id) {
                const el = document.getElementById(id);
                if (el) el.classList.toggle('hidden');
            }

            Object.keys(modals).forEach(btnId => {
                const trigger = document.getElementById(btnId);
                const closeBtn = document.getElementById(modals[btnId].close);
                const modalEl = document.getElementById(modals[btnId].modal);

                if (trigger) {
                    trigger.addEventListener('click', () => toggle(modals[btnId].modal));
                }
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => toggle(modals[btnId].modal));
                }
                if (modalEl) {
                    modalEl.addEventListener('click', (e) => {
                        if (e.target === modalEl) toggle(modals[btnId].modal);
                    });
                }
            });
        });
    </script>
</body>

</html>