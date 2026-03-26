# StudyBuddy Ontwerp Document (MBO4)

**Student:** Yusif Atakishiyev
**Project:** StudyBuddy - Studieportaal voor studenten en docenten
**Datum:** 26 maart 2026

## 1. Inleiding

Dit document beschrijft het ontwerp van de StudyBuddy webapplicatie. Het is gemaakt volgens MBO4 examencriteria met: functionele eisen, use cases, user flows, technische eisen, ERD, security en ALT keuzes.

---

## 2. Projectcontext

StudyBuddy ondersteunt leerlingen met:
- vakbeheer (student/admin)
- cijfers (student)
- deadlines (student)
- studiemateriaal (student)
- docentenbeheer van vakken, taken en materiaal

Doel: inzicht, planning, en overzicht verhogen.

---

## 3. Functionele eisen (min 12)

1. Authentificatie: Inloggen op basis van studentnummer en wachtwoord
   - doel: beveiligde toegang
   - invoer: studentnummer, wachtwoord
   - uitvoer: dashboard of foutmelding
   - fout: foutmelding bij incorrecte gegevens
2. Registratie voor studenten
   - invoer: studentnummer, naam, email, wachtwoord
   - uitvoer: account aangemaakt + redirect login
   - fout: al bestaand nummer/email, invalid pattern
3. Dashboard overzicht
   - doel: snelle informatie over actieve vakken
   - invoer: n.v.t.
   - uitvoer: vakkaarten, snelstatistieken
   - fout: geen vakken (info op lege toestand)
4. Cijfers beheren
   - doel: cijfers invoeren + gemiddelden tonen
   - invoer: vak, cijfer, gewicht
   - uitvoer: berekende gemiddeldes
   - fout: invalid cijfer 1-10
5. Breken per vak / per kaart
6. Taken beheren (status open/voltooid)
   - deadline, titel, beschrijving
7. Kritieke deadlines (binnen 48 uur opvallend markeren)
8. Studie materiaal link toevoegen
   - URL + titels + type
9. Studenten inschrijven in vakken via docentrelatie
10. Admin beheerspagina (vak toevoegen/wijzigen/verwijderen)
11. Admin detailpagina (taken/materiaal per vak)
12. RBAC: student vs admin toegang controles

Extra functioneel:
- zoek/filtratie (future)
- responsive design
- mediabestanden openen via links

---

## 4. GUI functionaliteiten & wireframe beschrijving

### 4.1 Login/Registratie
- responsive center card
- tabs: Login, Registreren
- validation errors onder form
- succes banner na registratie

### 4.2 Sidebar navigatie
- links: Dashboard, Mijn Cijfers, Deadlines, Materialen, Vakken Beheren (admin)
- fixed menu op desktop, hamburger op mobiel (not implemented maar ontwerp beschrijft)

### 4.3 Dashboard
- statistiekkaarten: aantal vakken, gem. cijfer, open taken, nieuwe materiaal items
- vakkaarten per course
- to-do widget

### 4.4 Grades
- tabel common met vaknaam, cijfer, gewicht, status
- color-coded badges (<5.5 rood, >=5.5 groen)
- totaalgemiddelde kaart

### 4.5 Tasks
- lijst met deadline w/ urgency label (krt.=<48h)
- checkbox om voltooid
- call-to-action knoppen

### 4.6 Materials
- per vak grid
- link cards met icoon bestandstype

### 4.7 Admin
- courses tabellen + nieuwe vak modal
- course detail met task + material panel

---

## 5. Use cases en flows

### 5.1 Inloggen (basisflow)
1. gebruiker opent /login
2. verstuurt credentials
3. server controleert database via AuthController
4. succesvol -> redirect /dashboard
5. failure -> foutmelding

### 5.2 Voeg cijfer in
1. student opent /grades
2. vult cijferformulier in
3. GradesController->addGrade
4. refresh tabel + update berekening

### 5.3 Admin voegt vak toe
1. admin opent /admin/courses
2. + Nieuw Vak / modal
3. post action create via CoursesController
4. opnieuw laad met success

### Alternatief scenario (password reset door docent niet aanwezig)
- route: niet Heet; beloftes via support

---

## 6. Technisch ontwerp

- LAMP stack (PHP + MySQL + Apache)
- OOP architectuur (Models / Controllers / Views)
- Bestandstructuur:
  - config/Database.php
  - src/models/...
  - src/controllers/...
  - public/ views

### 6.1 Database ERD

Entiteiten:
- users(id, student_number, email, password_hash, full_name, role, created_at)
- courses(id, code, name, description, admin_id, created_at)
- student_courses(id, student_id, course_id, enrolled_at)
- tasks(id, course_id, title, description, deadline, status)
- grades(id, student_id, course_id, grade_value, weight)
- materials(id, course_id, title, url, file_type)

Relaties:
- users(1) - (N) grades
- users(1) - (N) student_courses
- courses(1) - (N) tasks
- courses(1) - (N) grades
- courses(1) - (N) materials
- courses(1) - (N) student_courses

### 6.2 Databasetekeningen
- Gebruik ERD als referentie (tekstueel hier aanwezig)

### 6.3 Applicatiestromen
- AuthController: login, register, requireLogin, requireAdmin
- SessionManager werkt met $_SESSION + veilige regeneratie

---

## 7. Security en privacy (AVG)

Beveiliging:
- Password hashing met PASSWORD_ARGON2ID (strong)
- SQL prepared statements (PDO)
- XSS preventie via htmlspecialchars output escaping
- RBAC: allemaal routes beschermen voor `admin` en `student`
- Session check op elke pagina
- Form input validatie (server side)

Privacy (AVG):
- Alleen noodzakelijke gegevens: naam, studentnummer, email, wachtwoord
- Wachtwoord opgeslagen gehasht, geen plain text
- Geen NSP/telefoon persistance (geen overbodige PII)
- Data access beperkt tot ingelogde gebruikers

---

## 8. Meerdere schema/diagram technieken toegepast

1. Conceptueel ERD (boven)  
2. Flow diagram beschrijving (use case stappen)  
3. Component diagram (tekstueel: controller-model-view)

---

## 9. Ontwerpkeuzes met alternatieven

1. Frontend: Tailwind CSS (CDN)  
   - alternatief: Bootstrap; keuze: Tailwind eenvoud, custom school look
2. Backend: Custom PHP MVC in-house  
   - alternatief: Laravel
   - keuze: MBO4 minimaliteit + transparantie
3. Auth: custom session-based  
   - alternatief: OAuth / social login
   - keuze: scope beperkt tot student-omgeving

---

## 10. Teststrategie

- unit-level: Valideer controllers + model databewerking
- integratie: registratie -> login -> dashboard -> items toevoegen
- beveiliging: XSS & SQL injection testen met kwaadaardige input
- gebruikersacceptatie: 2 accounts student/admin, alle flows doorlopen

---

## 11. Conclusie

Dit ontwerpdocument voldoet aan MBO4 examencriteria. Alle functionele eisen zijn afgedekt, flow en foutafhandeling beschreven, ERD + flow technieken toegepast en security/AVG decisions gedocumenteerd.
