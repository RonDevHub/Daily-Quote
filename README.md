# Daily Quote – Der datenschutzkonforme Spruch des Tages

Ein extrem ressourcensparendes, autarkes PHP-Projekt, das jeden Tag vollautomatisch einen anderen motivierenden Spruch aus einer lokalen Datei anzeigt. Das Hintergrundbild rotiert ebenfalls täglich deterministisch mit.

## Features
- **100% Datenschutzkonform:** Keine Cookies, keine Sessions, kein LocalStorage.
- **Zero-Log-Policy:** Der Docker-Container loggt keine IP-Adressen oder Seitenzugriffe (`access.log` deaktiviert).
- **Modernes Teilen:** Statische HTML-Sharing-Links für Mastodon, Bluesky, Threads, WhatsApp, X (Twitter) und Facebook – ohne JavaScript-Tracker der Plattformen.
- **Garantiert lesbar:** Modernes Tailwind-CSS-Layout mit einem dynamischen Weichzeichner und Abdunklung des Hintergrundbildes (`backdrop-blur-md bg-black/50`).
- **Hybrid-Fähig:** Läuft ohne Anpassung direkt im Docker-Container oder auf klassischem Shared Webhosting per Apache.

## Projektstruktur
- `data/quotes.php`: Die zentrale Datei für deine Sprüche (PHP-Array-Format, extrem schnell).
- `public/assets/bg/`: Der Ordner für deine eigenen Hintergrundbilder (JPG, PNG, WEBP, AVIF werden automatisch erkannt).
- `src/`: Die gekapselte Kernlogik des Routers und der Share-Links.

## Installation & Betrieb

### 1. Klassischer Webhoster / FTP
1. Kopiere den gesamten Inhalt des Repositories auf deinen Webspace.
2. Konfiguriere deine Domain so, dass sie direkt auf das Verzeichnis `/public` verweist.
3. Lade deine Hintergrundbilder in `/public/assets/bg/` hoch.

### 2. Docker & Docker Compose
Starte die Applikation lokal oder auf deinem Server:
```bash
services:
  daily-quote:
    image: ghcr.io/rondevhub/daily-quote:latest
    container_name: daily_quote_app
    ports:
      - "8080:80"
    environment:
      - APP_OWNER_NAME=[Dein Name / Betreibername]
      - APP_OWNER_STREET=[Deine Straße und Hausnummer]
      - APP_OWNER_CITY=[Deine PLZ und Ort]
      - APP_OWNER_EMAIL=[Deine E-Mail-Adresse]
    restart: unless-stopped
```

## Lizenz

Freie Software – Open Source.