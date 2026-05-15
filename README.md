# 🔐 SecureWatch — CCTV Monitoring System

> A real-time web-based surveillance platform built with Laravel 11 MVC for crowd management, crime prevention, and worksite monitoring.

---

## 📌 About The Project

SecureWatch is a centralized CCTV monitoring dashboard that empowers security teams with intelligent tools for:

- 👥 **Crowd Management** — Monitor public gathering areas
- 🚨 **Crime Prevention** — Detect and respond to suspicious activity  
- 🏗️ **Worksite Monitoring** — Track worker safety and presence

Built as part of an MVC Programming (Laravel) academic project.

---

## ✨ Features

| Feature | Description |
|---------|-------------|
| 🎥 Live Camera Feeds | Watch all CCTV cameras in real-time grid |
| 🔔 Sound Alerts | Audio notifications for on-duty staff |
| 🚨 Emergency System | Override shift restrictions for critical alerts |
| 👑 Role-Based Access | Admin / Manager / Guard dashboards |
| ⏰ Shift Management | Morning / Night / Day shift system |
| ✅ Admin Approvals | New registrations require admin approval |
| 💬 Messaging System | Admin sends instructions to staff |
| 📊 Reports & Charts | Visual analytics with Chart.js |
| 📄 PDF Reports | Download daily activity reports |
| 📋 Activity Logs | Track every action in the system |
| 🔴 Real-time Broadcasting | Pusher + Laravel Echo integration |

---

## 🛠️ Tech Stack

### Backend
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat&logo=php&logoColor=white)

### Frontend
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=flat&logo=javascript&logoColor=black)
![Chart.js](https://img.shields.io/badge/Chart.js-4-FF6384?style=flat&logo=chartdotjs&logoColor=white)

### Database
![SQLite](https://img.shields.io/badge/SQLite-003B57?style=flat&logo=sqlite&logoColor=white)

### Real-time
![Pusher](https://img.shields.io/badge/Pusher-300D4F?style=flat&logo=pusher&logoColor=white)

### Other
![DomPDF](https://img.shields.io/badge/DomPDF-PDF%20Generation-red)
![Spatie](https://img.shields.io/badge/Spatie-Permissions-green)
![Vite](https://img.shields.io/badge/Vite-7-646CFF?style=flat&logo=vite&logoColor=white)

---

## 👥 User Roles

### 👑 Admin
- Full system control
- Manage users and cameras
- Approve / Reject registrations
- View all alerts and logs
- Generate and download PDF reports
- Send messages to all staff

### 👷 Manager
- View assigned cameras only
- Raise and resolve alerts in his area
- Send instructions to guards
- Generate area-specific reports
- Day shift (9AM - 6PM)

### 🛡️ Guard
- View all cameras live
- Raise regular and emergency alerts
- Resolve alerts with resolution notes
- Morning shift (6AM - 6PM)
- Night shift (6PM - 6AM)

---

## 🚀 Installation

### Prerequisites
Make sure you have installed:
- PHP 8.2+
- Composer
- Node.js + NPM
- Git

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/abhiram-FWD/securewatch-cctv.git
cd securewatch-cctv

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies
npm install

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Setup database with test data
php artisan migrate:fresh --seed

# 6. Build frontend assets
npm run build

# 7. Start the server
php artisan serve
```

### 8. Open in browser