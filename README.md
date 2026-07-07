# Daily Quote – Der datenschutzkonforme Spruch des Tages

<div align="center">

![Created](https://mini-badges.rondev.de/forgejo/RonDevHub/Daily-Quote/created-at/*/*/en) ![GitHub Repo stars](https://mini-badges.rondev.de/forgejo/RonDevHub/Daily-Quote/lastcommit/*/*/en) ![GitHub Repo stars](https://mini-badges.rondev.de/github/RonDevHub/Daily-Quote/stars/*/*/en) ![GitHub Repo stars](https://mini-badges.rondev.de/github/RonDevHub/Daily-Quote/issues/*/*/en) ![GitHub Repo language](https://mini-badges.rondev.de/forgejo/RonDevHub/Daily-Quote/language/*/*/en) ![GitHub Repo license](https://mini-badges.rondev.de/github/RonDevHub/Daily-Quote/license/*/*/en) ![GitHub Repo release](https://mini-badges.rondev.de/github/RonDevHub/Daily-Quote/release/*/*/en) ![GitHub Repo release](https://mini-badges.rondev.de/github/RonDevHub/Daily-Quote/forks/*/*/en) ![GitHub Repo downlods](https://mini-badges.rondev.de/github/RonDevHub/Daily-Quote/downloads/*/*/en) ![GitHub Repo stars](https://mini-badges.rondev.de/github/RonDevHub/Daily-Quote/watchers) [![status-badge](https://ci.commitcloud.net/api/badges/13/status.svg)](https://ci.commitcloud.net/repos/13) 

[![Buy me a coffee](https://mini-badges.rondev.de/icon/cuptogo/Buy_me_a_Coffee-c1d82f-222/for-the-badge "Buy me a coffee")](https://www.buymeacoffee.com/RonDev)
[![Buy me a coffee](https://mini-badges.rondev.de/icon/cuptogo/ko--fi.com-c1d82f-222/for-the-badge "Buy me a coffee")](https://ko-fi.com/U6U31EV2VS)
[![Pizza Power](https://mini-badges.rondev.de/icon/paypal/PayPal/for-the-badge "Pizza Power")](https://www.paypal.com/donate/?hosted_button_id=PWY939TPCQ3RA)
</div>
<hr>

Ein extrem ressourcensparendes, autarkes PHP-Projekt, das jeden Tag vollautomatisch einen anderen motivierenden Spruch ***(Aktuell 167)*** aus einer lokalen Datei anzeigt. Das Hintergrundbild rotiert ebenfalls täglich deterministisch mit.

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
      - APP_LINK_IMPRESSUM=true #Soll das Impressum auf der Seite erscheinen? true = ja, false = nein
      - APP_OWNER_NAME=[Dein Name / Betreibername]
      - APP_OWNER_STREET=[Deine Straße und Hausnummer]
      - APP_OWNER_CITY=[Deine PLZ und Ort]
      - APP_OWNER_EMAIL=[Deine E-Mail-Adresse]
    restart: unless-stopped
```

## Lizenz

Freie Software – Open Source.