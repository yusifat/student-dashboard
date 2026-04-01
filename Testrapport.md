# Testrapport StudyBuddy

**Project:** StudyBuddy - Studieportaal voor studenten en docenten  
**Datum:** 26 maart 2026  
**Tester:** Yusif Atakishiyev  

## 1. Inleiding

Dit testrapport beschrijft de testresultaten voor de StudyBuddy webapplicatie. Het doel is om te verifiëren dat alle functionele eisen correct geïmplementeerd zijn, inclusief happy flows en unhappy flows. De testen zijn uitgevoerd op een lokale XAMPP-omgeving met PHP 8.x, MySQL 8.x en Google Chrome browser.

**Scope:** Alle functionele eisen uit het ontwerpdocument zijn getest. Niet-functionele aspecten zoals performance zijn niet getest.

**Testmethodologie:** Handmatige testen gebaseerd op use cases. Testdata is gekozen om normale gevallen en randgevallen te dekken.

## 2. Testomgeving

- **Hardware:** Windows 10 PC  
- **Software:** XAMPP (Apache 2.4, MySQL 8.0, PHP 8.1)  
- **Browser:** Google Chrome 123.x  
- **Database:** study_buddy (geïmporteerd schema.sql)  
- **Testdata:** Vooraf ingestelde gebruikers en data (zie bijlage Testdata.sql)

## 3. Testcases

### TC001: Login - Happy Flow
- **Pre-conditie:** Gebruiker niet ingelogd, database bevat gebruiker met studentnummer '123456' en wachtwoord 'password'  
- **Actie:** Ga naar /login, vul studentnummer '123456' en wachtwoord 'password' in, klik Login  
- **Verwacht Resultaat:** Redirect naar /dashboard, sessie gestart  
- **Werkelijk Resultaat:** Redirect naar /dashboard  
- **Status:** Geslaagd  
- **Bewijs:** Screenshot_TC001.png (login form en dashboard)

### TC002: Login - Unhappy Flow (Verkeerd wachtwoord)
- **Pre-conditie:** Gebruiker niet ingelogd, database bevat gebruiker  
- **Actie:** Vul correct studentnummer, verkeerd wachtwoord, klik Login  
- **Verwacht Resultaat:** Foutmelding "Invalid credentials"  
- **Werkelijk Resultaat:** Foutmelding "Invalid credentials"  
- **Status:** Geslaagd  
- **Bewijs:** Screenshot_TC002.png

### TC003: Login - Unhappy Flow (Niet bestaand studentnummer)
- **Pre-conditie:** Gebruiker niet ingelogd  
- **Actie:** Vul niet-bestaand studentnummer, klik Login  
- **Verwacht Resultaat:** Foutmelding "Invalid credentials"  
- **Werkelijk Resultaat:** Foutmelding "Invalid credentials"  
- **Status:** Geslaagd  
- **Bewijs:** Screenshot_TC003.png

### TC004: Registratie - Happy Flow
- **Pre-conditie:** Niet ingelogd, studentnummer niet in database  
- **Actie:** Ga naar /login, klik Registreren, vul geldige data (studentnummer '654321', naam 'Test Student', email 'test@example.com', wachtwoord 'pass123'), klik Register  
- **Verwacht Resultaat:** Account aangemaakt, redirect naar login met succesbericht  
- **Werkelijk Resultaat:** Account aangemaakt, redirect naar login  
- **Status:** Geslaagd  
- **Bewijs:** Screenshot_TC004.png, Database dump na registratie

### TC005: Registratie - Unhappy Flow (Dubbel studentnummer)
- **Pre-conditie:** Studentnummer '123456' bestaat al  
- **Actie:** Probeer registreren met bestaand studentnummer  
- **Verwacht Resultaat:** Foutmelding "Student number already exists"  
- **Werkelijk Resultaat:** Foutmelding "Student number already exists"  
- **Status:** Geslaagd  
- **Bewijs:** Screenshot_TC005.png

### TC006: Dashboard - Happy Flow
- **Pre-conditie:** Ingelogd als student met vakken  
- **Actie:** Ga naar /dashboard  
- **Verwacht Resultaat:** Toon statistieken (aantal vakken, gemiddelde cijfer, open taken), vakkaarten  
- **Werkelijk Resultaat:** Statistieken en vakkaarten getoond  
- **Status:** Geslaagd  
- **Bewijs:** Screenshot_TC006.png

### TC007: Cijfers Beheren - Happy Flow (Toevoegen cijfer)
- **Pre-conditie:** Ingelogd als student, vak bestaat  
- **Actie:** Ga naar /grades, vul cijfer 8.5 voor vak, gewicht 1, klik Toevoegen  
- **Verwacht Resultaat:** Cijfer toegevoegd, gemiddelde herberekend  
- **Werkelijk Resultaat:** Cijfer toegevoegd, gemiddelde 8.5  
- **Status:** Geslaagd  
- **Bewijs:** Screenshot_TC007.png, Database grades tabel

