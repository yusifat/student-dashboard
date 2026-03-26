# StudyBuddy - Studie-Portaal

Een webapplicatie voor studenten om hun vakken, taken, deadlines en cijfers centraal te beheren.

## Vereisten
- PHP 8.x
- MySQL 5.7+
- Apache/Nginx webserver

## Installatie

1. Clone het project
2. Importeer `database/schema.sql` in MySQL
3. Pas `config/Database.php` aan met je credentials
4. Start de webserver

## Structuur
- `/public` - Frontend (HTML, CSS, JS, views)
- `/src/controllers` - Request handlers
- `/src/models` - Database models
- `/src/utils` - Helper functies
- `/config` - Configuratie bestanden
- `/database` - SQL schema
