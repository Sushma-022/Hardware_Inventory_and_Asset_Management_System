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
