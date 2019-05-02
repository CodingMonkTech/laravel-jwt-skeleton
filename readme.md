
# Laravel 5.8 JWT API Skeleton
Creating Laravel 5.8 API is now super easy, you don't need to setup everything from scratch. Just clone this repository and start developing api with one of best PHP framework - Laravel 5.8

[![GitHub license](https://img.shields.io/badge/license-GPLv2%2B-blue.svg)]()


### Quickstart

 - Clone this repository
 - composer install
 - php artisan migrate
 - php artisan db:seed
 - You are ready to go

### Quick Guide

Once you host your project you can use any rest client like Postman and start making requests

POST    http://laravel-jwt-api.local/auth/login

This project support multi tenant database. To enable multi tenant database application, follow below steps.

 1. open app/Http/Kernel.php
 2. Uncomment \App\Http\Middleware\DatabaseSwitcher::class, Line
 3. Hostname will be choosen as database. for example you tenant host is http://test-tenant.com then database for tenant will be **test-tenant**
 4. You can configure more things in **DatabaseSwitcher.php** Middleware.
 5. You are done.

#### Features

 1. Multy tenancy support
 2. Out of the box JWT
 3. Role based authentication
 4. Login, Registration, Forget Password, Email Verification Implemented.
 5. User and Role creation APIs ready.
 6. Pretty Routes (Available at /routes)

#### Versioning

This project uses uses [Semantic Versioning](http://semver.org/)

#### Contributors

 - Hiren Kavad


#### License


    Copyright (C) 2019 Coding Monk

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
