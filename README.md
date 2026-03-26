# StudyBuddy - Studie-Portaal 📚

Een webapplicatie voor studenten om hun vakken, taken, deadlines en cijfers centraal te beheren. Ontwikkeld als MBO4 eindproject.

## 🎯 Project Overzicht

**Opdrachtgever**: Studentenraad "Beter Leren"  
**Contactpersoon**: Dhr. L. De Boeck  
**Student**: Yusif  
**Datum**: 8 januari 2026

StudyBuddy lost het probleem op dat studenten moeite hebben met het organiseren van hun studietijd. Informatie staat verspreid over verschillende systemen, PDF's raken kwijt en de voortgang van projecten is onduidelijk. 

## ✨ Key Features

### Voor Studenten
- 📊 **Dashboard** - Overzicht van actieve vakken
- 📈 **Cijfers Management** - Invoer resultaten met automatische gemiddelde berekening
- ⏰ **Deadline Tracking** - Taken gesorteerd op urgentie (kritiek < 2 dagen)
- 📚 **Studiemateriaal** - Centrale verzameling van links en documenten
- 👤 **Persoonlijk Profiel** - Eigen gegevens beheer

### Voor Docenten (Admins)
- 📋 **Vak Management** - Nieuwe vakken aanmaken en beheren
- ✋ **Taken Toewijzen** - Deadlines instellen per vak
- 📎 **Materiaal Beheer** - Links naar studiemateriaal pushen
- 👥 **Student Overzicht** - Zien hoeveel studenten per vak ingeschreven zijn

## 🛠️ Technische Stack

| Component | Versie | Details |
|-----------|--------|---------|
| **Backend** | PHP 8.x | OOP, PDO, Prepared Statements |
| **Database** | MySQL 5.7+ | Relational DB met InnoDB |
| **Frontend** | HTML5/CSS3 | Responsive, Mobile-friendly |
| **Security** | N/A | ARGON2ID password hashing, RBAC |

## 📁 Project Structuur

```
student-dashboard/
├── public/                    # Web root
│   ├── index.php             # Entry point
│   ├── login.php             # Auth pagina
│   ├── dashboard.php         # Student dashboard
│   ├── grades.php            # Cijfers overzicht
│   ├── tasks.php             # Deadlines lijst
│   ├── materials.php         # Materiaal collectie
│   ├── logout.php            # Logout
│   ├── css/
│   │   └── style.css         # Main stylesheet
│   ├── js/                   # JavaScript files
│   └── admin/
│       ├── courses.php       # Vak management
│       └── course-detail.php # Vak details (taken, materiaal)
│
├── src/
│   ├── controllers/          # Request handlers
│   │   ├── AuthController.php
│   │   ├── CoursesController.php
│   │   ├── GradesController.php
│   │   └── TasksController.php
│   ├── models/               # Database models
│   │   ├── User.php
│   │   ├── Course.php
│   │   ├── Grade.php
│   │   ├── Task.php
│   │   └── Material.php
│   └── utils/
│       └── SessionManager.php
│
├── config/
│   └── Database.php          # PDO connection
│
├── database/
│   └── schema.sql            # MySQL schema
│
├── README.md                 # Dit bestand
├── SETUP.md                  # Installatie instructies
└── .gitignore               # Git ignore rules
```

## 🚀 Quick Start

### Vereisten
- PHP 8.0+
- MySQL 5.7+
- Apache/Nginx webserver
- Git

### Installatie

1. **Clone repository**
```bash
git clone <repository-url> student-dashboard
cd student-dashboard
```

2. **Database setup**
```bash
# Login in MySQL
mysql -u root -p

# Database aanmaken
CREATE DATABASE study_buddy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE study_buddy;
SOURCE database/schema.sql;
```

3. **Configuratie**
- Pas `config/Database.php` aan met je MySQL credentials
- Zorg dat `public/` als webroot is ingesteld

4. **Start webserver**
```bash
# PHP built-in server (development)
cd public
php -S localhost:8000

# Of gebruik Apache/Nginx
```

5. **Access app**
```
http://localhost:8000
```

## 📖 Uso Instructies

### Registratie & Login
1. Klik op "Registreren" tab
2. Vul studentnummer, naam, email in
3. Maak wachtwoord aan (min 6 characters)
4. Login met studentnummer + wachtwoord

