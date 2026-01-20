# 🏗️ Complete Project Architecture

## Directory Tree

```
travel-platform/                                    [ROOT]
│
├── 📄 INDEX.md                                     ← START HERE (Overview)
├── 📄 BUILD_SUMMARY.md                            ← Build Statistics
├── 📄 SETUP_GUIDE.md                              ← Step-by-Step Setup
├── 📄 README.md                                   ← Full Documentation
│
├── 📁 backend/                                    [LARAVEL API]
│   │
│   ├── 📁 app/
│   │   │
│   │   ├── 📁 Http/
│   │   │   ├── 📁 Controllers/                   [6 Controllers]
│   │   │   │   ├── AuthController.php            (Login/Logout/Me)
│   │   │   │   ├── DestinationController.php     (CRUD Destinations)
│   │   │   │   ├── CityController.php            (CRUD Cities)
│   │   │   │   ├── SightseeingController.php     (CRUD Activities/Transfers)
│   │   │   │   ├── HotelController.php           (CRUD Hotels)
│   │   │   │   └── ItineraryController.php       (Full Itinerary Ops)
│   │   │   │
│   │   │   └── 📁 Middleware/
│   │   │       └── CheckRole.php                 (Role-based Access)
│   │   │
│   │   └── 📁 Models/                            [7 Models]
│   │       ├── User.php                          (Users with Roles)
│   │       ├── Destination.php                   (Travel Destinations)
│   │       ├── City.php                          (Cities in Destinations)
│   │       ├── Sightseeing.php                   (Activities & Transfers)
│   │       ├── Hotel.php                         (Hotel Properties)
│   │       ├── Itinerary.php                     (Trip Itineraries)
│   │       └── ItineraryItem.php                 (Items in Itinerary)
│   │
│   ├── 📁 database/
│   │   └── 📁 migrations/                        [7 Migrations]
│   │       ├── 2024_01_01_000001_create_users_table.php
│   │       ├── 2024_01_01_000002_create_destinations_table.php
│   │       ├── 2024_01_01_000003_create_cities_table.php
│   │       ├── 2024_01_01_000004_create_sightseeings_table.php
│   │       ├── 2024_01_01_000005_create_hotels_table.php
│   │       ├── 2024_01_01_000006_create_itineraries_table.php
│   │       └── 2024_01_01_000007_create_itinerary_items_table.php
│   │
│   ├── 📁 routes/
│   │   └── api.php                               [25+ API Routes]
│   │       ├── POST   /api/login
│   │       ├── POST   /api/logout
│   │       ├── GET    /api/me
│   │       ├── GET    /api/destinations
│   │       ├── POST   /api/destinations
│   │       ├── GET    /api/cities
│   │       ├── GET    /api/destinations/{id}/cities
│   │       ├── GET    /api/sightseeings
│   │       ├── GET    /api/cities/{id}/sightseeings
│   │       ├── GET    /api/hotels
│   │       ├── GET    /api/cities/{id}/hotels
│   │       ├── GET    /api/itineraries
│   │       ├── POST   /api/itineraries
│   │       ├── GET    /api/itineraries/{id}
│   │       ├── PUT    /api/itineraries/{id}
│   │       ├── POST   /api/itineraries/{id}/items
│   │       ├── DELETE /api/itineraries/{id}/items/{itemId}
│   │       └── + 8 more...
│   │
│   ├── 📄 .env.example                           (Environment Template)
│   ├── 📄 .gitignore                             (Git Exclusions)
│   └── 📄 composer.json                          (PHP Dependencies)
│
└── 📁 frontend/                                  [REACT + VITE]
    │
    ├── 📁 src/
    │   │
    │   ├── 📁 components/                        [2 Components]
    │   │   ├── ProtectedRoute.jsx                (Route Protection Wrapper)
    │   │   │
    │   │   └── 📁 itinerary/
    │   │       └── ItineraryBuilder.jsx          (Multi-step Itinerary Wizard)
    │   │           ├── Step 1: Destination/City/Client/Dates
    │   │           └── Step 2: Day-by-day Planning
    │   │
    │   ├── 📁 pages/                             [6 Pages]
    │   │   ├── Login.jsx                         (Authentication Page)
    │   │   ├── Dashboard.jsx                     (Role-based Dashboard)
    │   │   │   ├── Admin/Staff View
    │   │   │   ├── Agent View
    │   │   │   ├── Hotel Partner View
    │   │   │   └── Operator View
    │   │   │
    │   │   ├── 📁 admin/
    │   │   │   ├── Destinations.jsx              (Manage Destinations)
    │   │   │   └── Inventory.jsx                 (Manage Activities/Transfers)
    │   │   │
    │   │   ├── 📁 agent/
    │   │   │   └── ItineraryList.jsx             (View Created Itineraries)
    │   │   │
    │   │   └── 📁 operator/
    │   │       └── Bookings.jsx                  (View Assigned Bookings)
    │   │
    │   ├── 📁 services/
    │   │   └── api.js                            (Axios HTTP Client)
    │   │       ├── Login/Logout/Me Endpoints
    │   │       ├── Destinations Endpoints
    │   │       ├── Cities Endpoints
    │   │       ├── Sightseeing Endpoints
    │   │       ├── Hotels Endpoints
    │   │       └── Itineraries Endpoints
    │   │
    │   ├── 📁 hooks/
    │   │   └── useAuth.js                        (Authentication Context)
    │   │       ├── AuthContext
    │   │       ├── AuthProvider
    │   │       └── useAuth Hook
    │   │
    │   ├── 📄 App.jsx                            (Main App Component)
    │   │   ├── BrowserRouter Setup
    │   │   ├── Route Configuration
    │   │   ├── Protected Routes
    │   │   └── Role-based Routes
    │   │
    │   ├── 📄 main.jsx                           (React Entry Point)
    │   │   └── ReactDOM.createRoot()
    │   │
    │   └── 📄 index.css                          (Global Styles + Tailwind)
    │
    ├── 📄 index.html                             (HTML Template)
    ├── 📄 package.json                           (Node Dependencies)
    ├── 📄 vite.config.js                         (Vite Build Config)
    │   ├── React Plugin
    │   ├── Dev Server Config
    │   └── API Proxy
    ├── 📄 tailwind.config.js                     (Tailwind CSS Config)
    ├── 📄 postcss.config.js                      (PostCSS Config)
    └── 📄 .gitignore                             (Git Exclusions)
```