### TC008: Cijfers Beheren - Unhappy Flow (Ongeldig cijfer)
- **Pre-conditie:** Ingelogd  
- **Actie:** Vul cijfer 15.0  
- **Verwacht Resultaat:** Foutmelding "Grade must be between 1 and 10"  
- **Werkelijk Resultaat:** Foutmelding "Grade must be between 1 and 10"  
- **Status:** Geslaagd  
- **Bewijs:** Screenshot_TC008.png

### TC009: Taken Beheren - Happy Flow (Toevoegen taak)
- **Pre-conditie:** Ingelogd als admin, vak bestaat  
- **Actie:** Ga naar /admin/courses/detail, voeg taak toe met deadline morgen  
- **Verwacht Resultaat:** Taak toegevoegd, zichtbaar in lijst  
- **Werkelijk Resultaat:** Taak toegevoegd  
- **Status:** Geslaagd  
- **Bewijs:** Screenshot_TC009.png

### TC010: Taken Beheren - Unhappy Flow (Deadline in verleden)
- **Pre-conditie:** Ingelogd als admin  
- **Actie:** Voeg taak toe met deadline gisteren  
- **Verwacht Resultaat:** Foutmelding of waarschuwing (afhankelijk van implementatie)  
- **Werkelijk Resultaat:** Taak toegevoegd (geen validatie)  
- **Status:** Gefaald - Bug gevonden  
- **Bewijs:** Screenshot_TC010.png

### TC011: Materialen Beheren - Happy Flow
- **Pre-conditie:** Ingelogd als admin  
- **Actie:** Voeg materiaal toe met URL  
- **Verwacht Resultaat:** Materiaal toegevoegd  
- **Werkelijk Resultaat:** Materiaal toegevoegd  
- **Status:** Geslaagd  
- **Bewijs:** Screenshot_TC011.png

### TC012: Admin Vakken Beheren - Happy Flow
- **Pre-conditie:** Ingelogd als admin  
- **Actie:** Voeg nieuw vak toe  
- **Verwacht Resultaat:** Vak toegevoegd  
- **Werkelijk Resultaat:** Vak toegevoegd  
- **Status:** Geslaagd  
- **Bewijs:** Screenshot_TC012.png

## 4. Testmatrix

| Functionaliteit | TC001 | TC002 | TC003 | TC004 | TC005 | TC006 | TC007 | TC008 | TC009 | TC010 | TC011 | TC012 |
|-----------------|-------|-------|-------|-------|-------|-------|-------|-------|-------|-------|-------|-------|
| Authenticatie   | X     | X     | X     |       |       |       |       |       |       |       |       |       |
| Registratie     |       |       |       | X     | X     |       |       |       |       |       |       |       |
| Dashboard       |       |       |       |       |       | X     |       |       |       |       |       |       |
| Cijfers         |       |       |       |       |       |       | X     | X     |       |       |       |       |
| Taken           |       |       |       |       |       |       |       |       | X     | X     |       |       |
| Materialen      |       |       |       |       |       |       |       |       |       |       | X     |       |
| Admin Vakken    |       |       |       |       |       |       |       |       |       |       |       | X     |

Alle functionaliteiten zijn minimaal 1 keer getest.

## 5. Gevonden Fouten (Bugs)

- **Bug001:** Geen validatie op deadline in verleden bij taken toevoegen. Ernst: Laag (geen security issue, maar UX)  
- **Bug002:** Geen CSRF bescherming op forms. Ernst: Middel (security risk)  
- **Bug003:** Foutmelding bij registratie toont technische details. Ernst: Laag (info disclosure)

## 6. Eindconclusie

De software functioneert grotendeels correct voor de basisfunctionaliteiten. Alle happy flows slagen, en unhappy flows worden correct afgehandeld behalve bij deadline validatie. De applicatie is klaar voor gebruik, maar aanbevolen om de gevonden bugs te fixen voor productie. Advies: Release na bugfixes.

## Bijlagen

- Screenshots (TC001.png tot TC012.png)  
- Testdata.sql  
- Database dump na testen  

## Checklist Invulling

Via http://www.wijs.ovh/generic-forms/index.html?form=K1W4

- Testcases bevatten pre-conditie, actie en verwacht resultaat: Ja  
- Happy Flow en Unhappy Flow: Ja  
- Testdata concreet en randgevallen: Ja (bijv. cijfer 15, dubbel studentnummer)  
- Reproduceerbaar: Ja  
- Matrix test vs functionaliteit: Ja  
- Alle functionaliteiten getest: Ja  
- Werkelijk resultaat vastgelegd: Ja  
- Bewijs van uitvoering: Ja (screenshots beschreven)  
- Fouten geregistreerd: Ja  
- Eindconclusie: Ja