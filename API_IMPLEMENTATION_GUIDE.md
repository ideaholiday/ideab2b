# Complete API Implementation Guide

## Overview

This guide covers all 25+ REST API endpoints for the IdeaB2B Travel Platform. The API is built using Laravel 10+ with Sanctum authentication.

## Quick Stats

- **Total Endpoints**: 25+
- **Base URL**: `http://localhost:8000/api`
- **Authentication**: Token-based (Laravel Sanctum)
- **Response Format**: JSON with `{success: boolean, data: object}`
- **HTTP Methods**: GET, POST, PUT, DELETE

## Authentication Flow

### 1. Register User
```bash
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "securepass",
  "password_confirmation": "securepass",
  "role": "agent",
  "phone": "+1234567890"
}

Response: 201 Created
{
  "success": true,
  "data": {
    "id": 4,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "agent",
    "phone": "+1234567890",
    "token": "abc123..."
  }
}
```

### 2. Login
```bash
POST /api/auth/login
{
  "email": "admin@ideaholiday.com",
  "password": "password"
}

Response: 200 OK
{
  "success": true,
  "data": {
    "user": {...},
    "token": "abc123...",
    "expires_in": 86400
  }
}
```

### 3. Use Token in Requests
```bash
GET /api/auth/me
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@ideaholiday.com",
    "role": "admin"
  }
}
```

### 4. Refresh Token
```bash
POST /api/auth/refresh
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": {
    "user": {...},
    "token": "new_token...",
    "expires_in": 86400
  }
}
```

### 5. Logout
```bash
POST /api/auth/logout
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## User Management

### Create User (Admin/Staff only)
```bash
POST /api/users
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "New Agent",
  "email": "agent@example.com",
  "password": "securepass",
  "role": "agent",
  "phone": "+1234567890"
}

Response: 201 Created
```

### List Users
```bash
GET /api/users?page=1&per_page=15
GET /api/users?role=agent
GET /api/users?is_active=true
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 10,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

### Get User Details
```bash
GET /api/users/{id}
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@ideaholiday.com",
    "role": "admin",
    "is_active": true
  }
}
```

### Update User
```bash
PUT /api/users/{id}
Authorization: Bearer {token}

{
  "name": "Updated Name",
  "email": "new@example.com",
  "role": "agent",
  "phone": "+9876543210"
}

Response: 200 OK
```

### Delete User (Admin only)
```bash
DELETE /api/users/{id}
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "message": "User deleted successfully"
}
```

### Get Users by Role
```bash
GET /api/users/role/agent
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": [...],
  "count": 5
}
```

### Update User Status
```bash
PUT /api/users/{id}/status
Authorization: Bearer {token}

{
  "is_active": false
}

Response: 200 OK
```

---

## Destinations

### List Destinations (Public)
```bash
GET /api/destinations

Response: 200 OK
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "India",
      "description": "Exotic destination",
      "country": "India"
    }
  ]
}
```

### Create Destination (Admin/Staff)
```bash
POST /api/destinations
Authorization: Bearer {token}

{
  "name": "France",
  "description": "European paradise",
  "country": "France"
}

Response: 201 Created
```

### Update Destination
```bash
PUT /api/destinations/{id}
Authorization: Bearer {token}

{
  "name": "Updated Name",
  "description": "New description"
}

Response: 200 OK
```

### Delete Destination
```bash
DELETE /api/destinations/{id}
Authorization: Bearer {token}

Response: 200 OK
```

---

## Cities

### List Cities (Public)
```bash
GET /api/cities
GET /api/cities?destination_id=1

Response: 200 OK
{
  "success": true,
  "data": [...]
}
```

### Create City (Admin/Staff)
```bash
POST /api/cities
Authorization: Bearer {token}

{
  "name": "Paris",
  "destination_id": 1,
  "description": "City of Light"
}

Response: 201 Created
```

### Get Cities by Destination
```bash
GET /api/destinations/{destination}/cities
Authorization: Bearer {token}

Response: 200 OK
```

---

## Sightseeings

### List Sightseeings (Public)
```bash
GET /api/sightseeings
GET /api/sightseeings?city_id=1

Response: 200 OK
```

### Create Sightseeing (Admin/Staff)
```bash
POST /api/sightseeings
Authorization: Bearer {token}

{
  "name": "Eiffel Tower",
  "city_id": 1,
  "description": "Famous landmark",
  "price": 1000,
  "duration": "2 hours",
  "rating": 4.8
}

Response: 201 Created
```

