<?php
/**
 * Single Entry Point
 * Datenschutzkonform - Keine Cookies, Keine Sessions, Keine Logs.
 */

declare(strict_types=1);

$nonce = base64_encode(random_bytes(16));

// CSP erweitert, um das datenschutzkonforme Fallback-Bild zu erlauben, falls lokal keins geladen werden kann
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' https://cdn.jsdelivr.net 'nonce-$nonce'; img-src 'self' data: https://images.unsplash.com/; font-src 'self';");

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
                    – <?php echo htmlspecialchars($data['quote']['author'], ENT_QUOTES, 'UTF-8'); ?>
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

    <footer class="relative z-10 w-full p-6 text-center text-xs opacity-60 flex flex-wrap justify-center gap-6">
        <button id="btn-impressum" class="hover:underline cursor-pointer">Impressum</button>
        <button id="btn-datenschutz" class="hover:underline cursor-pointer">Datenschutz</button>
        <button id="btn-donate" class="hover:underline cursor-pointer">Spenden</button>
    </footer >

    <div id="modal-impressum" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md">
        <div class="bg-zinc-900 border border-zinc-800 text-zinc-100 max-w-lg w-full rounded-2xl p-6 shadow-2xl relative">
            <h2 class="text-xl font-bold mb-4 border-b border-zinc-800 pb-2">Impressum</h2>
            <div class="space-y-2 text-sm overflow-y-auto max-h-[60vh]">
                <p class="font-bold">Angaben gemäß § 5 TMG:</p>
                <p>[Dein Name / Betreibername]<br>[Deine Straße und Hausnummer]<br>[Deine PLZ und Ort]</p>
                <p class="font-bold mt-4">Kontakt:</p>
                <p>E-Mail: [Deine E-Mail-Adresse]</p>
            </div>
            <button id="close-impressum" class="mt-6 w-full py-2 bg-white/10 hover:bg-white/20 rounded-xl font-medium transition-colors cursor-pointer">Schließen</button>
        </div>
    </div>

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
                'btn-impressum': { modal: 'modal-impressum', close: 'close-impressum' },
                'btn-datenschutz': { modal: 'modal-datenschutz', close: 'close-datenschutz' },
                'btn-donate': { modal: 'modal-donate', close: 'close-donate' }
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