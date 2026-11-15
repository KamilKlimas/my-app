# DuelLab

Aplikacja webowa dla graczy Counter-Strike 2. Użytkownik wgrywa demo meczu (`.dem`), aplikacja znajduje przegrane
pojedynki, diagnozuje ich przyczynę i generuje scenariusze treningowe eksportowane jako plik konfiguracyjny CS2 (`.cfg`).

Projekt semestralny z przedmiotu „Wstęp do aplikacji internetowych” (Politechnika Krakowska), rozwijany laboratorium
po laboratorium, bez frameworków PHP i CSS.

## Stan projektu

Po etapie M1 (fundamenty) strony renderują **dane przykładowe** zdefiniowane w kontrolerach. Baza ma już schemat
(`users`, `maps`) i dane demo. Logowanie działa na danych z bazy (sesja z regenerowanym id, CSRF), ale strony jeszcze
nie czytają zalogowanego użytkownika: nagłówek pokazuje przykładowego gracza, a trasy nie są chronione.

**Motyw jasny i ciemny:** przycisk w nagłówku przełącza `data-theme` na `<html>`, a wybór zapisuje się w `localStorage`
(klucz `theme`). Bez zapisanego wyboru obowiązuje ustawienie systemu (`prefers-color-scheme`). Powrót do ustawienia
systemowego: w konsoli `localStorage.removeItem('theme'); location.reload();`. Pozostałe formularze (upload demo, panel admina) wysyłają żądania
do tras, które jeszcze nie istnieją, więc kończą się odpowiedzią 404. Każdy POST bez poprawnego tokenu CSRF (ukryte pole `_csrf`) dostaje 403.

## Funkcje

| Trasa              | Kontroler i akcja              | Co pokazuje                                    |
|--------------------|--------------------------------|------------------------------------------------|
| `/`                | `DemoController::upload`       | strona startowa: wgrywanie dema, jak działa DuelLab, wykrywane błędy |
| `/login`           | `SecurityController::login`    | formularz logowania (po polsku)                |
| `POST /login`      | `SecurityController::authenticate` | logowanie: sukces → 303 na `/demos`, złe dane → 422 z jednym ogólnym komunikatem |
| `POST /logout`     | `SecurityController::logout`   | wylogowanie: niszczy sesję i ciasteczko, 303 na `/login` |
| `/demos`           | `DemoController::index`        | moje dema: statystyki, szybki upload, tabela z filtrem `?map=` i `?outcome=` |
| `/demos/{id}`      | `DemoController::show`         | szczegóły dema; nieznane id → 404              |
| `/hotspots`        | `HotspotController::index`     | miejsca na mapie, w których gracz przegrywa    |
| `/training`        | `TrainingController::index`    | zestawy treningowe                             |
| `/admin`           | `AdminController::index`       | panel administratora                           |
| każda inna         | `DefaultController::error`     | strona 404                                     |

`{id}` dopasowuje tylko cyfry, więc np. `/demos/abc` nie pasuje do żadnej trasy i zwraca 404.
Parametr query podany jako tablica (np. `/demos?map[]=x`) kończy się stroną 400. Każdy błąd (400, 403, 404, 503) to wyjątek `HttpException`, który `public/index.php`
łapie w jednym miejscu i renderuje przez `DefaultController::error`.

Filtr na `/demos` to zwykły formularz GET: `map` przyjmuje `all` albo mapę obecną na liście dem, `outcome` przyjmuje
`all`, `win` lub `loss`. Każda inna wartość wraca do `all`. Dema bez wyniku (w trakcie parsowania lub z błędem)
nie są ani wygraną, ani przegraną.

## Tech stack

| Warstwa         | Technologie                                          |
|-----------------|------------------------------------------------------|
| Backend         | PHP 8.3 (PHP-FPM), własny router i kontrolery         |
| Frontend        | HTML, czysty CSS                                     |
| Serwer HTTP     | nginx                                                |
| Baza danych     | PostgreSQL 17 (PDO; aplikacja łączy się, ale jeszcze nie czyta danych) |
| Narzędzia       | Composer (autoloading PSR-4), PHPUnit 12, Mailpit, pgAdmin |
| Konteneryzacja  | Docker, Docker Compose                               |

## Szybki start

1. Utwórz plik `.env` (nie jest commitowany) z przykładowej konfiguracji:

   ```bash
   cp .env.example .env
   ```

   Wartości z przykładu wystarczą do pracy lokalnej. Zmienne:

   | Zmienna                    | Wymagana | Domyślnie  |
   |----------------------------|----------|------------|
   | `POSTGRES_DB`              | tak      | —          |
   | `POSTGRES_USER`            | tak      | —          |
   | `POSTGRES_PASSWORD`        | tak      | —          |
   | `PGADMIN_DEFAULT_EMAIL`    | tak      | —          |
   | `PGADMIN_DEFAULT_PASSWORD` | tak      | —          |
   | `APP_PORT`                 | nie      | `8080`     |
   | `POSTGRES_PORT`            | nie      | `5433`     |
   | `MAILPIT_PORT`             | nie      | `8025`     |
   | `PGADMIN_PORT`             | nie      | `5050`     |
   | `PGADMIN_CONTAINER_NAME`   | nie      | `pg-admin` |
   | `SESSION_SECURE`           | nie      | `0`        |

   `SESSION_SECURE=1` dodaje do ciasteczka sesji flagę `Secure` (tylko HTTPS); w produkcji wymagane, lokalnie `0`.

