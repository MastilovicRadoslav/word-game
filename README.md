# Full Stack Word Game Application

This is a full stack coding challenge solution for Company challenge.  
The project implements a simple **Word Game** application with scoring rules, REST API, tests, and frontend interface.

---

## 🚀 Tech Stack

- **Backend:** PHP (Symfony)
- **Frontend:** React + Vite + Redux Toolkit + Ant Design
- **Testing:** PHPUnit (unit & functional tests)
- **Persistence:** Local dictionary file (`dictionaries/words.txt`) and browser local storage (frontend)

---

## 📖 Project Description

The application allows the user to enter a word.  
Only words present in the English dictionary (loaded from `words.txt`) are allowed.

### Scoring Rules
1. **+1 point** for each unique letter.
2. **+3 points** if the word is a **palindrome**.
3. **+2 points** if the word is an **almost palindrome**  
   (definition: if by removing at most one letter the word becomes a palindrome).

### Backend
- REST API endpoint: `POST /api/words/score`
- Request body: `{ "word": "example" }`
- Response: JSON with normalized word, score, and flags (palindrome, almost palindrome, valid).  
- Includes **functional tests** (REST API), **unit tests** (services), and a **Symfony console command** (`app:score-word`).

### Frontend
- Word submission form with validation.
- List of all submitted words, **sorted by score**.
- Last submitted word is **highlighted**.
- List is persisted using `localStorage` so it survives browser refresh.

---

## 🏃 Sprint Plan

### 🚀 Sprint 1 – Backend REST API (Part 1)

**Cilj:** imati potpuno funkcionalan REST API sa validacijom riječi i scoring logikom.  
**Zadaci:**
- Kreirati Symfony skeleton projekat + dodati potrebne pakete.  
- Implementirati `DictionaryService` (učitavanje engleskog rječnika).  
- Implementirati `WordGameService` (normalizacija, palindrome, almost-palindrome, scoring).  
- Napraviti `WordApiController` sa endpointom `/api/words/score`.  
- Dodati funkcionalne testove za REST API (npr. `tests/Functional/WordApiTest.php`).  

### ⚡ Sprint 2 – Unit testovi + Console app (Part 2)

**Cilj:** pokriti core logiku testovima i imati konzolni command-line način rada.  
**Zadaci:**
- Pisanje unit testova za `WordGameService`.  
- Pisanje unit testova za `DictionaryService`.  
- Kreirati Symfony konzolnu komandu `app:score-word` koja prima riječ i vraća JSON ili tekstualni rezultat.  
- Testirati konzolnu komandu.  

### 🌐 Sprint 3 – Frontend (Part 3)

**Cilj:** React + Redux aplikacija povezana sa backendom.  
**Zadaci:**
- Postaviti React + Vite projekat sa Redux Toolkit-om.  
- Napraviti formu za unos riječi.  
- Povezati formu sa backend API-jem.  
- Napraviti listu riječi sortiranu po score-u.  
- Highlight zadnje dodate riječi.  
- Dodati trajnu pohranu liste (`localStorage` ili `redux-persist`).  

---

## ⚡ Installation & Run

### Backend (Symfony)
```bash
# Clone repo and install dependencies
composer install

# Run Symfony local server
symfony serve -d
# or
php -S 127.0.0.1:8000 -t public
```

### Frontend (React + Vite)
```bash
cd frontend
npm install
npm run dev
```

Frontend will run on [http://localhost:5173](http://localhost:5173) and connect to backend at `http://localhost:8000/api`.

---

## 🧪 Testing

The project comes with **three levels of testing**:

### 1. Functional tests (REST API)
Located in `tests/Functional/WordApiTest.php`.  
They send real HTTP requests to the API and check the responses.

Run:
```bash
php bin/phpunit --testsuite functional
```

Example checks:
- ✅ Valid palindrome (`level`) returns `200` with correct score.  
- ✅ Almost palindrome (`abca`) returns `200` with correct flags.  
- ✅ Word not in dictionary returns `422`.  
- ✅ Invalid characters or empty input returns `400`.  

### 2. Unit tests (services)
Located in `tests/Unit/DictionaryServiceTest.php` and `tests/Unit/WordGameServiceTest.php`.  
They test dictionary loading, normalization, palindrome logic, scoring.

Run:
```bash
php bin/phpunit --testsuite unit
```

Example checks:
- `normalize("He!!o-World") === "heoworld"`  
- `isPalindrome("racecar") === true`  
- `isAlmostPalindrome("abca") === true`  
- Correct calculation of score for different words.  

### 3. Console command tests
Located in `tests/Console/ScoreWordCommandTest.php`.  
They simulate running `php bin/console app:score-word` with different inputs.

Run all tests:
```bash
php bin/phpunit
```

### 4. Manual console testing
You can also try directly in the terminal:

```bash
php bin/console app:score-word level
php bin/console app:score-word abca --json
php bin/console app:score-word unknown
php bin/console app:score-word he!!o
```

Expected:
- Valid words print success + score.  
- Invalid words return error/warning and exit code `2`.  
- Option `--json` outputs machine-readable JSON.  

---

## 📂 Project Structure

```
backend/
  ├── config/
  ├── src/
  │   ├── Controller/
  │   ├── Service/
  │   └── ...
  ├── tests/
  └── dictionaries/words.txt

frontend/
  ├── src/
  │   ├── components/
  │   ├── pages/
  │   ├── services/
  │   └── store/
  └── vite.config.js
```

---

## ✨ Example API Usage

### Request
```http
POST /api/words/score
Content-Type: application/json

{ "word": "level" }
```

### Response
```json
{
  "input": "level",
  "word": "level",
  "isValid": true,
  "uniqueLetters": 3,
  "isPalindrome": true,
  "isAlmostPalindrome": false,
  "score": 6
}
```

---

## 👨‍💻 Author

Radoslav Mastilović  
Solution for Company challenge  
Date: September 2025