### Student Workflow
1. **Dashboard**: Bekijk je vakken
2. **Deadlines**: Zie taken gesorteerd op urgentie
   - 🔴 Kritiek (< 2 dagen) in rode tekst
   - 📅 Gepland in blauw
   - ✅ Voltooid doorgestreept
3. **Cijfers**: Voer behaalde resultaten in
   - Rode achtergrond voor < 5.5
   - Automatisch gemiddelde berekening
4. **Materiaal**: Download/open studiemateriaal links

### Admin Workflow
1. **Vakken Beheren**: Maak nieuwe vakken aan
2. **Vak Details**: 
   - Voeg taken toe met deadlines
   - Voeg studiemateriaal links toe
3. **Student Tellingen**: Zie hoeveel students per vak

## 🔒 Security

### Implementatie
```php
// SQL Injection preventie
$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);

// Output escaping
echo htmlspecialchars($user_input, ENT_QUOTES);

// Password hashing
$hash = password_hash($password, PASSWORD_ARGON2ID);
$verify = password_verify($password, $hash);

// Role-based access
if(!SessionManager::isAdmin()) {
    header('Location: /dashboard.php');
}
```

### Features
- ✅ Prepared statements (geen SQL injection)
- ✅ XSS preventie via htmlspecialchars
- ✅ ARGON2ID password hashing
- ✅ Session management
- ✅ Role-based access control (RBAC)
- ✅ Student kan enkel eigen cijfers zien

## 🎨 Design & Styling

### Kleurenpalette
- **Primair**: Lichtblauw `#ADD8E6` - Rust en focus
- **Secundair**: Zachtgroen `#90EE90` - Positiviteit
- **Danger**: Rood `#FF6B6B` - Alerts en kritiek
- **Success**: Groen `#51CF66` - Voltooid
- **Achtergrond**: Wit `#FFFFFF` - Clean design

### Layout
- Minimale sidebar met iconen
- Responsive grid layouts
- Mobile-friendly interface
- Focus op leesbaarheid

## 📊 Database Schema

### Tabellen
- `users` - Gebruiker accounts (student/admin)
- `courses` - Vakken
- `student_courses` - Inschrijvingen (many-to-many)
- `tasks` - Deadlines per vak
- `grades` - Cijfers per student per vak
- `materials` - Studiemateriaal links

### Relaties
```
Course (1) ─── (N) Task
Course (1) ─── (N) Grade
Course (1) ─── (N) Material
User (1) ─── (N) Grade
```

## 🧪 Testing

### Test Accounts
```
Student:
- Studentnummer: TEST001
- Wachtwoord: Test123!

Admin:
- Studentnummer: ADMIN01
- Wachtwoord: Admin123!
```

### Database Test
```sql
-- Check alle tabellen
SHOW TABLES;

-- Sample queries
SELECT * FROM users;
SELECT c.name, COUNT(sc.id) FROM courses c
LEFT JOIN student_courses sc ON c.id = sc.course_id
GROUP BY c.id;
```

## 📝 Git Commits

Het project is goed georganiseerd met duidelijke commits:

```
e5336cc - feat: Initial project setup met database schema e core models
204693f - feat: Authenticatie systeem en Dashboard implementatie
e0d0f49 - feat: Student views voor cijfers, taken en materialen
cb4dda1 - feat: Admin panel voor docenten met vak management
69a2fb4 - docs: Setup instructies en finalisatie
```

## 📚 Documentatie

- **SETUP.md** - Gedetailleerde installatie & configuratie
- **Code comments** - Docblocks op alle funktie
- **Database schema** - SQL in database/schema.sql

## 🤝 Contributing

Dit is een eindproject. Voor wijzigingen:
1. Fork repository
2. Create feature branch (`git checkout -b feature/feature-name`)
3. Commit changes (`git commit -m 'feat: description'`)
4. Push to branch (`git push origin feature/feature-name`)
5. Open Pull Request

## 📄 Licentie

© 2026 Joey - StudyBuddy MBO4 Project

---

**Status**: ✅ Production Ready  
**Version**: 1.0.0  
**Last Updated**: 26 maart 2026
