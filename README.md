# 🎟 GoalTicket — Football Ticketing System

## 📌 Overview

GoalTicket is a web-based football ticketing system developed as part of my university Work-Based Learning project.

The application allows supporters to browse fixtures, register for an account, book match tickets, receive QR-based tickets, and view their bookings.

An administrative area allows authorised staff to manage fixtures, validate tickets, manage administrator accounts, and support matchday operations.

---

## ✨ Key Features

### Supporter Features

- User registration and secure login
- Browse upcoming football fixtures
- View fixture details
- Book match tickets
- Generate QR-based digital tickets
- View previously purchased tickets
- Booking confirmation
- Email confirmation support
- Responsive web interface

### Admin Features

- Secure administrator login
- Admin dashboard with key statistics
- Fixture management
- Add, edit and delete fixtures
- QR ticket scanning and validation
- Administrator account management
- Support/help section
- Active navigation highlighting

### Additional Features

- MySQL database integration
- Secure password hashing
- PDO prepared statements
- Session-based authentication
- Progressive Web App (PWA) support
- Service Worker and offline page
- Local configuration separated from public source code

---

## 🛠 Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- PDO
- Composer
- Apache
- MAMP / XAMPP
- Git
- GitHub

---

## 📂 Project Structure

```text
football-ticketing-system/
│
├── admin/
│   ├── admins.php
│   ├── dashboard.php
│   ├── fixture_delete.php
│   ├── fixture_form.php
│   ├── fixtures.php
│   ├── login.php
│   ├── logout.php
│   ├── scan.php
│   └── support.php
│
├── assets/
│   ├── css/
│   ├── icons/
│   ├── images/
│   └── js/
│
├── config/
│   ├── config.php
│   ├── config.local.example.php
│   ├── database.php
│   └── database.local.example.php
│
├── includes/
│   ├── admin_footer.php
│   ├── admin_guard.php
│   ├── admin_header.php
│   ├── email_helper.php
│   ├── footer.php
│   ├── header.php
│   └── qr_helper.php
│
├── public/
│   ├── book.php
│   ├── booking_success.php
│   ├── delete_account.php
│   ├── fixture_detail.php
│   ├── fixtures.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── my_tickets.php
│   ├── register.php
│   ├── support.php
│   └── view_ticket.php
│
├── scripts/
│   └── create_admin.php
│
├── screenshots/
│
├── .gitignore
├── composer.json
├── composer.lock
├── goalticket_db.sql
├── index.php
├── LICENSE
├── manifest.json
├── offline.html
├── README.md
└── sw.js
```

---

## 🚀 Getting Started

### Requirements

You will need:

- PHP 8.0 or later
- MySQL
- Apache
- Composer
- MAMP, XAMPP or another local PHP environment

---

## ⚙️ Installation

### 1. Clone the repository

```bash
git clone https://github.com/ben-ekay/football-ticketing-system.git
```

Move into the project directory:

```bash
cd football-ticketing-system
```

### 2. Install Composer dependencies

```bash
composer install
```

### 3. Create the database

Create a MySQL database named:

```text
goalticket_db
```

Then import:

```text
goalticket_db.sql
```

The SQL file contains the database structure and sample football fixtures.

It does not include user accounts, administrator accounts, bookings or generated QR tokens.

### 4. Configure the database connection

Inside the `config/` folder, copy:

```text
database.local.example.php
```

and rename the copy to:

```text
database.local.php
```

Then update the values for your local MySQL installation.

Example:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '8889');
define('DB_NAME', 'goalticket_db');
define('DB_USER', 'root');
define('DB_PASS', 'your-password');
```

### 5. Configure the application

Inside the `config/` folder, copy:

```text
config.local.example.php
```

and rename the copy to:

```text
config.local.php
```

Update:

```php
define('SITE_URL', 'http://localhost:8888/goalticket');
```

and replace the example QR secret with your own long random secret.

Example:

```php
define('SITE_URL', 'http://localhost:8888/goalticket');

