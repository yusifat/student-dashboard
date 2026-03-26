# StudyBuddy - Installatie & Configuratie

## System Requirements
- PHP 8.0 of hoger
- MySQL 5.7 of hoger
- Apache/Nginx webserver
- Git versie control

## Installatie Stappen

### 1. Database Setup
```bash
# Login in MySQL
mysql -u root -p

# Maak database aan
CREATE DATABASE study_buddy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Selecteer database
USE study_buddy;

# Importeer schema
SOURCE database/schema.sql;
```

### 2. Database Configuration
Pas `config/Database.php` aan met jouw MySQL credentials:
```php
private $host = 'localhost';
private $db_name = 'study_buddy';
private $username = 'root';        // Jouw MySQL username
private $password = '';             // Jouw MySQL password
```

### 3. Webserver Configuration

#### Apache .htaccess (optioneel)
Zorg dat mod_rewrite is ingeschakeld:
```bash
a2enmod rewrite
```

#### Nginx (optioneel)
Voeg toe aan server block:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 4. Project Structure
```
public/            # Entry point voor web
├── index.php       # Redirect naar login/dashboard
├── login.php       # Inlog/registratie pagina
├── dashboard.php   # Student dashboard
├── grades.php      # Cijfers overzicht
├── tasks.php       # Deadlines/taken
├── materials.php   # Studiemateriaal
├── logout.php      # Logout
├── css/            # Stylesheets
├── js/             # JavaScript
├── admin/          # Admin panel
│   ├── courses.php
│   └── course-detail.php

src/                # Backend code
├── controllers/    # Request handlers
├── models/         # Database models
└── utils/          # Helper functions

config/             # Configuration
├── Database.php
database/           # Database files
├── schema.sql
```

## Gebruik

### Voor Studenten
1. Ga naar `http://localhost/` (of je webserver URL)
2. Klik op "Registreren" en vul je gegevens in
3. Inloggen met je studentnummer
4. Bekijk vakken, deadlines en cijfers

### Voor Docenten/Admins
1. Login met admin account
2. Ga naar Vakken Beheren (⚙️ in sidebar)
3. Maak vakken aan
4. Voeg taken en studiemateriaal toe
5. Students kunnen zich inschrijven

## Features

### Student Features
- ✅ Vakken overzicht (mijn vakken)
- ✅ Deadline tracking met urgency levels
- ✅ Cijfers management met automatische gemiddeldenbberekening
- ✅ Studiemateriaal links collectie
- ✅ Persoonlijk dashboard
- ✅ Sessie management & logout

### Admin Features
- ✅ Vak management (create, update, delete)
- ✅ Taken toevoegen per vak
- ✅ Studiemateriaal linksbeheren
- ✅ Student tellingen per vak
- ✅ Role-based access control

## Security Features

### Implementatie
- 🔒 SQL Injection preventie (prepared statements)
- 🔒 XSS preventie (htmlspecialchars output escaping)
- 🔒 Password hashing (ARGON2ID algorithm)
- 🔒 Session management
- 🔒 Role-based access control (RBAC)
- 🔒 CSRF token voorkoming (session-based)

### Best Practices
```php
// Prepared statements:
$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);

// Output escaping:
echo htmlspecialchars($user_input);

// Password hashing:
$hash = password_hash($password, PASSWORD_ARGON2ID);
$verify = password_verify($password, $hash);
```

## API Endpoints (Controller Methods)

### AuthController
- `login($student_number, $password)` - Login gebruiker
- `register($data)` - Registreer nieuwe student
- `logout()` - Logout & session destroy
- `requireLogin()` - Redirect als niet ingelogd
- `requireAdmin()` - Redirect als niet admin

### CoursesController
- `getStudentCourses($student_id)` - Get vakken student
- `getAdminCourses($admin_id)` - Get vakken docent
- `createCourse($admin_id, $data)` - Nieuw vak
- `getCourseDetails($course_id)` - Vak info

### GradesController
- `getStudentGrades($student_id)` - Get alle cijfers
- `getCourseAverage($student_id, $course_id)` - Gemiddelde per vak
- `getOverallAverage($student_id)` - Algemeen gemiddelde
- `addGrade($student_id, $data)` - Nieuw cijfer

### TasksController
- `getStudentTasks($student_id)` - Get alle taken
- `getCriticalTasks($student_id)` - Get kritieke taken
- `updateTaskStatus($task_id, $status)` - Update status
- `formatDeadline($deadline)` - Format datum

## Styling & Design

### Kleuren Palette
- Primair (Lichtblauw): `#ADD8E6`
- Secundair (Zachtgroen): `#90EE90`
- Gevaar (Rood): `#FF6B6B`
- Succes (Groen): `#51CF66`
- Achtergrond (Wit): `#FFFFFF`

### Layout
- Minimale sidebar navigatie met iconen
- Responsive grid layout
- Pastel kleuren voor rustgevend design
- Clean, professionele interface

## Troubleshooting

### Database Connection Error
```bash
# Check MySQL service
systemctl status mysql

# Check credentials in config/Database.php
mysql -u [username] -p -h localhost
```

### 404 Not Found
- Zorg dat mod_rewrite aanstaat (Apache)
- Check DocumentRoot naar public/ folder

### Session Errors
- Zorg dat session.save_path schrijfbaar is
- Check PHP error_log voor details

## Development Workflow

### Git Commits
Commits zijn georganiseerd met duidelijke messages:
```bash
git log --oneline
```

### Testing Database
```sql
-- Check tables
SHOW TABLES;

-- Check sample data
SELECT * FROM users;
SELECT * FROM courses;

-- Test join
SELECT c.name, COUNT(sc.id) as students
FROM courses c
LEFT JOIN student_courses sc ON c.id = sc.course_id
GROUP BY c.id;
```

## Deployment Checklist
- [ ] Database migrated naar production
- [ ] config/Database.php met production credentials
- [ ] .gitignore geconfigureerd
- [ ] error_reporting uitgeschakeld in production
- [ ] SSL certificaat geïnstalleerd
- [ ] Regelmmatige backups ingericht
- [ ] Logs gemonitord

---
**Versie 1.0** - Geïmplementeerd als MBO4 student project
