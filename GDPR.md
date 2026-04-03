# GDPR Considerations

This document outlines how personal data is handled in this application in relation to GDPR (General Data Protection Regulation).

---

## What personal data is collected

| Data | Where it is stored | Why it is needed |
|---|---|---|
| Name | `users` table | Used to identify the user and display on orders |
| Email address | `users` table | Used as the login identifier |
| Password (hashed) | `users` table | Required for authentication |
| Order history | `orders` and `order_items` tables | Needed to process and track purchases |

No payment card data is stored. No tracking cookies are used.

---

## How data is protected

- **Passwords** are hashed with bcrypt (`password_hash(PASSWORD_DEFAULT)`) before being stored. Plain-text passwords are never saved.
- **Authentication** uses short-lived JWT tokens (expire after 24 hours). Tokens are stored in the browser's `localStorage` and are not sent in cookies.
- **SQL injection** is prevented by using PDO with parameterized queries throughout all repositories.
- **XSS / script injection** is prevented by sanitizing all user-supplied text with `htmlspecialchars()` before storing, and Vue's template interpolation escapes output by default.
- **Authorization** is enforced on every protected route using JWT validation. Admin-only routes return a 403 if a non-admin token is used.
- **HTTPS** should be enforced at the web server / reverse proxy level in production (not handled in application code).

---

## Data retention

- User accounts remain in the database until explicitly deleted by an admin via `DELETE /users/{id}`.
- Orders are kept indefinitely as part of business records. To support the right to erasure, an admin can delete a user account — the database is set up with `ON DELETE CASCADE` on the `orders` table, so all related orders are deleted automatically.

---

## User rights under GDPR

| Right | How it is handled |
|---|---|
| Right to access | Users can view their own orders via `GET /my-orders`. Admins can view all user data. |
| Right to erasure ("right to be forgotten") | Admin can delete a user via `DELETE /users/{id}`. This cascades to delete all their orders. |
| Right to data portability | Not currently implemented — user data can be exported by an admin from the database. |
| Right to rectification | Users can update their profile. Admins can modify user records. |

---

## Data minimization

Only the minimum data needed to operate the store is collected:
- No date of birth, phone number, or address is stored.
- The JWT token carries only user ID, email, role, and expiry — no sensitive fields.

---

## Third-party data sharing

No personal data is shared with third-party services. All data stays within the application's own database.