define(
    'QR_SECRET',
    'replace-with-your-own-long-random-secret'
);
```

### 6. Create the first administrator

Run the admin setup script from Terminal:

```bash
php scripts/create_admin.php
```

You will be asked to enter:

- Username
- Full name
- Password

The password is securely hashed before being stored in the database.

### 7. Start the application

Start Apache and MySQL using MAMP, XAMPP or another local server.

Then open the project in your browser.

Example with MAMP:

```text
http://localhost:8888/goalticket
```

Admin panel:

```text
http://localhost:8888/goalticket/admin/login.php
```

---

## 🔐 Security Features

The project includes several security practices:

- Password hashing using PHP password functions
- PDO prepared statements
- Session regeneration after authentication
- Protected administrator pages
- Local credentials excluded from Git
- Generic authentication error messages
- QR-based ticket validation
- Output escaping with `htmlspecialchars()`

Local configuration files such as:

```text
config/config.local.php
config/database.local.php
```

are excluded from Git using `.gitignore`.

This prevents local database credentials and application secrets from being published in the repository.

---

## 🗄 Database

The application uses a relational MySQL database containing five main tables:

- `users`
- `admins`
- `fixtures`
- `bookings`
- `tickets`

### Relationships

- A user can create multiple bookings.
- A fixture can have multiple bookings.
- A booking can generate one or more tickets.
- Tickets can be scanned and validated by administrators.

Foreign keys are used to maintain relationships between the tables.

The public SQL dump contains the database schema and demo fixtures only.

For security and privacy, it does not include:

- User accounts
- Administrator credentials
- Password hashes
- Booking history
- Generated QR tokens

---

## 🎫 Ticket Workflow

The typical ticket booking process is:

```text
User Registration
        ↓
User Login
        ↓
Browse Fixtures
        ↓
Select Fixture
        ↓
Book Tickets
        ↓
Booking Stored in MySQL
        ↓
QR Ticket Generated
        ↓
Ticket Displayed to User
        ↓
QR Scanned at Match Entrance
        ↓
Ticket Validated
```

This provides a complete booking and matchday ticket validation workflow.

---

## 🛡 Admin Workflow

Administrators can:

```text
Admin Login
     ↓
Dashboard
     ↓
Manage Fixtures
     ↓
View Ticketing Statistics
     ↓
Scan QR Ticket
     ↓
Validate Ticket
     ↓
Mark Ticket as Used
```

The admin area is protected using session-based authentication.

---

## 📱 Progressive Web App

GoalTicket includes Progressive Web App functionality.

The project contains:

- `manifest.json`
- `sw.js`
- `offline.html`
- PWA icons
- Service Worker registration
- PWA installation support

This allows the application to provide an app-like experience on supported devices.

---

## 📸 Screenshots

### Homepage

![Homepage](screenshots/homepage.png)

### Fixtures

![Fixtures](screenshots/fixtures.png)

### Admin Dashboard

![Admin Dashboard](screenshots/admindashboard.png)

---

## 🎓 Learning Outcomes

Through this project I developed practical experience in:

- Full-stack web application development
- PHP server-side programming
- MySQL relational database design
- Database relationships and foreign keys
- PDO and prepared SQL statements
- Authentication and session management
- Secure password storage
- QR ticket generation and validation
- Progressive Web App development
- Secure handling of local configuration
- Organising a multi-folder PHP application
- Version control with Git
- GitHub repository management
- Refactoring an existing project into a cleaner structure

---

## 🚧 Future Improvements

Planned improvements include:

- Online payment integration
- Ticket cancellation and refund workflow
- Fixture update email notifications
- User profile management
- Improved role-based admin permissions
- REST API
- Automated testing
- Cloud deployment
- Improved accessibility
- Mobile application integration
- Matchday attendance analytics

---

## 👨‍💻 Author

**Benjamin Ekay**

Computing Student in London  
Aspiring Software Engineer

LinkedIn:  
https://www.linkedin.com/in/benjamin-ekay

GitHub:  
https://github.com/ben-ekay

---

## 📄 License

This project is licensed under the MIT License.