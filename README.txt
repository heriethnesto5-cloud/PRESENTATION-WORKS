LIBRARY MANAGEMENT SYSTEM - ADMINLTE 3
======================================

REQUIREMENTS
- XAMPP (Apache + MySQL/MariaDB)
- PHP 7.4+ (PHP 8+ recommended)
- A browser
- Internet connection for AdminLTE 3 CDN assets

INSTALLATION
1. Extract the folder "library_management_adminlte" into:
   C:\xampp\htdocs\
   The final path should be:
   C:\xampp\htdocs\library_management_adminlte\

2. Start Apache and MySQL in the XAMPP Control Panel.

3. Open phpMyAdmin:
   http://localhost/phpmyadmin

4. Select the SQL tab and import/run:
   library_management.sql

5. Open this one-time setup page:
   http://localhost/library_management_adminlte/setup_admin.php
   Create your administrator account.
   IMPORTANT: After creating the account, delete setup_admin.php from the project folder.

6. Open the system:
   http://localhost/library_management_adminlte/

DATABASE CONFIGURATION
Default XAMPP settings are configured in config/database.php:
- Host: localhost
- Database: library_management
- Username: root
- Password: (blank)

If your MySQL credentials differ, edit config/database.php.

FEATURES
- Admin login with hashed passwords and PHP sessions
- Dashboard statistics
- Book management: list, add, edit, delete
- Category management: list, add, delete
- Member management: list, add, delete
- Book issue/borrowing
- Book return
- Overdue loan indicator
- Responsive AdminLTE 3 interface

NOTES
- AdminLTE 3 and Bootstrap assets are loaded from CDNs. An internet connection is needed to load the styling.
- This is a starter system. Before public/production use, add CSRF protection, stronger deployment settings, backups, audit logs, and more comprehensive validation.
- Loans are issued and returned inside database transactions to help keep copy counts consistent.
