# Task Management System

[![Backend PHP](https://img.shields.io/badge/backend-php-%23777BB4?logo=php&logoColor=white)](https://www.php.net/) [![Framework CodeIgniter](https://img.shields.io/badge/framework-codeigniter-%23EF4223?logo=codeigniter&logoColor=white)](https://codeigniter.com/) [![Frontend JavaScript](https://img.shields.io/badge/frontend-javascript-%23F7DF1E?logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript) [![Database MySQL](https://img.shields.io/badge/database-mysql-%234479A1?logo=mysql&logoColor=white)](https://www.mysql.com/) [![Styling Tailwind CSS](https://img.shields.io/badge/styling-tailwind%20css-%2338B2AC?logo=tailwind-css&logoColor=white)](https://tailwindcss.com/) [![UI jQuery](https://img.shields.io/badge/ui-jquery-%230769AD?logo=jquery&logoColor=white)](https://jquery.com/) [![Icons Font Awesome](https://img.shields.io/badge/icons-font%20awesome-%23528DD7?logo=font-awesome&logoColor=white)](https://fontawesome.com/) [![Session PHP Session](https://img.shields.io/badge/session-php%20session-%23777BB4?logo=php&logoColor=white)](https://www.php.net/manual/en/book.session.php) [![License MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A modern task management system built with CodeIgniter 4 and Tailwind CSS. Features user authentication, category-based task organization, real-time updates, and responsive design.

## Prerequisites

Before you begin, ensure you have the following installed:
- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Git

## Installation Steps

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/task-management-system.git
cd task-management-system
```

2. **Install dependencies**
```bash
composer install
```

3. **Configure environment**
```bash
# Copy the example env file
cp .env.example .env

# Generate application key (if needed)
php spark key:generate
```

4. **Configure database**
Edit `.env` file and update database settings:
```env
database.default.hostname = localhost
database.default.database = your_database_name
database.default.username = your_username
database.default.password = your_password
database.default.DBDriver = MySQLi
database.default.port = 3306
```

5. **Run migrations**
```bash
# Create database tables
php spark migrate

# Seed default categories
php spark db:seed DefaultCategories
```

6. **Set permissions** (Linux/Unix)
```bash
chmod -R 777 writable/
```

7. **Start development server**
```bash
php spark serve
```

The application will be available at `http://localhost:8080`

## Test Account

Use these credentials to test the application:
```
Email: test@example.com
Username: TestUser
Password: password123
```

## Features

- User Authentication
  - Register with email and password
  - Login with email/username
  - Secure session management

- Task Management
  - Create, edit, and delete tasks
  - Organize tasks by categories
  - Set task deadlines
  - Move tasks between states (Pending/In Progress/Completed)

- Categories
  - Create custom categories
  - Edit and delete categories
  - Color-coded category system

- Search and Filter
  - Search tasks by title/description
  - Filter by status
  - Filter by category
  - Combined filters support

## Database Structure

The system uses three main tables:

1. **users**
   - Store user credentials and information
   - Handles authentication

2. **tasks**
   - Store task details
   - Fields: title, description, category, deadline, status, user_id

3. **categories**
   - Store task categories
   - Fields: name, color_class, user_id

## Development

### File Structure
```
task-management-system/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   ├── Filters/
│   └── Database/
├── public/
│   ├── assets/
│   └── js/
└── writable/
```

### Key Files
- `app/Config/Routes.php` - Application routes
- `app/Controllers/` - Application controllers
- `app/Models/` - Database models
- `app/Views/` - View templates
- `app/Database/Migrations/` - Database migrations

## Troubleshooting

1. **Permission Issues**
```bash
chmod -R 777 writable/
```

2. **Database Connection**
- Verify database credentials in `.env`
- Ensure MySQL service is running

3. **Composer Issues**
```bash
composer clear-cache
composer update
```

## Security

- CSRF protection enabled
- Secure session management
- Password hashing
- XSS protection
- Input validation

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

---

For additional help or questions, please open an issue in the repository.
