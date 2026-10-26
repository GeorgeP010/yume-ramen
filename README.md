# Yume Ramen - PHP Setup Instructies

## Vereisten
- PHP 7.4 of hoger
- MySQL/MariaDB database
- Apache webserver met mod_rewrite
- PHPMyAdmin (voor database beheer)

## Installatie Stappen

### 1. Database Setup
1. Open PHPMyAdmin (meestal op `http://localhost/phpmyadmin`)
2. Klik op "Import" in het bovenste menu
3. Selecteer het bestand `database_setup.sql`
4. Klik op "Go" om de database en tabellen aan te maken
5. Verifieer dat de database `yume` is aangemaakt met alle tabellen

### 2. Database Configuratie
1. Open het bestand `config/config.php`
2. Pas de database credentials aan indien nodig:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'yume');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Vul je database wachtwoord in
   ```

### 3. Webserver Setup
1. Zorg dat de `yume_ramen` folder in je webserver root staat (bijv. `htdocs` of `www`)
2. Navigeer naar `http://localhost/yume_ramen/` in je browser
3. De website zou nu moeten werken!

## Test Account
Voor het testen van de login functionaliteit:
- **Email:** test@test.nl
- **Wachtwoord:** test

Of maak een nieuw account aan via de registratie pagina.

## Project Structuur

```
yume_ramen/
├── config/              # Database configuratie
│   ├── config.php
│   └── Database.php
├── models/              # Database models
│   ├── Customer.php
│   ├── MenuItem.php
│   ├── Order.php
│   └── Category.php
├── controllers/         # Business logic
│   ├── HomeController.php
│   ├── MenuController.php
│   ├── AccountController.php
│   └── AuthController.php
├── views/               # PHP templates
│   ├── home/
│   ├── menu/
│   ├── account/
│   └── includes/
├── public/              # Static assets
│   ├── css/
│   ├── js/
│   ├── img/
│   └── video/
├── index.php            # Homepage entry point
├── menu.php             # Menu page entry point
├── account.php          # Account page entry point
├── auth.php             # Authentication handler
└── database_setup.sql   # Database schema
```

## Functionaliteiten

### ✅ Geïmplementeerd
- **Homepage** met featured items uit database
- **Menu pagina** met alle items uit database, gegroepeerd per categorie
- **Gebruikersregistratie** met wachtwoord hashing
- **Login systeem** met sessie management
- **Account pagina** met:
  - Gebruikersprofiel
  - Bestellingsgeschiedenis
  - Account statistieken
  - Loyalty punten
- **Database integratie** voor alle content
- **MVC architectuur** voor schone code organisatie

### 🎨 Design
- Alle originele styling is behouden
- Responsive design voor mobiel en desktop
- Animaties en effecten werken nog steeds
- Identiek aan de originele HTML versie

## Troubleshooting

### Database connectie fout
- Controleer of MySQL/MariaDB draait
- Verifieer database credentials in `config/config.php`
- Zorg dat de database `yume` bestaat

### 404 Errors
- Controleer of mod_rewrite enabled is in Apache
- Verifieer dat `.htaccess` bestand aanwezig is

### Styling werkt niet
- Controleer of de `public/` folder correct is aangemaakt
- Verifieer dat CSS/JS bestanden in `public/css/` en `public/js/` staan
- Check browser console voor errors

### Login werkt niet
- Controleer of sessies enabled zijn in PHP
- Verifieer dat de `customers` tabel bestaat in de database
- Check of JavaScript console errors toont

## Ontwikkeling

### Nieuwe menu items toevoegen
Gebruik PHPMyAdmin om items toe te voegen aan de `menu_items` tabel, of voer SQL uit:

```sql
INSERT INTO menu_items (category_id, name, description, price, is_featured) 
VALUES (1, 'Nieuwe Ramen', 'Beschrijving', 15.90, TRUE);
```

### Nieuwe gebruiker handmatig toevoegen
```sql
INSERT INTO customers (first_name, last_name, email, password_hash) 
VALUES ('Jan', 'Jansen', 'jan@example.nl', '$2y$10$...');
```

## Support
Voor vragen of problemen, check de code comments in de PHP bestanden voor meer details over de implementatie.