### Update Sightseeing
```bash
PUT /api/sightseeings/{id}
Authorization: Bearer {token}

{
  "price": 1200,
  "duration": "3 hours"
}

Response: 200 OK
```

### Get Sightseeings by City
```bash
GET /api/cities/{city}/sightseeings
Authorization: Bearer {token}

Response: 200 OK
```

---

## Hotels

### List Hotels (Public)
```bash
GET /api/hotels
GET /api/hotels?city_id=1

Response: 200 OK
```

### Create Hotel (Admin/Staff)
```bash
POST /api/hotels
Authorization: Bearer {token}

{
  "name": "Luxury Hotel",
  "city_id": 1,
  "description": "5-star hotel",
  "price": 5000,
  "rating": 4.8,
  "rooms": 100,
  "amenities": "WiFi, Pool, Gym"
}

Response: 201 Created
```

### Get Hotels by City
```bash
GET /api/cities/{city}/hotels
Authorization: Bearer {token}

Response: 200 OK
```

---

## Itineraries

### List Itineraries
```bash
GET /api/itineraries
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": [...],
  "pagination": {...}
}
```

### Create Itinerary
```bash
POST /api/itineraries
Authorization: Bearer {token}

{
  "name": "Paris Trip",
  "destination_id": 1,
  "city_id": 1,
  "start_date": "2026-03-15",
  "end_date": "2026-03-22",
  "guest_name": "John Doe",
  "guest_email": "john@example.com",
  "guest_phone": "+1234567890",
  "budget": 50000
}

Response: 201 Created
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Paris Trip",
    "destination_id": 1,
    ...
  }
}
```

### Get Itinerary Details
```bash
GET /api/itineraries/{id}
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Paris Trip",
    "items": [...],
    "total_price": 15000
  }
}
```

### Add Item to Itinerary
```bash
POST /api/itineraries/{id}/items
Authorization: Bearer {token}

{
  "type": "hotel",
  "item_id": 1,
  "price": 5000
}

Response: 201 Created
```

### Remove Item from Itinerary
```bash
DELETE /api/itineraries/{id}/items/{item_id}
Authorization: Bearer {token}

Response: 200 OK
```

### Assign Operator to Itinerary
```bash
POST /api/itineraries/{id}/assign-operator
Authorization: Bearer {token}

{
  "operator_id": 3
}

Response: 200 OK
```

---

## Bookings

### List Bookings
```bash
GET /api/bookings
GET /api/bookings?status=pending
GET /api/bookings?status=confirmed
Authorization: Bearer {token}

Response: 200 OK
```

### Create Booking
```bash
POST /api/bookings
Authorization: Bearer {token}

{
  "itinerary_id": 1,
  "operator_id": 3,
  "total_price": 15000,
  "notes": "VIP customer"
}

Response: 201 Created
```

### Get Booking Details
```bash
GET /api/bookings/{id}
Authorization: Bearer {token}

Response: 200 OK
```

### Update Booking
```bash
PUT /api/bookings/{id}
Authorization: Bearer {token}

{
  "status": "confirmed",
  "notes": "Updated notes"
}

Response: 200 OK
```

### Confirm Booking
```bash
PUT /api/bookings/{id}/confirm
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": {
    "id": 1,
    "status": "confirmed",
    ...
  }
}
```

### Cancel Booking
```bash
PUT /api/bookings/{id}/cancel
Authorization: Bearer {token}

Response: 200 OK
```

### Booking Statistics
```bash
GET /api/bookings/statistics
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": {
    "total_bookings": 10,
    "confirmed": 8,
    "pending": 2,
    "cancelled": 0,
    "total_revenue": 150000
  }
}
```

---

## Search

### Global Search
```bash
GET /api/search?q=paris
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": {
    "destinations": [...],
    "cities": [...],
    "hotels": [...],
    "sightseeings": [...]
  },
  "total": 10
}
```

### Search Destinations
```bash
GET /api/search/destinations?q=france
Authorization: Bearer {token}

Response: 200 OK
```

### Search Cities
```bash
GET /api/search/cities?q=paris
Authorization: Bearer {token}

Response: 200 OK
```

### Search Hotels
```bash
GET /api/search/hotels?q=luxury
Authorization: Bearer {token}

Response: 200 OK
```

### Search Sightseeings
```bash
GET /api/search/sightseeings?q=tower
Authorization: Bearer {token}

Response: 200 OK
```

---

## Statistics

