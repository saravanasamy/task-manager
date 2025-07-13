# Task Manager

A modern full-stack task manager application built with Laravel (API) and React, Angular, Vue (frontend options).

## Features
- Task CRUD (Create, Read, Update, Delete)
- Dashboard with metrics
- Filtering, searching, and statistics
- Responsive UI
- API-first architecture

## Requirements
- PHP >= 8.1
- Composer
- Node.js >= 16.x
- npm or yarn
- MySQL or SQLite

## Installation

### Backend (Laravel)
1. Clone the repository:
   ```sh
   git clone <your-repo-url>
   cd task-manager
   ```
2. Install dependencies:
   ```sh
   composer install
   ```
3. Copy and configure environment:
   ```sh
   cp .env.example .env
   # Edit .env for your DB settings
   php artisan key:generate
   ```
4. Run migrations:
   ```sh
   php artisan migrate
   ```
5. Start the API server:
   ```sh
   php artisan serve
   ```

### Frontend (React example)
1. Go to frontend-react:
   ```sh
   cd ../frontend-react
   npm install
   npm start
   ```

### Other Frontends
- For Angular: `cd ../Frontend-angular && npm install && ng serve`
- For Vue: `cd ../Frontend-vuejs && npm install && npm run dev`

## Usage
- Access API at `http://localhost:8000/api`
- Access React app at `http://localhost:3000`
- Access Angular app at `http://localhost:4200`
- Access Vue app at `http://localhost:5173`

## Development
- All code is in `/task-manager` (API), `/frontend-react`, `/Frontend-angular`, `/Frontend-vuejs`
- See each folder's README for more details

## Contributing
- Fork the repo, create a branch, submit a PR

## License
MIT
