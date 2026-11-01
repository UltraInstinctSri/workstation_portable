// Client-side checks for the signup form (the server repeats the same checks in config.php).
const COMMON_WORDS = ['abc', 'xyz', 'system', 'admin', 'root', 'india', 'qwerty', 'qerty', 'password', 'iloveyou', 'welcome'];

// Weak = common word, or 3 digits in a row that repeat (222), rise (123) or fall (321)
function isWeak(s) {
    if (COMMON_WORDS.some(w => s.toLowerCase().includes(w))) return true;
    for (let i = 0; i + 2 < s.length; i++) {
        const t = s.slice(i, i + 3);
        if (!/^\d{3}$/.test(t)) continue;
        const [a, b, c] = t.split('').map(Number);
        if ((a === b && b === c) || (b - a === 1 && c - b === 1) || (a - b === 1 && b - c === 1)) return true;
    }
    return false;
}

// True if one string is inside the other (case-insensitive)
function overlap(a, b) {
    a = a.toLowerCase(); b = b.toLowerCase();
    return a !== '' && b !== '' && (a.includes(b) || b.includes(a));
}

function validateSignup(uid, username, email, pw, pw2) {
    const err = [];

    if (!/^[A-Za-z0-9]{10,}$/.test(uid) || !/[A-Z]/.test(uid) || !/[a-z]/.test(uid) || !/[0-9]/.test(uid))
        err.push('User ID: at least 10 letters/digits, with an uppercase letter, a lowercase letter and a digit.');
    if (uid.toLowerCase() === email.toLowerCase() || overlap(uid, username))
        err.push('User ID cannot be your email or username, or part of them.');
    if (isWeak(uid))
        err.push('User ID is too common (no 123 / 222 / 321 patterns or words like ABC, admin, root...).');

    if (!/^[A-Za-z0-9_]{3,30}$/.test(username))
        err.push('Username: 3-30 letters, digits or underscore.');

    if (pw.length < 10)               err.push('Password must have at least 10 characters.');
    if (!/[A-Z]/.test(pw))            err.push('Password needs an uppercase letter.');
    if (!/[a-z]/.test(pw))            err.push('Password needs a lowercase letter.');
    if (!/[0-9]/.test(pw))            err.push('Password needs a digit.');
    if (!/[^A-Za-z0-9\s]/.test(pw))   err.push('Password needs a special character (@ # $ % & ! ...).');
    if (/\s/.test(pw))                err.push('Password cannot contain spaces.');
    if (pw !== pw2)                   err.push('Password and Confirm Password do not match.');
    if (isWeak(pw))                   err.push('Password is too common (no 123 / 222 / 321 patterns or words like password, admin...).');
    if (overlap(uid, pw) || overlap(username, pw))
        err.push('Password and User ID / username cannot contain each other.');

    return err;
}

const form = document.getElementById('signupForm');
const box  = document.getElementById('jsErrors');
let tried = false;

function check() {
    const f = form.elements;
    const errors = validateSignup(f.user_id.value.trim(), f.username.value.trim(), f.email.value.trim(), f.password.value, f.confirm.value);
    box.innerHTML = errors.length ? '<ul>' + errors.map(m => '<li>' + m + '</li>').join('') + '</ul>' : '';
    box.style.display = errors.length ? 'block' : 'none';
    return errors.length === 0;
}

form.addEventListener('submit', ev => {
    tried = true;
    if (!check()) ev.preventDefault();
});
form.addEventListener('input', () => { if (tried) check(); });