### Get Overall Statistics (Admin/Staff)
```bash
GET /api/statistics
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": {
    "users": 3,
    "destinations": 2,
    "cities": 3,
    "hotels": 3,
    "sightseeings": 4,
    "itineraries": 5,
    "bookings": 10,
    "total_revenue": 150000,
    "users_by_role": {
      "admin": 1,
      "staff": 1,
      "agent": 1,
      "operator": 1,
      "hotel_partner": 0
    },
    "bookings_by_status": {
      "pending": 2,
      "confirmed": 8,
      "cancelled": 0
    }
  }
}
```

### Get Dashboard Summary
```bash
GET /api/statistics/summary
Authorization: Bearer {token}

Response: 200 OK
{
  "success": true,
  "data": {
    "total_users": 4,
    "active_users": 4,
    "total_destinations": 2,
    "total_itineraries": 5,
    "total_bookings": 10,
    "confirmed_bookings": 8,
    "pending_bookings": 2,
    "total_revenue": 150000,
    "average_booking_value": 15000
  }
}
```

### Get User Statistics
```bash
GET /api/statistics/users
Authorization: Bearer {token}

Response: 200 OK
```

### Get Booking Statistics
```bash
GET /api/statistics/bookings
Authorization: Bearer {token}

Response: 200 OK
```

### Get Destination Statistics
```bash
GET /api/statistics/destinations
Authorization: Bearer {token}

Response: 200 OK
```

---

## Error Handling

### All errors follow this format:
```json
{
  "success": false,
  "error": "Error message",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### Common HTTP Status Codes:
- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Unprocessable Entity
- `500` - Server Error

---

## Testing with cURL

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@ideaholiday.com","password":"password"}'
```

### Get Current User
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Destination
```bash
curl -X POST http://localhost:8000/api/destinations \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "Greece",
    "description": "Greek Islands",
    "country": "Greece"
  }'
```

### List Destinations
```bash
curl -X GET http://localhost:8000/api/destinations
```

### List Bookings
```bash
curl -X GET http://localhost:8000/api/bookings \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Validation Rules

### User
- `name`: required, string, max 255
- `email`: required, email, unique
- `password`: required, string, min 6, confirmed
- `role`: required, in (admin, staff, agent, operator, hotel_partner)

### Destination
- `name`: required, string
- `description`: optional, string
- `country`: optional, string

### City
- `name`: required, string
- `destination_id`: required, exists in destinations

### Hotel
- `name`: required, string
- `city_id`: required, exists in cities
- `price`: required, numeric, min 0
- `rating`: optional, numeric, 0-5

### Sightseeing
- `name`: required, string
- `city_id`: required, exists in cities
- `price`: required, numeric, min 0
- `duration`: optional, string

### Itinerary
- `name`: required, string
- `destination_id`: required
- `start_date`: required, date, before end_date
- `end_date`: required, date, after start_date

### Booking
- `itinerary_id`: required, exists
- `status`: optional, in (pending, confirmed, cancelled)
- `total_price`: required, numeric, min 0

---

## Authorization Levels

| Endpoint | Admin | Staff | Agent | Operator | Hotel Partner |
|----------|-------|-------|-------|----------|---------------|
| User Management | ✅ | ⚠️ | ❌ | ❌ | ❌ |
| Destination CRUD | ✅ | ✅ | ❌ | ❌ | ❌ |
| City CRUD | ✅ | ✅ | ❌ | ❌ | ❌ |
| Hotel CRUD | ✅ | ✅ | ❌ | ❌ | ⚠️ |
| Sightseeing CRUD | ✅ | ✅ | ❌ | ❌ | ❌ |
| Create Itinerary | ✅ | ✅ | ✅ | ❌ | ❌ |
| View Own Itinerary | ✅ | ✅ | ✅ | ❌ | ❌ |
| Create Booking | ✅ | ✅ | ✅ | ⚠️ | ❌ |
| View Booking | ✅ | ✅ | ✅ | ⚠️ | ❌ |
| Statistics | ✅ | ✅ | ❌ | ❌ | ❌ |

**✅ = Full Access | ⚠️ = Limited Access | ❌ = No Access**

---

## Test Credentials

```
Admin:
Email: admin@ideaholiday.com
Password: password

Staff:
Email: staff@ideaholiday.com
Password: password

Agent:
Email: agent1@ideaholiday.com
Password: password

Operator:
Email: operator1@ideaholiday.com
Password: password
```

---

**API Documentation Version**: 2.0  
**Last Updated**: January 2026  
**Status**: Complete & Production Ready ✅
