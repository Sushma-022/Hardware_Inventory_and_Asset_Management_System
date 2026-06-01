
# Hardware Inventory and Asset Management System
A robust, full-stack web application designed for tracking, managing, and issuing IT infrastructure assets (such as CPUs, Monitors, Keyboards, Mice, Combo Sets, and custom network configurations) within an organization.

## 🚀 Features

**Smart Serial Number Generation:** Automated, sequential unique identifier generation matching the corporate structure framework: VFSTR/TD/[COMPANY]/[TYPE]/[COUNT] (e.g., VFSTR/TD/DELL/CPU/001).

**Dynamic Batch Inserts:** Supports adding hardware components in bulk with instantaneous individual row expansion in the database.

**Advanced Global Sorting:** Utilizes multi-table UNION ALL subqueries executed with timestamp-based sorting and descending serial number tie-breakers to keep newly added inventory consistently at the top.

**Performance Optimization (Pagination):** Integrated server-side pagination limiting item display to 10 records per page while preserving active range or text filter scopes.

**Cross-Table Lifecycle Synchronization:** Auto-cascading operational workflow ensures that updating asset metadata (such as shifting a device to a alternative vendor/company code) automatically syncs operational states within the Employee Issuance tables.

**Smart Forms & Event Handlers:** Client-side JavaScript calculators automatically compute hardware warranty expiration parameters against the assigned purchase dates.

**Dashboard Summary Analytics:** High-level operational reporting system evaluating total stock counts, dynamic allocations, live low-stock thresholds, and transactional monthly issuance timelines.


## 🛠️ Tech Stack

**Backend**: PHP (with MySQLi prepared statements to prevent SQL Injection attacks)

**Database**: MySQL / MariaDB (Relational design managing decoupled hardware entities alongside transactional tracking tables)

**Frontend**: HTML5, Modern CSS Grid/Flexbox, Vanilla JavaScript

**Local Server Environment**: XAMPP / WAMP

## Screenshots 


<img width="1919" height="864" alt="image" src="https://github.com/user-attachments/assets/014401d0-7bb0-45bf-959a-41e7b72402b0" />


<img width="1901" height="871" alt="image" src="https://github.com/user-attachments/assets/c6f67196-7064-472d-8740-f928751472d8" />


<img width="1919" height="805" alt="image" src="https://github.com/user-attachments/assets/6c936723-b734-4909-b140-e6b1fe7e963c" />


<img width="1896" height="870" alt="image" src="https://github.com/user-attachments/assets/4cafcecc-91bd-4c76-919f-9716f8ae7bd2" />


<img width="1901" height="868" alt="image" src="https://github.com/user-attachments/assets/4f93ea56-5c97-48a0-9cac-ca36bd9a45d4" />


<img width="1917" height="801" alt="image" src="https://github.com/user-attachments/assets/ed700a9d-217e-4949-8714-6724f8f0dbaa" />


<img width="1919" height="830" alt="image" src="https://github.com/user-attachments/assets/cb803db5-9dc5-450e-91ec-ba448e903d44" />


<img width="1910" height="844" alt="image" src="https://github.com/user-attachments/assets/1303ee97-979e-44fd-915b-9b8bcc4712c9" />




## 💻 Installation & Setup

**Clone the Repository:** git clone https://github.com/YOUR_USERNAME/YOUR_REPOSITORY_NAME.git

**Move to Web Root Directory**:Place the project folder inside your XAMPP server's directory:

**Window**s: C:\xampp\htdocs\

**Database Configuration**:

- Open your browser and navigate to http://localhost/phpmyadmin/.

- Create a new database named hardware (or your preferred database name).

- Import your .sql backup file into the newly created database.

- Ensure your db.php credential mapping aligns with your environment details:

$conn = new mysqli("localhost", "root", "", "hardware");

**Launch the Application**

Start Apache and MySQL from your XAMPP Control Panel and access the application via: **http://localhost/hardware/admin/view_equipment.php**
