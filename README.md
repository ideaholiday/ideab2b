# Travel Platform - B2B Itinerary Management System

Complete full-stack travel itinerary platform for B2B operations.

## Backend Setup (Laravel)

### Prerequisites
- PHP 8.0+
- Composer
- MySQL 5.7+

### Installation

1. **Install dependencies:**
```bash
cd backend
composer install
```

2. **Configure environment:**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Update .env with database credentials:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=travel_platform
DB_USERNAME=root
DB_PASSWORD=your_password
```

4. **Run migrations:**
```bash
php artisan migrate
```

5. **Start development server:**
```bash
php artisan serve
```

Server runs on `http://localhost:8000`

## Frontend Setup (React + Vite)

### Prerequisites
- Node.js 16+
- npm or yarn

### Installation

1. **Install dependencies:**
```bash
cd frontend
npm install
```

2. **Start development server:**
```bash
npm run dev
```

Frontend runs on `http://localhost:5173`

## Features

### User Roles
- **Admin**: Full system access, manage all data
- **Staff**: Support admin with operational tasks
- **Agent**: Create itineraries, manage customer trips
- **Hotel Partner**: Manage hotel properties
- **Operator**: Execute booked itineraries

### Core Features
- Multi-role authentication with JWT tokens
- Destination & city management
- Hotel and sightseeing inventory
- Interactive itinerary builder
- Day-by-day trip planning
- Item ordering and management
- Booking status tracking

## API Endpoints

### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/me` - Get current user

### Destinations
- `GET /api/destinations` - List destinations
- `POST /api/destinations` - Create destination
- `GET /api/destinations/{id}` - Get destination
- `PUT /api/destinations/{id}` - Update destination
- `DELETE /api/destinations/{id}` - Delete destination

### Cities
- `GET /api/cities` - List cities
- `GET /api/destinations/{id}/cities` - Get cities by destination
- `POST /api/cities` - Create city

### Sightseeing & Transfers
- `GET /api/sightseeings` - List all sightseeing
- `GET /api/cities/{id}/sightseeings` - Get by city
- `POST /api/sightseeings` - Create sightseeing

### Hotels
- `GET /api/hotels` - List hotels
- `GET /api/cities/{id}/hotels` - Get by city
- `POST /api/hotels` - Create hotel

### Itineraries
- `GET /api/itineraries` - List itineraries
- `POST /api/itineraries` - Create itinerary
- `GET /api/itineraries/{id}` - Get itinerary
- `PUT /api/itineraries/{id}` - Update itinerary
- `POST /api/itineraries/{id}/items` - Add item to itinerary
- `DELETE /api/itineraries/{id}/items/{itemId}` - Remove item
- `POST /api/itineraries/{id}/assign-operator` - Assign operator

## Project Structure

```
travel-platform/
├── backend/                          # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/        # API Controllers
│   │   ├── Models/                  # Eloquent Models
│   │   └── Http/Middleware/         # Custom Middleware
│   ├── database/
│   │   └── migrations/              # Database Migrations
│   ├── routes/
│   │   └── api.php                  # API Routes
│   ├── .env.example                 # Environment template
│   └── composer.json                # PHP dependencies
│
└── frontend/                        # React + Vite
    ├── src/
    │   ├── components/              # React Components
    │   ├── pages/                   # Page Components
    │   ├── services/                # API Services
    │   ├── hooks/                   # Custom Hooks
    │   ├── App.jsx                  # Main App Component
    │   └── main.jsx                 # Entry Point
    ├── index.html                   # HTML Template
    ├── package.json                 # Node Dependencies
    ├── vite.config.js              # Vite Configuration
    └── tailwind.config.js           # Tailwind CSS Config
```

## Database Schema

### Users
- id, name, email, password, role, is_active, company_name, phone

### Destinations
- id, name, country, description, is_active

### Cities
- id, destination_id, name, description, is_active

### Sightseeings
- id, destination_id, city_id, type (sightseeing/transfer), name, description, duration, internal_cost, agent_price, is_active

### Hotels
- id, destination_id, city_id, partner_id, name, category (3star/4star/5star), room_type, notes, is_active

### Itineraries
- id, agent_id, destination_id, city_id, client_name, client_email, client_phone, start_date, end_date, pax_count, total_price, status, operator_id

### ItineraryItems
- id, itinerary_id, day_number, item_type (hotel/sightseeing/transfer), hotel_id, sightseeing_id, notes, order

## Authentication Flow

1. User logs in with email/password
2. Backend validates and returns JWT token
3. Frontend stores token in localStorage
4. Token included in all API requests
5. Backend validates token and role permissions

## Development Workflow

1. **Backend Development:**
```bash
cd backend
php artisan serve
```

2. **Frontend Development:**
```bash
cd frontend
npm run dev
```

3. **Build for Production:**

Backend:
```bash
# Configure .env for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Frontend:
```bash
npm run build
# Output in dist/ directory
```

## Testing

### Backend
```bash
cd backend
php artisan test
```

### Frontend
```bash
cd frontend
npm test
```

## Environment Configuration

### Backend (.env)
- Database credentials
- App key and URL
- Sanctum configuration
- Mail settings (if needed)

### Frontend
- API base URL (configured in src/services/api.js)
- Default: http://localhost:8000/api

## Troubleshooting

### CORS Issues
- Add frontend URL to CORS whitelist in backend
- Configure proper headers in Laravel

### Database Connection
- Verify MySQL is running
- Check .env database credentials
- Run `php artisan migrate` again if needed

### Frontend Build Issues
- Delete node_modules and package-lock.json
- Run `npm install` again
- Clear vite cache

## Contributing

1. Create feature branch
2. Make changes
3. Test thoroughly
4. Submit pull request

## License

MIT License - See LICENSE file for details
