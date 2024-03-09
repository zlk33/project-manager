
# Panel do zarządzania projektami i zadaniami

Projekt przygotowany na zaliczenie przedmiotu na studiach. Aplikacja pozwala na rejestracje/logowanie użytkowników, tworzenie projektów i zadań.

# Wykorzystane technologie

![PHP](https://www.vectorlogo.zone/logos/php/php-horizontal.svg)
![MYSQL](https://www.vectorlogo.zone/logos/mysql/mysql-horizontal.svg)
![JAVASCRIPT](https://www.vectorlogo.zone/logos/javascript/javascript-horizontal.svg)
![JQUERY](https://www.vectorlogo.zone/logos/jquery/jquery-horizontal.svg)

# Możliwości
+ Rejestracja/logowanie
+ Edycja profilu
+ Tworzenie projektów
    * Zarządzanie projektem
        * Dodawanie zadań
            * Przypisywanie zadań członkom projektu
        * Zarządzanie uczestnikami
        * Monitorowanie zdarzeń
        * Dodawanie plików
    * Chat projektu

# Wygląd
![logowanie](./screenshots/logowanie.png)
![rejestracja](./screenshots/rejestracja.png)
![strona glowna](./screenshots/strona_glowna.png)
![profil](./screenshots/profil.png)
![lista projektow](./screenshots/lista_projektow.png)
![widok projektu](./screenshots/widok_projektu.png)
![widok projektu z chatem](./screenshots/widok_projektu_chat.png)
![edycja projektu](./screenshots/edycja_projektu.png)
# Uruchamianie

W plikach projektu znajduje się plik **management_panel.sql** który należy zaimportować do bazy MYSQL. Następnie w pliku konfiguracyjnym **db_config.php** ustawiamy połączenie z bazą danych.
```
<?php
    $db_ip = "localhost"; //Adres serwera
    $db_login = "root"; //Login do bazy danych
    $db_pass = ""; //Hasło 
    $db_name = "management_panel"; //Nazwa bazy danych
?>
```