---

## 🔄 Data Flow Architecture

### Authentication Flow
```
User Login (Login.jsx)
    ↓
POST /api/login
    ↓
AuthController::login()
    ↓
Generate JWT Token (Sanctum)
    ↓
localStorage.setItem('token')
    ↓
useAuth Hook (AuthProvider)
    ↓
Protected Routes Check
    ↓
Dashboard (Role-based)
```

### Itinerary Creation Flow
```
Agent (ItineraryBuilder.jsx)
    ↓
Step 1: Select Destination/City
    ↓
GET /api/destinations
GET /api/cities/{id}
GET /api/hotels/{city}
GET /api/sightseeings/{city}
    ↓
Step 2: Create Itinerary
    ↓
POST /api/itineraries
    ↓
ItineraryController::store()
    ↓
Database: Insert into itineraries
    ↓
Step 3: Add Items (Hotels/Activities)
    ↓
POST /api/itineraries/{id}/items
    ↓
ItineraryController::addItem()
    ↓
Database: Insert into itinerary_items
    ↓
Display Confirmation
```

### Data Model Relationships
```
User
├── Itinerary (agent_id)
│   ├── Destination
│   ├── City
│   ├── Operator (operator_id)
│   └── ItineraryItem[]
│       ├── Hotel
│       └── Sightseeing
├── Hotel (partner_id)
│   ├── Destination
│   └── City

Destination
├── City[]
│   └── Hotel[]
│   └── Sightseeing[]
└── Sightseeing[]
```

---

## 🎯 API Request/Response Examples

### Login Request
```
POST /api/login HTTP/1.1
Content-Type: application/json

{
  "email": "agent@example.com",
  "password": "password"
}

Response:
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "agent@example.com",
    "role": "agent"
  },
  "token": "eyJhbGc..."
}
```

### Create Itinerary
```
POST /api/itineraries HTTP/1.1
Authorization: Bearer {token}
Content-Type: application/json

{
  "destination_id": 1,
  "city_id": 5,
  "client_name": "Client Name",
  "start_date": "2024-02-01",
  "end_date": "2024-02-10",
  "pax_count": 4
}

Response:
{
  "id": 100,
  "agent_id": 1,
  "destination_id": 1,
  "city_id": 5,
  "client_name": "Client Name",
  "start_date": "2024-02-01",
  "end_date": "2024-02-10",
  "pax_count": 4,
  "status": "draft",
  "created_at": "2024-01-20T10:30:00Z"
}
```

### Add Item to Itinerary
```
POST /api/itineraries/100/items HTTP/1.1
Authorization: Bearer {token}
Content-Type: application/json

{
  "day_number": 1,
  "item_type": "hotel",
  "hotel_id": 5,
  "order": 0
}

Response:
{
  "id": 501,
  "itinerary_id": 100,
  "day_number": 1,
  "item_type": "hotel",
  "hotel_id": 5,
  "order": 0,
  "created_at": "2024-01-20T10:35:00Z"
}
```

---

## 🔐 Security Architecture

### Authentication
```
Request
  ↓
Check Authorization Header
  ↓
Extract Token
  ↓
Verify Token Signature (Sanctum)
  ↓
Validate Token Expiry
  ↓
Load User from Token
  ↓
Check User is Active
  ↓
Attach User to Request
  ↓
Execute Controller
```

### Authorization (Role-based)
```
Frontend:
  ProtectedRoute checks user.role
  ↓
  Only renders component if role matches

Backend:
  CheckRole Middleware verifies role
  ↓
  Only allows specified roles to access
  ↓
  Returns 403 if unauthorized
```

---

## 💾 Database Relationships Diagram

