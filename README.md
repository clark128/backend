LARAVEL 10

Github Repository Backend : https://github.com/ClarkGenesis/Backend

🧰 1. XAMPP and Database Setup
🔹 Install XAMPP
If you haven’t already, download and install XAMPP from the official site: https://www.apachefriends.org

🔹 Place Project Files
Go to your XAMPP installation directory (usually C:\xampp)

Open the htdocs folder

Place both your frontend and backend folders inside the htdocs directory

🔹 Start XAMPP Services
Open the XAMPP Control Panel

Click Start for both:

✅ Apache

✅ MySQL

This will allow you to run PHP apps and access phpMyAdmin.

🔹 Create the Database
Open your browser and go to 👉 http://localhost/phpmyadmin
Click "New" and create a new database named:
	database_premio
	Then, import the database file:
	Click on the database_premio database in the left sidebar.
	Go to the Import tab.
	Click Choose File and select the database_premio.sql file from your computer.
	Click Go to upload and import the SQL file into the database_premio database.

✅ This will populate your database with the necessary tables and data.

⚠️ Note: If your Laravel .env file uses another database name (e.g., database_premio), make sure it matches what you enter here.

🛠️ 3. Backend Setup (Laravel)
🔹 Install Composer
Make sure Composer is installed. If not, download it from:
👉 https://getcomposer.org

🔹 Open Terminal in Backend Folder
Go to your backend folder:
C:\xampp\htdocs\backend

Open a terminal inside this folder
🔹 Install Backend Dependencies
Run this command to install Laravel and PHP dependencies:
composer install

🔹 Set Up Environment File
Copy the example environment file:
cp .env.example .env

🔹 Generate Application Key
Run this to generate the Laravel app key:
php artisan key:generate

🔹 Configure Database Connection
Open the .env file in a text editor

Update the database section:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database_premio
DB_USERNAME=root
DB_PASSWORD=

📝 Make sure DB_DATABASE matches the one you created in phpMyAdmin!


Run Database Migrations
This will create the necessary tables:
php artisan migrate

🔹 Seed the Database (Optional but Recommended)
If your project includes seeders, run:
php artisan db:seed

🔹 Run the Backend Application
Start the Laravel development server:
php artisan serve

This will start the backend at:
👉 http://127.0.0.1:8000


<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
