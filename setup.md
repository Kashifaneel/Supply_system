# Supply Management System Setup Guide

## Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL Database
- XAMPP/WAMP/LAMP stack

## Installation Steps

### 1. Install PHP Dependencies
```bash
composer install
composer require barryvdh/laravel-dompdf spatie/laravel-permission
```

### 2. Install Node Dependencies
```bash
npm install
```

### 3. Environment Setup
- Copy `.env.example` to `.env` (if not already done)
- Update database configuration in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=supply_management
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Create Database
Create a MySQL database named `supply_management`

### 6. Run Migrations
```bash
php artisan migrate
```

### 7. Seed Database
```bash
php artisan db:seed
```

### 8. Create Storage Link
```bash
php artisan storage:link
```

### 9. Build Frontend Assets
```bash
npm run build
# or for development
npm run dev
```

### 10. Start Development Server
```bash
php artisan serve
```

## Default Login Credentials

### Admin User
- Email: admin@example.com
- Password: password

### Regular User
- Email: user@example.com
- Password: password

## Features

### Admin Features
- View all purchase orders and supplies
- Confirm/reject payments
- Manage users (if user management is implemented)

### User Features
- Create purchase orders
- Manage supplies
- Submit payments
- View own data only

## File Structure

### Backend
- `app/Models/` - Eloquent models
- `app/Http/Controllers/` - Controllers
- `app/Http/Requests/` - Form validation
- `app/Policies/` - Authorization policies
- `database/migrations/` - Database migrations
- `resources/views/pdf/` - PDF templates

### Frontend
- `resources/js/pages/` - Vue.js pages
- `resources/js/components/` - Reusable components
- `resources/js/layouts/` - Layout components

## Troubleshooting

### Common Issues

1. **Composer timeout**: Increase timeout or install packages individually
2. **Permission denied**: Check file permissions on storage and bootstrap/cache
3. **Database connection**: Verify MySQL is running and credentials are correct
4. **PDF generation**: Ensure DomPDF is properly installed

### Commands for Debugging
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check routes
php artisan route:list

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Set proper file permissions
7. Configure web server (Apache/Nginx)

## Support

For issues or questions, check:
1. Laravel documentation: https://laravel.com/docs
2. Vue.js documentation: https://vuejs.org/
3. Inertia.js documentation: https://inertiajs.com/
