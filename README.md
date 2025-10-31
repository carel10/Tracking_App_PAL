# Tracking App - User Management System

Sistem manajemen user yang lengkap dengan fitur authentication, role-based access control, activity logging, dan dashboard monitoring. Dibangun dengan Laravel 11 dan NobleUI Admin Template.

## Features

### ✅ User Management
- Create, Read, Update, Delete users
- User status management (Active, Inactive, Pending)
- Role and division assignment
- User profile management

### ✅ Authentication
- User registration dengan validasi
- Login dengan email/password
- Logout dengan activity logging
- Session management

### ✅ Role & Permission System
- Role management (Super Admin, Admin, User)
- Permission management dengan kategori
- Role-Permission mapping
- Division management

### ✅ Dashboard
- Statistics cards (Total Users, Active Users, Login Today)
- Recent users list
- Recent activity logs
- User detail modal
- Permission mapping display

### ✅ Activity Logging
- Login/logout tracking
- User CRUD operations logging
- IP address dan user agent tracking
- Searchable activity history

### ✅ Modern UI
- Clean dan responsive design dengan NobleUI
- Bootstrap 5 based
- Feather Icons integration
- User-friendly forms dan tables
- Dynamic user profile display

## Technology Stack

- **Framework**: Laravel 11
- **PHP**: 8.2+
- **Database**: MySQL
- **UI Framework**: Bootstrap 5
- **Admin Template**: NobleUI
- **Icons**: Feather Icons
- **Frontend**: Blade Templates

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL
- Node.js & NPM (optional, for assets)

### Setup Instructions

1. **Clone Repository**
```bash
git clone <repository-url>
cd Tracking_App
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure Database**
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tracking_app
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run Migrations**
```bash
php artisan migrate
```

6. **Seed Database**
```bash
php artisan db:seed
```

7. **Serve Application**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## Default Credentials

Setelah seeder berhasil dijalankan, gunakan credentials berikut:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@example.com | password123 |
| Admin | admin@example.com | password123 |
| User | user@example.com | password123 |

**⚠️ PENTING**: Ubah password default di production environment!

## Database Structure

### Tables
- **users** - User accounts
  - user_id (PK), username, email, full_name
  - password_hash, role_id (FK), division_id (FK)
  - status (active/inactive/pending), last_login
  
- **roles** - User roles
  - role_id (PK), role_name, role_description, created_at
  
- **divisions** - Organizational divisions
  - division_id (PK), division_name
  
- **permissions** - System permissions
  - permission_id (PK), permission_name, permission_code
  - category, created_at
  
- **role_permissions** - Pivot table
  - role_id (FK), permission_id (FK)
  
- **user_activity_log** - Activity logs
  - log_id (PK), user_id (FK), activity
  - ip_address, user_agent, timestamp

## Project Structure

```
Tracking_App/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── UserController.php
│   │   ├── RoleController.php
│   │   ├── PermissionController.php
│   │   ├── DashboardController.php
│   │   └── ActivityLogController.php
│   └── Models/
│       ├── User.php
│       ├── Role.php
│       ├── Division.php
│       ├── Permission.php
│       └── UserActivityLog.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   └── views/
│       ├── auth/
│       ├── users/
│       ├── roles/
│       ├── permissions/
│       ├── activity/
│       ├── dashboard.blade.php
│       └── Layouts/
├── routes/
│   └── web.php
└── public/
    └── assets/
```

## Routes

### Public Routes
- `GET /` - Redirect to login
- `GET /login` - Show login form
- `POST /login` - Process login
- `GET /register` - Show registration form
- `POST /register` - Process registration

### Protected Routes (Require Auth)
- `GET /dashboard` - Dashboard
- `GET /users` - List users
- `GET /users/create` - Create user form
- `POST /users` - Store user
- `GET /users/{user}/edit` - Edit user form
- `PUT /users/{user}` - Update user
- `DELETE /users/{user}` - Delete user
- `PATCH /users/{user}/toggle` - Toggle status

- `GET /roles` - List roles
- `GET /roles/create` - Create role form
- `POST /roles` - Store role

- `GET /permissions` - List permissions
- `GET /permissions/create` - Create permission form
- `POST /permissions` - Store permission

- `GET /activity` - Activity logs

## Features Detail

### User Management
- Full CRUD operations
- Status toggle (active/inactive)
- Role and division assignment
- Activity logging untuk setiap action

### Dashboard
- Real-time statistics
- Recent users with pagination
- Recent activity logs
- User detail modal dengan edit capabilities
- Permission mapping overview

### Security
- Password hashing dengan bcrypt
- CSRF protection
- SQL injection prevention (Eloquent ORM)
- XSS prevention (Blade escaping)
- Session security
- Activity audit trail

## Development

### Code Style
Mengikuti PSR-12 coding standards dan Laravel best practices.

### Contributing
1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

This project is open-sourced software licensed under the MIT license.

## Support

Untuk support atau pertanyaan, hubungi development team.

## Changelog

### Version 2.0 (Latest)
- ✅ Perbaikan lengkap sistem user management
- ✅ Implementasi role-based access control
- ✅ Activity logging comprehensive
- ✅ Modern UI dengan NobleUI
- ✅ Database schema optimization
- ✅ Clean code implementation

### Version 1.0
- Initial release

## Authors

Development Team - Tracking App

## Acknowledgments

- Laravel Framework
- NobleUI Admin Template
- Bootstrap Team
- Feather Icons