2. Uruchom kontenery:

   ```bash
   docker compose up --build -d
   ```

   Przy pierwszym starcie kontener `php` sam wykona `composer install`, jeśli brakuje katalogu `vendor/`.
   Kontener `php` startuje dopiero, gdy baza przejdzie healthcheck (`pg_isready`). Dostaje z `.env` tylko dane bazy
   (`POSTGRES_DB`, `POSTGRES_USER`, `POSTGRES_PASSWORD` jako `DB_NAME`, `DB_USER`, `DB_PASSWORD`) i `SESSION_SECURE`. Gdy baza nie działa,
   każda strona zwraca 503, a przyczyna trafia do `docker compose logs php`.

| Usługa      | Adres                     |
|-------------|---------------------------|
| Aplikacja   | http://localhost:8080     |
| Mailpit     | http://localhost:8025     |
| pgAdmin     | http://localhost:5050     |
| PostgreSQL  | `localhost:5433`          |

Dane bazy leżą w nazwanym wolumenie `pg-data`, więc przeżywają `docker compose down` i ponowne `up`. Schemat tworzą pliki
`docker/db/NNN_*.sql`, wykonywane w kolejności nazw, ale tylko na pustym wolumenie. Po zmianie schematu trzeba więc zacząć
od pustej bazy: `docker compose down -v` (**kasuje wszystkie dane**), a potem `docker compose up -d`.

Po migracjach wykonuje się seed `docker/db/900_seed_demo_data.sql`. Numer 900 sprawia, że idzie po każdej migracji.
Seed wstawia mapy `de_ancient`, `de_anubis`, `de_dust2`, `de_inferno`, `de_mirage` i `de_nuke` z kalibracją radaru
oraz dwa konta demo, którymi można się zalogować na `/login`:

| E-mail                 | Hasło      | Rola  |
|------------------------|------------|-------|
| `nightowl@example.com` | `demo1234` | USER  |
| `admin@example.com`    | `demo1234` | ADMIN |

Hasło jest publiczne, więc służy tylko do demo. W bazie leży wyłącznie jego hash.

## Przydatne polecenia

```bash
docker compose ps                                   # stan kontenerów
docker compose logs -f php server                   # logi
docker compose exec -T php php -l src/Routing.php   # sprawdzenie składni pliku PHP
docker compose exec -T php composer dump-autoload   # po zmianie autoloadingu
curl -i http://localhost:8080/demos/1               # sprawdzenie trasy i kodu odpowiedzi
```

## Testy

Testy jednostkowe (PHPUnit 12) leżą w `tests/Unit/`, a konfiguracja w `phpunit.xml`. Uruchamia się je w kontenerze `php`:

```bash
docker compose exec -T php vendor/bin/phpunit
```

Test kończy się porażką także wtedy, gdy kod wywoła warning, notice albo deprecation PHP.

## Zasady developmentu

- Nazwy techniczne w kodzie po angielsku; tekst interfejsu na razie zapisany na sztywno.
- Każda wartość dynamiczna w szablonie przechodzi przez `$e(...)` (`htmlspecialchars`).
- Sekrety tylko w `.env`, którego nie commitujemy.
- Jeden commit = jedna logiczna zmiana, np. `feat(css): add hover and focus-visible states`.
- Praca bezpośrednio na `main`; każdy commit zaczyna się od typu i zakresu (`feat`, `fix`, `chore`, `docs`, ...).

Pełne konwencje i zasady dla agentów AI: [AGENTS.md](AGENTS.md).

## Struktura projektu

```text
.
├── docker/
│   ├── db/                 # obraz PostgreSQL, skrypty init (.sql)
│   ├── nginx/              # obraz i konfiguracja nginx
│   └── php/                # obraz PHP-FPM, entrypoint (composer install)
├── docs/                   # opis produktu, architektura, roadmapa, kryteria oceny
├── public/                 # document root nginx
│   ├── css/                # variables → base → layout → components → demos → upload → states → responsive
│   ├── js/                 # moduły przeglądarki (theme.js)
│   ├── index.php           # front controller, rejestracja tras
│   └── views/              # szablony stron + partials/ (header, footer, upload-form, csrf-field)
├── src/
│   ├── controllers/        # App\Controller\*: AppController i kontrolery obszarów
│   ├── Http/               # Query (odczyt $_GET), Session (start sesji, odczyt/zapis), Csrf (token, sprawdzanie POST), wyjątki HTTP (400/403/404/503), Redirect (303)
│   ├── Database/           # DatabaseConfig (zmienne DB_*), ConnectionFactory (PDO)
│   ├── Model/              # User, Role: obiekty danych bez SQL
│   ├── Repository/         # UserRepository (interfejs), PdoUserRepository: jedyne miejsce z SQL
│   ├── Service/            # AuthService: sprawdzenie e-maila i hasła
│   └── Routing.php         # router GET/POST z parametrami {param}, kontrolery z klasy albo fabryki
├── tests/Unit/             # testy PHPUnit (router, Query, Session, Csrf, wyjątki HTTP, Redirect, DatabaseConfig, AuthService)
├── composer.json           # autoloading PSR-4, PHPUnit jako zależność dev
├── docker-compose.yaml     # usługi server, php, db, mailpit, pgadmin-wdpai
├── phpunit.xml             # konfiguracja testów
└── README.md
```

## Autorzy

- Kamil Klimas
