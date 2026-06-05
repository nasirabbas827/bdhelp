# BdHelp

**BdHelp** is a PHP‑based web application that connects donors with beneficiaries, enabling transparent donation management, story sharing, and direct communication. The platform provides separate dashboards for donors, beneficiaries, and administrators, each with tailored functionality.

---

## Overview

BdHelp facilitates a streamlined workflow for charitable giving:

- **Beneficiaries** can post stories, track donation status, and communicate with donors.
- **Donors** can browse stories, make donations, view their donation history, and interact with beneficiaries.
- **Admins** manage categories, oversee all donations, and moderate messages.

The repository includes all source code, a sample database (`bdhelp.sql`), and a documentation file (`BDHelp Book.docx`) that outlines the system architecture and user guides.

---

## Features

| Area | Key Capabilities |
|------|------------------|
| **Beneficiary** | Post and update stories, view donation status, manage profile, send/receive messages, contact support |
| **Donor** | Browse stories, request details, donate, view donation history, update profile, message beneficiaries |
| **Admin** | Add/edit categories, view all donations, reply to messages, manage user sessions |
| **Common** | Secure login/logout, responsive navigation bar, centralized configuration (`config.php`) |

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 7.4+ |
| **Database** | MySQL (schema in `Database/bdhelp.sql`) |
| **Frontend** | HTML5, CSS3, Bootstrap (included via CDN) |
| **Server** | Apache / Nginx (compatible with typical LAMP/LEMP stacks) |
| **Version Control** | Git |

---

## Installation

1. **Clone the repository**  
   ```bash
   git clone https://github.com/your-username/BdHelp.git
   cd BdHelp
   ```

2. **Set up the database**  
   - Create a new MySQL database (e.g., `bdhelp`).  
   - Import the schema:  
     ```bash
     mysql -u your_user -p your_password bdhelp < Database/bdhelp.sql
     ```

3. **Configure the application**  
   - Copy the sample config files (if provided) or edit the existing ones:  
     - `Beneficiary/config.php`  
     - `Donor/config.php`  
     - `admin/config.php`  
   - Update the following constants with your environment values:  
     ```php
     define('DB_HOST', 'YOUR_DB_HOST');
     define('DB_NAME', 'bdhelp');
     define('DB_USER', 'YOUR_DB_USER');
     define('DB_PASS', 'YOUR_DB_PASSWORD');
     ```

4. **Set up the web server**  
   - Point your virtual host document root to the project folder.  
   - Ensure PHP is enabled and the `mysqli` extension is installed.

5. **Optional – Secure the site**  
   - Enable HTTPS.  
   - Adjust file permissions so that only the web server can write to necessary directories.

---

## Usage

1. **Access the application**  
   Open a browser and navigate to `http://localhost/` (or your domain).

2. **Admin login**  
   - URL: `admin/admin_login.php`  
   - Use the default credentials defined in `admin/config.php` or create a new admin via the database.

3. **Beneficiary workflow**  
   - Register / log in via `Beneficiary/beneficiary_dashboard.php`.  
   - Use the navigation bar to post a story, view donation status, or update the profile.

4. **Donor workflow**  
   - Log in via `Donor/donor_dashboard.php`.  
   - Browse stories (`Don