# HouseBoat Booking System

A comprehensive online booking system for houseboats in Kerala, India.

## Features

- User registration and authentication
- Boat browsing and searching
- Booking management with date selection
- Payment processing
- Admin panel for boat and booking management
- Revenue reporting and analytics

## Technologies Used

- PHP (Backend)
- MySQL (Database)
- HTML/CSS/JavaScript (Frontend)
- Bootstrap 5 (UI Framework)
- Chart.js (Data Visualization)

## Installation

1. Clone the repository
2. Set up a web server with PHP support (WAMP/XAMPP/MAMP)
3. Import the database schema from `houseboat_db.sql`
4. Configure database connection in `backend/inc/db_connect.php`
5. Access the application through your web browser

## Project Structure

```
HouseBoatBooking/
├── admin/              # Admin panel
├── backend/            # Backend logic and database connections
├── css/                # Stylesheets
├── frontend/           # User-facing pages
├── img/                # Images
├── js/                 # JavaScript files
├── uploads/            # User uploaded files
├── houseboat_db.sql    # Database schema
└── index.php           # Main entry point
```

## Key Components

### User Features
- Browse and search available houseboats
- View boat details and images
- Make bookings with date selection
- View booking history
- Process payments

### Admin Features
- Manage boats (add, edit, delete)
- Manage users
- View and manage bookings
- Generate revenue reports
- System configuration

## Recent Improvements

- Fixed booking date display issues
- Enhanced admin reporting with multiple report types
- Improved data validation and error handling
- Better user interface and experience

## Database Schema

The database includes tables for:
- Users (customers and administrators)
- Boats (houseboats with details and images)
- Bookings (reservations with dates and payment info)
- Payments (transaction details)

## Configuration

Update the database connection settings in `backend/inc/db_connect.php`:
```php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'houseboat_db';
```

## License

This project is proprietary and intended for educational purposes.