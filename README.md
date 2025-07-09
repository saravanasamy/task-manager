# Task Manager - Laravel CRUD Application

A comprehensive task management system built with Laravel that allows users to create, read, update, and delete tasks with advanced filtering, sorting, and bulk operations.

## Features

### Core Functionality
- ✅ **Create Tasks** - Add new tasks with title, description, status, and due date
- ✅ **View Tasks** - List all tasks with pagination and detailed view
- ✅ **Update Tasks** - Edit existing task information
- ✅ **Delete Tasks** - Remove tasks individually or in bulk
- ✅ **Task Status Management** - Track tasks through pending, in progress, and completed states

### Advanced Features
- 🔍 **Search & Filtering** - Search by title/description and filter by status, due date range, or overdue tasks
- 📊 **Task Statistics** - Dashboard showing total, pending, in progress, completed, and overdue tasks
- 🔄 **Bulk Operations** - Select multiple tasks for bulk status updates or deletion
- 📅 **Due Date Management** - Track overdue tasks with visual indicators
- 🎯 **Sortable Columns** - Sort tasks by title, status, due date, or creation date
- 📱 **Responsive Design** - Mobile-friendly interface using Bootstrap 5

### Technical Features
- 🏗️ **Service Layer Architecture** - Separated business logic from controllers
- ✅ **Form Request Validation** - Dedicated validation classes with custom error messages
- 🎨 **Modern UI** - Clean, professional interface with Bootstrap 5 and Bootstrap Icons
- 📄 **Pagination** - Efficient data loading with Laravel pagination
- 🔧 **Database Seeders** - Sample data for testing and development
- 🏭 **Model Factories** - Generate test data easily
- 🚀 **RESTful API** - Complete API with standardized responses and error handling
- 🔒 **Validation Service** - Centralized validation logic for consistency

## Technology Stack

- **Framework**: Laravel 11.x
- **Database**: MySQL (configurable to SQLite)
- **Frontend**: Bootstrap 5, Bootstrap Icons
- **PHP Version**: 8.2+

## Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL or SQLite
- Node.js & NPM (optional, for asset compilation)

### Step 1: Clone the Repository
```bash
git clone <repository-url>
cd task-manager
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Configuration
Edit your `.env` file with your database credentials:

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**For SQLite (simpler option):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### Step 5: Database Setup
```bash
# Create database tables
php artisan migrate

# (Optional) Seed with sample data
php artisan db:seed
```

### Step 6: Run the Application
```bash
# Start the development server
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Project Structure

### Key Files and Directories

```
app/
├── Http/
│   ├── Controllers/
│   │   └── TaskController.php      # Main controller (thin, delegates to service)
│   └── Requests/
│       ├── StoreTaskRequest.php    # Validation for creating tasks (uses validation service)
│       └── UpdateTaskRequest.php   # Validation for updating tasks (uses validation service)
├── Models/
│   └── Task.php                    # Task model with relationships and scopes
├── Providers/
│   └── TaskServiceProvider.php     # Service provider for dependency injection
└── Services/
    ├── TaskService.php             # Business logic and data operations
    └── TaskValidationService.php   # Centralized validation logic and business rules

database/
├── factories/
│   └── TaskFactory.php             # Generate test data
├── migrations/
│   └── *_create_tasks_table.php    # Database schema
└── seeders/
    ├── DatabaseSeeder.php          # Main seeder
    └── TaskSeeder.php              # Sample task data

resources/views/
├── layouts/
│   └── app.blade.php               # Main layout template
└── tasks/
    ├── index.blade.php             # Task listing with filters
    ├── create.blade.php            # Create task form
    ├── edit.blade.php              # Edit task form
    └── show.blade.php              # Task details view
```

## Database Schema

### Tasks Table
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| title | VARCHAR(255) | Task title (required) |
| description | TEXT | Task description (optional) |
| status | ENUM | pending, in_progress, completed |
| due_date | DATE | Due date (optional) |
| created_at | TIMESTAMP | Creation time |
| updated_at | TIMESTAMP | Last update time |

## Architecture Decisions

### Service Layer Pattern
- **TaskService**: Handles all business logic and data operations
- **TaskValidationService**: Dedicated service for all validation logic and business rules
- **Controllers**: Thin layer that handles HTTP requests/responses
- **Form Requests**: Lightweight validation classes that delegate to validation service
- **Model Scopes**: Reusable query logic for filtering and searching

### Benefits
- **Separation of Concerns**: Clear separation between HTTP handling, business logic, and validation
- **Testability**: Business logic and validation are easily unit testable
- **Reusability**: Service methods can be used across different controllers and contexts
- **Maintainability**: Changes to business logic and validation rules are centralized
- **Consistency**: Validation rules are consistent across web forms and API endpoints

## Usage Examples

### Creating a Task
1. Click "New Task" button
2. Fill in the form:
   - **Title**: Required field
   - **Description**: Optional detailed description
   - **Status**: Choose from pending, in progress, or completed
   - **Due Date**: Optional deadline
3. Click "Create Task"

### Filtering Tasks
- **Search**: Type in the search box to find tasks by title or description
- **Status Filter**: Select specific status or view all
- **Date Range**: Filter by due date range
- **Overdue**: Check box to show only overdue tasks

### Bulk Operations
1. Select multiple tasks using checkboxes
2. Choose action from dropdown (delete, mark as completed, etc.)
3. Click "Apply Action"

## Validation Rules

### Creating Tasks
- Title: Required, max 255 characters
- Description: Optional, max 2000 characters
- Status: Required, must be one of: pending, in_progress, completed
- Due Date: Optional, must be today or future date

### Updating Tasks
- Same as creating, except due date can be in the past

## Testing Data

The application includes seeders that create sample tasks for testing:

```bash
# Create sample data
php artisan db:seed --class=TaskSeeder
```

This creates 8 sample tasks with various statuses, due dates, and some overdue tasks.

## Troubleshooting

### Common Issues

**Database Connection Error**
- Verify database credentials in `.env`
- Ensure database server is running
- Check database exists

**Migration Issues**
- Run `php artisan migrate:fresh` to reset all tables
- Check file permissions on database file (SQLite)

**Permission Errors**
- Ensure `storage/` and `bootstrap/cache/` are writable
- Run `php artisan cache:clear` and `php artisan config:clear`

## Architecture & Code Quality

This project demonstrates:
- **Clean Architecture**: Service layer separates business logic from HTTP concerns
- **SOLID Principles**: Single responsibility, dependency injection, interface segregation
- **Laravel Best Practices**: Eloquent relationships, form requests, resource controllers
- **Security**: CSRF protection, input validation, XSS prevention
- **Performance**: Query optimization, pagination, efficient filtering

## Contributing

### Code Style
- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Add comments for complex business logic
- Keep controllers thin, move logic to services

### Adding Features
1. Create/update migrations for database changes
2. Add business logic to TaskService
3. Add validation logic to TaskValidationService
4. Update controllers to use service methods
5. Add/update views as needed
6. Update this README if needed

## Future Enhancements

- User authentication and task ownership
- Task categories/tags
- File attachments
- Email notifications for due dates
- API endpoints for mobile app
- Real-time updates with WebSockets
- Task comments and history
- Recurring tasks

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