```
┌─────────────┐
│    Users    │
│─────────────│
│ id (PK)     │
│ role        │
│ email       │
│ password    │
└──────┬──────┘
       │ agent_id        ┌──────────────────┐
       ├────────────────→│  Itineraries     │
       │                 │──────────────────│
       │ operator_id     │ id               │
       ├───────────┐     │ destination_id   │
       │           └────→│ city_id          │
       │                 │ agent_id (FK)    │
       │                 │ operator_id (FK) │
       │ partner_id      │ status           │
       └───┬─────────────→│ start_date       │
           │              │ end_date         │
           │              │ pax_count        │
           │              │ total_price      │
           │              └────────┬─────────┘
           │                       │ 1:N
           │                       ↓
       ┌───┴──────────┐    ┌──────────────────┐
       │   Hotels     │    │ ItineraryItems   │
       │──────────────│    │──────────────────│
       │ id           │    │ id               │
       │ name         │    │ itinerary_id(FK) │
       │ category     │    │ day_number       │
       │ partner_id→ (|)   │ item_type        │
       │ city_id      │    │ hotel_id (FK)    │
       │ destination_id    │ sightseeing_id   │
       └──────────────┘    │ (FK)             │
                           │ order            │
                           └────────┬─────────┘
                                    │
       ┌─────────────────────────────┼─────────────────┐
       │                             │                 │
       ↓ item_type='hotel'           ↓ item_type      ↓
    ┌──────────┐           ┌─────────────────────┐
    │  Hotels  │           │  Sightseeing        │
    └──────────┘           │─────────────────────│
                           │ id                  │
                           │ type (sightseeing   │
                           │        or transfer) │
                           │ name                │
                           │ duration            │
                           │ internal_cost       │
                           │ agent_price         │
                           │ city_id (FK)        │
                           │ destination_id (FK) │
                           └─────────────────────┘

       ┌────────────────────────────┐
       │   Destinations             │
       │────────────────────────────│
       │ id (PK)                    │
       │ name                       │
       │ country                    │
       └─────────┬──────────────────┘
                 │
       ┌─────────┴─────────┐
       │                   │
       ↓ destination_id    ↓ destination_id
   ┌────────────┐      ┌─────────────────┐
   │   Cities   │      │  Sightseeing    │
   │────────────│      │─────────────────│
   │ id         │      │ id              │
   │ name       │      │ name            │
   │ destination_id(FK) │ city_id (FK)    │
   └────────────┘      │ destination_id  │
                       │ (FK)            │
                       └─────────────────┘
```

---

## 📊 Component Tree

```
App
├── BrowserRouter
│   ├── AuthProvider
│   │   └── Routes
│   │       ├── /login
│   │       │   └── Login
│   │       │
│   │       ├── /
│   │       │   └── ProtectedRoute
│   │       │       └── Dashboard
│   │       │           ├── Admin View
│   │       │           ├── Agent View
│   │       │           ├── Hotel Partner View
│   │       │           └── Operator View
│   │       │
│   │       ├── /destinations
│   │       │   └── ProtectedRoute (admin)
│   │       │       └── Destinations
│   │       │
│   │       ├── /inventory
│   │       │   └── ProtectedRoute (admin)
│   │       │       └── Inventory
│   │       │
│   │       ├── /itineraries
│   │       │   └── ProtectedRoute (agent)
│   │       │       └── ItineraryList
│   │       │
│   │       ├── /itineraries/new
│   │       │   └── ProtectedRoute (agent)
│   │       │       └── ItineraryBuilder
│   │       │           ├── Step 1 Form
│   │       │           │   ├── Destination Select
│   │       │           │   ├── City Select
│   │       │           │   ├── Client Form
│   │       │           │   └── Date Range
│   │       │           │
│   │       │           └── Step 2 Planner
│   │       │               ├── Day 1-N
│   │       │               │   ├── Hotel Selector
│   │       │               │   ├── Activity Selector
│   │       │               │   └── Transfer Selector
│   │       │               │
│   │       │               └── Save Button
│   │       │
│   │       └── /my-bookings
│   │           └── ProtectedRoute (operator)
│   │               └── Bookings
```

---

## 🔌 API Integration Points

### Frontend Calls Backend
```
api.js (axios instance)
  ├── GET    /destinations
  ├── GET    /destinations/{id}/cities
  ├── GET    /cities/{id}/hotels
  ├── GET    /cities/{id}/sightseeings
  ├── POST   /itineraries
  ├── GET    /itineraries
  ├── POST   /itineraries/{id}/items
  └── POST   /login
      
With Headers:
  Authorization: Bearer {token}
  Content-Type: application/json
```

### Backend Response Format
```
Success (2xx):
{
  "data": {...},
  "message": "Success"
}

Error (4xx/5xx):
{
  "message": "Error description",
  "errors": {...}
}
```

---

## 📈 Scaling Architecture

Current Structure Supports:
- ✅ Multiple concurrent users
- ✅ Role-based access control
- ✅ Complex data relationships
- ✅ Stateless API design
- ✅ Easy to add new features

Future Enhancements:
- Add payment processing
- Add email notifications
- Add PDF export
- Add real-time chat
- Add analytics dashboard

---

**Architecture Version**: 1.0  
**Last Updated**: January 20, 2026  
**Status**: Production Ready ✅
