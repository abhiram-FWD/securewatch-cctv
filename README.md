# SecureWatch — CCTV Monitoring System

A web-based surveillance system built with
Laravel 11 MVC for crowd management,
crime prevention, and worksite monitoring.

## Features

- Real-time alerts with Pusher broadcasting
- Sound notifications for on-duty staff
- Emergency alert system
- Role-based access (Admin/Manager/Guard)
- Shift management (Morning/Night/Day)
- Camera management with live feeds
- Admin approval for new registrations
- PDF report generation
- Chart.js data visualization
- Admin messaging system
- Activity logs

## Requirements

- PHP 8.2+
- Composer
- Node.js + NPM
- Git

## Installation

1. Clone the project
   git clone https://github.com/abhiram-FWD/securewatch-cctv.git
   cd securewatch-cctv

2. Install PHP packages
   composer install

3. Install JS packages
   npm install

4. Setup environment file
   cp .env.example .env
   php artisan key:generate

5. Run migrations and seed
   php artisan migrate:fresh --seed

6. Build assets
   npm run build

7. Start server
   php artisan serve

8. Open browser
   http://localhost:8000

Note: Uses SQLite - No database setup needed!

## Login Credentials

### Admin

- Email: admin@gmail.com
- Password: admin@123

### Managers

| Email                    | Password    |
| ------------------------ | ----------- |
| crowd@securewatch.com    | password123 |
| crime@securewatch.com    | password123 |
| worksite@securewatch.com | password123 |

### Guards

| Email                         | Password    | Shift   |
| ----------------------------- | ----------- | ------- |
| guard.alpha@securewatch.com   | password123 | Morning |
| guard.beta@securewatch.com    | password123 | Morning |
| guard.charlie@securewatch.com | password123 | Night   |
| guard.delta@securewatch.com   | password123 | Night   |

## Camera Assignments

| Manager          | Cameras        |
| ---------------- | -------------- |
| Crowd Manager    | Camera 5, 6    |
| Crime Manager    | Camera 1, 2, 3 |
| Worksite Manager | Camera 4, 7, 8 |

## Tech Stack

- Backend: Laravel 11 (PHP)
- Frontend: Bootstrap 5 + Blade
- Database: SQLite
- Real-time: Pusher + Laravel Echo
- Charts: Chart.js
- PDF: DomPDF
- Roles: Spatie Laravel Permission

## Common Issues

Run these if you get errors:
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan migrate:fresh --seed
