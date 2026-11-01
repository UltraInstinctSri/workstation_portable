# SIS Registration & Login (Lab-VI, Question 2)

HTML + CSS + JavaScript + PHP + MySQL. Needs PHP 7.4+ and MySQL/MariaDB (XAMPP works).

## Setup
1. Import the database:  `mysql -u root -p < database.sql`  (or import it in phpMyAdmin)
2. Open `config.php` and set `DB_USER` / `DB_PASS` if yours differ from root / empty.
3. Run the site from this folder:  `php -S localhost:8000`
   (or copy the folder to `htdocs` in XAMPP and open `http://localhost/sis_login/`)
4. Open http://localhost:8000

## Flow
Sign up -> Login (User ID + password + CAPTCHA) -> OTP (3 tries) -> Student / Faculty dashboard -> Logout

## Files
| File | Purpose |
|---|---|
| `database.sql` | Student, Faculty, Login tables |
| `config.php` | settings (`DEMO_MODE`), DB connection, helpers, validation rules |
| `signup.php` / `validate.js` | registration page + browser-side checks |
| `login.php` / `captcha.php` | login page + CAPTCHA image (refresh button, no page reload) |
| `otp.php` | 6-digit OTP, max 3 wrong attempts then back to home |
| `student_dashboard.php`, `faculty_dashboard.php`, `logout.php` | dashboards and logout |
| `style.css` | responsive styling |

## Notes
- `DEMO_MODE = true` shows the OTP on the OTP screen. Set it to `false` and add your own email/SMS code in `login.php` (marked with a comment) for real use.
- Password digest = SHA-256(random salt + password); salt and digest are stored in the `Login` table, as the lab asks. (For real systems, prefer bcrypt/Argon2.)
- All SQL uses prepared statements, forms have CSRF tokens, and the session id is renewed after login.
