<?php

/**
 * IdeaB2B Travel Platform - Complete REST API
 * All endpoints for travel itinerary management system
 * Version: 2.0 (Enhanced with comprehensive features)
 */

// ============================================================================
// HEADERS & CORS
// ============================================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

function success($data, $code = 200) {
    http_response_code($code);
    return json_encode(['success' => true, 'data' => $data], JSON_PRETTY_PRINT);
}

function error($message, $code = 400) {
    http_response_code($code);
    return json_encode(['success' => false, 'error' => $message], JSON_PRETTY_PRINT);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_required($data, $fields) {
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            return "Field '$field' is required";
        }
    }
    return null;
}

function get_auth_user() {
    return $_SESSION['auth_user'] ?? null;
}

function require_auth() {
    if (!isset($_SESSION['auth_user'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
}

function require_role(...$roles) {
    require_auth();
    if (!in_array($_SESSION['auth_user']['role'], $roles)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Forbidden']);
        exit;
    }
}

function get_request_data() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

// ============================================================================
// REQUEST PARSING
// ============================================================================

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path);
$path = rtrim($path, '/');
$input = get_request_data();

// ============================================================================
// SESSION INITIALIZATION & DATA
// ============================================================================

session_start();

if (!isset($_SESSION['data'])) {
    $_SESSION['data'] = [
        // Users
        'users' => [
            ['id' => 1, 'name' => 'Admin User', 'email' => 'admin@ideaholiday.com', 'role' => 'admin', 'phone' => '+1234567890', 'password' => password_hash('password', PASSWORD_DEFAULT), 'created_at' => date('c')],
            ['id' => 2, 'name' => 'Agent 1', 'email' => 'agent1@ideaholiday.com', 'role' => 'agent', 'phone' => '+1234567891', 'password' => password_hash('password', PASSWORD_DEFAULT), 'created_at' => date('c')],
            ['id' => 3, 'name' => 'Operator 1', 'email' => 'operator1@ideaholiday.com', 'role' => 'operator', 'phone' => '+1234567892', 'password' => password_hash('password', PASSWORD_DEFAULT), 'created_at' => date('c')],
        ],
        
        // Destinations
        'destinations' => [
            ['id' => 1, 'name' => 'India', 'description' => 'Exotic destination in South Asia', 'country' => 'India', 'created_at' => date('c')],
            ['id' => 2, 'name' => 'Thailand', 'description' => 'Paradise in Southeast Asia', 'country' => 'Thailand', 'created_at' => date('c')],
        ],
        
        // Cities
        'cities' => [
            ['id' => 1, 'destination_id' => 1, 'name' => 'Delhi', 'description' => 'Capital of India', 'created_at' => date('c')],
            ['id' => 2, 'destination_id' => 1, 'name' => 'Goa', 'description' => 'Beach paradise', 'created_at' => date('c')],
            ['id' => 3, 'destination_id' => 2, 'name' => 'Bangkok', 'description' => 'Capital of Thailand', 'created_at' => date('c')],
        ],
        
        // Sightseeings
        'sightseeings' => [
            ['id' => 1, 'city_id' => 1, 'name' => 'Red Fort', 'description' => 'Historic fort in Delhi', 'price' => 500, 'duration' => '2 hours', 'created_at' => date('c')],
            ['id' => 2, 'city_id' => 1, 'name' => 'India Gate', 'description' => 'Iconic monument', 'price' => 300, 'duration' => '1 hour', 'created_at' => date('c')],
            ['id' => 3, 'city_id' => 2, 'name' => 'Colva Beach', 'description' => 'Beautiful beach', 'price' => 200, 'duration' => '3 hours', 'created_at' => date('c')],
            ['id' => 4, 'city_id' => 3, 'name' => 'Grand Palace', 'description' => 'Royal palace', 'price' => 400, 'duration' => '2.5 hours', 'created_at' => date('c')],
        ],
        
        // Hotels
        'hotels' => [
            ['id' => 1, 'city_id' => 1, 'name' => 'Taj Hotel', 'description' => '5-star luxury hotel', 'price' => 5000, 'rating' => 4.8, 'created_at' => date('c')],
            ['id' => 2, 'city_id' => 2, 'name' => 'Goa Beach Resort', 'description' => 'Beachfront resort', 'price' => 3000, 'rating' => 4.5, 'created_at' => date('c')],
            ['id' => 3, 'city_id' => 3, 'name' => 'Bangkok Plaza', 'description' => 'City center hotel', 'price' => 4000, 'rating' => 4.6, 'created_at' => date('c')],
        ],
        
        // Itineraries
        'itineraries' => [],
        
        // Itinerary Items
        'itinerary_items' => [],
        
        // Bookings
        'bookings' => [],
    ];
    
    $_SESSION['next_ids'] = [
        'users' => 4,
        'destinations' => 3,
        'cities' => 4,
        'sightseeings' => 5,
        'hotels' => 4,
        'itineraries' => 1,
        'itinerary_items' => 1,
        'bookings' => 1,
    ];
}

$data = &$_SESSION['data'];
$nextIds = &$_SESSION['next_ids'];

// ============================================================================
// ROUTE HANDLING - HEALTH & INFO
// ============================================================================

if ($path === '/health' && $method === 'GET') {
    echo success([
        'status' => 'OK',
        'timestamp' => date('c'),
        'version' => '2.0',
        'api' => 'IdeaB2B Travel Platform API',
        'endpoints' => 25,
    ]);
    exit;
}

if ($path === '/info' && $method === 'GET') {
    echo success([
        'name' => 'IdeaB2B Travel Platform',
        'version' => '1.0.0',
        'description' => 'Complete REST API for travel itinerary management',
        'endpoints' => 25,
        'auth' => 'Token-based (JWT)',
        'database' => 'Session-based storage',
    ]);
    exit;
}

// ============================================================================
// ROUTE HANDLING - AUTHENTICATION
// ============================================================================

if ($path === '/auth/login' && $method === 'POST') {
    $err = validate_required($input, ['email', 'password']);
    if ($err) {
        echo error($err, 400);
        exit;
    }
    
    $user = null;
    foreach ($data['users'] as $u) {
        if ($u['email'] === $input['email']) {
            $user = $u;
            break;
        }
    }
    
    if (!$user || !password_verify($input['password'], $user['password'])) {
        echo error('Invalid credentials', 401);
        exit;
    }
    
    unset($user['password']);
    $_SESSION['auth_token'] = bin2hex(random_bytes(32));
    $_SESSION['auth_user'] = $user;
    
    echo success([
        'token' => $_SESSION['auth_token'],
        'user' => $user,
        'expires_in' => 86400,
    ]);
    exit;
}

if ($path === '/auth/register' && $method === 'POST') {
    $err = validate_required($input, ['name', 'email', 'password', 'role']);
    if ($err) {
        echo error($err, 400);
        exit;
    }
    
    if (!validate_email($input['email'])) {
        echo error('Invalid email format', 400);
        exit;
    }
    
    foreach ($data['users'] as $u) {
        if ($u['email'] === $input['email']) {
            echo error('Email already exists', 409);
            exit;
        }
    }
    
    $user = [
        'id' => $nextIds['users']++,
        'name' => $input['name'],
        'email' => $input['email'],
        'role' => $input['role'] ?? 'agent',
        'phone' => $input['phone'] ?? '',
        'password' => password_hash($input['password'], PASSWORD_DEFAULT),
        'created_at' => date('c'),
    ];
    
    $data['users'][] = $user;
    unset($user['password']);
    
    echo success($user, 201);
    exit;
}

if ($path === '/auth/logout' && $method === 'POST') {
    require_auth();
    session_destroy();
    echo success(['message' => 'Logged out successfully']);
    exit;
}

if ($path === '/auth/me' && $method === 'GET') {
    require_auth();
    echo success(get_auth_user());
    exit;
}

if ($path === '/auth/refresh' && $method === 'POST') {
    require_auth();
    $_SESSION['auth_token'] = bin2hex(random_bytes(32));
    echo success([
        'token' => $_SESSION['auth_token'],
        'user' => get_auth_user(),
    ]);
    exit;
}

// ============================================================================
// ROUTE HANDLING - USERS
// ============================================================================

if ($path === '/users' && $method === 'GET') {
    require_auth();
    $users = array_map(function($u) {
        unset($u['password']);
        return $u;
    }, $data['users']);
    echo success(['data' => $users, 'count' => count($users)]);
    exit;
}

if ($path === '/users' && $method === 'POST') {
    require_role('admin', 'staff');
    $err = validate_required($input, ['name', 'email', 'role']);
    if ($err) {
        echo error($err, 400);
        exit;
    }
    
    $user = [
        'id' => $nextIds['users']++,
        'name' => $input['name'],
        'email' => $input['email'],
        'role' => $input['role'],
        'phone' => $input['phone'] ?? '',
        'password' => password_hash($input['password'] ?? 'password', PASSWORD_DEFAULT),
        'created_at' => date('c'),
    ];
    
    $data['users'][] = $user;
    unset($user['password']);
    
    echo success($user, 201);
    exit;
}

if (preg_match('/^\/users\/(\d+)$/', $path, $matches) && $method === 'GET') {
    require_auth();
    $id = (int)$matches[1];
    foreach ($data['users'] as $user) {
        if ($user['id'] === $id) {
            unset($user['password']);
            echo success($user);
            exit;
        }
    }
    echo error('User not found', 404);
    exit;
}

if (preg_match('/^\/users\/(\d+)$/', $path, $matches) && $method === 'PUT') {
    require_role('admin', 'staff');
    $id = (int)$matches[1];
    foreach ($data['users'] as &$user) {
        if ($user['id'] === $id) {
            if (isset($input['name'])) $user['name'] = $input['name'];
            if (isset($input['email'])) $user['email'] = $input['email'];
            if (isset($input['role'])) $user['role'] = $input['role'];
            if (isset($input['phone'])) $user['phone'] = $input['phone'];
            if (isset($input['password'])) $user['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
            
            unset($user['password']);
            echo success($user);
            exit;
        }
    }
    echo error('User not found', 404);
    exit;
}

if (preg_match('/^\/users\/(\d+)$/', $path, $matches) && $method === 'DELETE') {
    require_role('admin');
    $id = (int)$matches[1];
    foreach ($data['users'] as $key => $user) {
        if ($user['id'] === $id) {
            unset($data['users'][$key]);
            $data['users'] = array_values($data['users']);
            echo success(['message' => 'User deleted']);
            exit;
        }
    }
    echo error('User not found', 404);
    exit;
}

// ============================================================================
// ROUTE HANDLING - DESTINATIONS
// ============================================================================

if ($path === '/destinations' && $method === 'GET') {
    echo success(['data' => $data['destinations'], 'count' => count($data['destinations'])]);
    exit;
}

if ($path === '/destinations' && $method === 'POST') {
    require_role('admin', 'staff');
    $err = validate_required($input, ['name']);
    if ($err) {
        echo error($err, 400);
        exit;
    }
    
    $dest = [
        'id' => $nextIds['destinations']++,
        'name' => $input['name'],
        'description' => $input['description'] ?? '',
        'country' => $input['country'] ?? '',
        'created_at' => date('c'),
    ];
    
    $data['destinations'][] = $dest;
    echo success($dest, 201);
    exit;
}

if (preg_match('/^\/destinations\/(\d+)$/', $path, $matches) && $method === 'GET') {
    $id = (int)$matches[1];
    foreach ($data['destinations'] as $dest) {
        if ($dest['id'] === $id) {
            echo success($dest);
            exit;
        }
    }
    echo error('Destination not found', 404);
    exit;
}

if (preg_match('/^\/destinations\/(\d+)$/', $path, $matches) && $method === 'PUT') {
    require_role('admin', 'staff');
    $id = (int)$matches[1];
    foreach ($data['destinations'] as &$dest) {
        if ($dest['id'] === $id) {
            if (isset($input['name'])) $dest['name'] = $input['name'];
            if (isset($input['description'])) $dest['description'] = $input['description'];
            if (isset($input['country'])) $dest['country'] = $input['country'];
            
            echo success($dest);
            exit;
        }
    }
    echo error('Destination not found', 404);
    exit;
}

if (preg_match('/^\/destinations\/(\d+)$/', $path, $matches) && $method === 'DELETE') {
    require_role('admin', 'staff');
    $id = (int)$matches[1];
    foreach ($data['destinations'] as $key => $dest) {
        if ($dest['id'] === $id) {
            unset($data['destinations'][$key]);
            $data['destinations'] = array_values($data['destinations']);
            echo success(['message' => 'Destination deleted']);
            exit;
        }
    }
    echo error('Destination not found', 404);
    exit;
}

// ============================================================================
// ROUTE HANDLING - CITIES
// ============================================================================

if ($path === '/cities' && $method === 'GET') {
    $cities = $data['cities'];
    if (isset($_GET['destination_id'])) {
        $dest_id = (int)$_GET['destination_id'];
        $cities = array_filter($cities, fn($c) => $c['destination_id'] === $dest_id);
    }
    echo success(['data' => array_values($cities), 'count' => count($cities)]);
    exit;
}

if ($path === '/cities' && $method === 'POST') {
    require_role('admin', 'staff');
    $err = validate_required($input, ['name', 'destination_id']);
    if ($err) {
        echo error($err, 400);
        exit;
    }
    
    $city = [
        'id' => $nextIds['cities']++,
        'destination_id' => (int)$input['destination_id'],
        'name' => $input['name'],
        'description' => $input['description'] ?? '',
        'created_at' => date('c'),
    ];
    
    $data['cities'][] = $city;
    echo success($city, 201);
    exit;
}

if (preg_match('/^\/cities\/(\d+)$/', $path, $matches) && $method === 'GET') {
    $id = (int)$matches[1];
    foreach ($data['cities'] as $city) {
        if ($city['id'] === $id) {
            echo success($city);
            exit;
        }
    }
    echo error('City not found', 404);
    exit;
}

if (preg_match('/^\/cities\/(\d+)$/', $path, $matches) && $method === 'PUT') {
    require_role('admin', 'staff');
    $id = (int)$matches[1];
    foreach ($data['cities'] as &$city) {
        if ($city['id'] === $id) {
            if (isset($input['name'])) $city['name'] = $input['name'];
            if (isset($input['description'])) $city['description'] = $input['description'];
            if (isset($input['destination_id'])) $city['destination_id'] = (int)$input['destination_id'];
            
            echo success($city);
            exit;
        }
    }
    echo error('City not found', 404);
    exit;
}

if (preg_match('/^\/cities\/(\d+)$/', $path, $matches) && $method === 'DELETE') {
    require_role('admin', 'staff');
    $id = (int)$matches[1];
    foreach ($data['cities'] as $key => $city) {
        if ($city['id'] === $id) {
            unset($data['cities'][$key]);
            $data['cities'] = array_values($data['cities']);
            echo success(['message' => 'City deleted']);
            exit;
        }
    }
    echo error('City not found', 404);
    exit;
}

// ============================================================================
// ROUTE HANDLING - SIGHTSEEINGS
// ============================================================================

if ($path === '/sightseeings' && $method === 'GET') {
    $sights = $data['sightseeings'];
    if (isset($_GET['city_id'])) {
        $city_id = (int)$_GET['city_id'];
        $sights = array_filter($sights, fn($s) => $s['city_id'] === $city_id);
    }
    echo success(['data' => array_values($sights), 'count' => count($sights)]);
    exit;
}

if ($path === '/sightseeings' && $method === 'POST') {
    require_role('admin', 'staff');
    $err = validate_required($input, ['name', 'city_id', 'price']);
    if ($err) {
        echo error($err, 400);
        exit;
    }
    
    $sight = [
        'id' => $nextIds['sightseeings']++,
        'city_id' => (int)$input['city_id'],
        'name' => $input['name'],
        'description' => $input['description'] ?? '',
        'price' => (int)$input['price'],
        'duration' => $input['duration'] ?? '1 hour',
        'created_at' => date('c'),
    ];
    
    $data['sightseeings'][] = $sight;
    echo success($sight, 201);
    exit;
}

if (preg_match('/^\/sightseeings\/(\d+)$/', $path, $matches) && $method === 'GET') {
    $id = (int)$matches[1];
    foreach ($data['sightseeings'] as $sight) {
        if ($sight['id'] === $id) {
            echo success($sight);
            exit;
        }
    }
    echo error('Sightseeing not found', 404);
    exit;
}

if (preg_match('/^\/sightseeings\/(\d+)$/', $path, $matches) && $method === 'PUT') {
    require_role('admin', 'staff');
    $id = (int)$matches[1];
    foreach ($data['sightseeings'] as &$sight) {
        if ($sight['id'] === $id) {
            if (isset($input['name'])) $sight['name'] = $input['name'];
            if (isset($input['description'])) $sight['description'] = $input['description'];
            if (isset($input['price'])) $sight['price'] = (int)$input['price'];
            if (isset($input['duration'])) $sight['duration'] = $input['duration'];
            
            echo success($sight);
            exit;
        }
    }
    echo error('Sightseeing not found', 404);
    exit;
}

if (preg_match('/^\/sightseeings\/(\d+)$/', $path, $matches) && $method === 'DELETE') {
    require_role('admin', 'staff');
    $id = (int)$matches[1];
    foreach ($data['sightseeings'] as $key => $sight) {
        if ($sight['id'] === $id) {
            unset($data['sightseeings'][$key]);
            $data['sightseeings'] = array_values($data['sightseeings']);
            echo success(['message' => 'Sightseeing deleted']);
            exit;
        }
    }
    echo error('Sightseeing not found', 404);
    exit;
}

// ============================================================================
// ROUTE HANDLING - HOTELS
// ============================================================================

if ($path === '/hotels' && $method === 'GET') {
    $hotels = $data['hotels'];
    if (isset($_GET['city_id'])) {
        $city_id = (int)$_GET['city_id'];
        $hotels = array_filter($hotels, fn($h) => $h['city_id'] === $city_id);
    }
    echo success(['data' => array_values($hotels), 'count' => count($hotels)]);
    exit;
}

if ($path === '/hotels' && $method === 'POST') {
    require_role('admin', 'staff');
    $err = validate_required($input, ['name', 'city_id', 'price']);
    if ($err) {
        echo error($err, 400);
        exit;
    }
    
    $hotel = [
        'id' => $nextIds['hotels']++,
        'city_id' => (int)$input['city_id'],
        'name' => $input['name'],
        'description' => $input['description'] ?? '',
        'price' => (int)$input['price'],
        'rating' => (float)($input['rating'] ?? 4.0),
        'created_at' => date('c'),
    ];
    
    $data['hotels'][] = $hotel;
    echo success($hotel, 201);
    exit;
}

if (preg_match('/^\/hotels\/(\d+)$/', $path, $matches) && $method === 'GET') {
    $id = (int)$matches[1];
    foreach ($data['hotels'] as $hotel) {
        if ($hotel['id'] === $id) {
            echo success($hotel);
            exit;
        }
    }
    echo error('Hotel not found', 404);
    exit;
}

if (preg_match('/^\/hotels\/(\d+)$/', $path, $matches) && $method === 'PUT') {
    require_role('admin', 'staff');
    $id = (int)$matches[1];
    foreach ($data['hotels'] as &$hotel) {
        if ($hotel['id'] === $id) {
            if (isset($input['name'])) $hotel['name'] = $input['name'];
            if (isset($input['description'])) $hotel['description'] = $input['description'];
            if (isset($input['price'])) $hotel['price'] = (int)$input['price'];
            if (isset($input['rating'])) $hotel['rating'] = (float)$input['rating'];
            
            echo success($hotel);
            exit;
        }
    }
    echo error('Hotel not found', 404);
    exit;
}

if (preg_match('/^\/hotels\/(\d+)$/', $path, $matches) && $method === 'DELETE') {
    require_role('admin', 'staff');
    $id = (int)$matches[1];
    foreach ($data['hotels'] as $key => $hotel) {
        if ($hotel['id'] === $id) {
            unset($data['hotels'][$key]);
            $data['hotels'] = array_values($data['hotels']);
            echo success(['message' => 'Hotel deleted']);
            exit;
        }
    }
    echo error('Hotel not found', 404);
    exit;
}

// ============================================================================
// ROUTE HANDLING - ITINERARIES
// ============================================================================

if ($path === '/itineraries' && $method === 'GET') {
    require_auth();
    $itineraries = $data['itineraries'];
    
    $user = get_auth_user();
    if ($user['role'] === 'agent') {
        $itineraries = array_filter($itineraries, fn($it) => $it['created_by'] === $user['id']);
    }
    
    echo success([
        'data' => array_values($itineraries),
        'count' => count($itineraries),
    ]);
    exit;
}

if ($path === '/itineraries' && $method === 'POST') {
    require_role('agent', 'admin', 'staff');
    $err = validate_required($input, ['name', 'destination_id', 'start_date', 'end_date']);
    if ($err) {
        echo error($err, 400);
        exit;
    }
    
    $itinerary = [
        'id' => $nextIds['itineraries']++,
        'created_by' => get_auth_user()['id'],
        'name' => $input['name'],
        'destination_id' => (int)$input['destination_id'],
        'city_id' => (int)($input['city_id'] ?? 0),
        'start_date' => $input['start_date'],
        'end_date' => $input['end_date'],
        'guest_name' => $input['guest_name'] ?? '',
        'guest_email' => $input['guest_email'] ?? '',
        'guest_phone' => $input['guest_phone'] ?? '',
        'budget' => (int)($input['budget'] ?? 0),
        'status' => 'draft',
        'items' => [],
        'total_price' => 0,
        'created_at' => date('c'),
        'updated_at' => date('c'),
    ];
    
    $data['itineraries'][] = $itinerary;
    echo success($itinerary, 201);
    exit;
}

if (preg_match('/^\/itineraries\/(\d+)$/', $path, $matches) && $method === 'GET') {
    require_auth();
    $id = (int)$matches[1];
    foreach ($data['itineraries'] as $it) {
        if ($it['id'] === $id) {
            $it['items'] = array_filter($data['itinerary_items'], fn($item) => $item['itinerary_id'] === $id);
            $it['total_price'] = array_sum(array_map(fn($item) => $item['price'], $it['items']));
            echo success($it);
            exit;
        }
    }
    echo error('Itinerary not found', 404);
    exit;
}

if (preg_match('/^\/itineraries\/(\d+)$/', $path, $matches) && $method === 'PUT') {
    require_auth();
    $id = (int)$matches[1];
    foreach ($data['itineraries'] as &$it) {
        if ($it['id'] === $id) {
            if (isset($input['name'])) $it['name'] = $input['name'];
            if (isset($input['status'])) $it['status'] = $input['status'];
            if (isset($input['start_date'])) $it['start_date'] = $input['start_date'];
            if (isset($input['end_date'])) $it['end_date'] = $input['end_date'];
            $it['updated_at'] = date('c');
            
            echo success($it);
            exit;
        }
    }
    echo error('Itinerary not found', 404);
    exit;
}

if (preg_match('/^\/itineraries\/(\d+)$/', $path, $matches) && $method === 'DELETE') {
    require_auth();
    $id = (int)$matches[1];
    foreach ($data['itineraries'] as $key => $it) {
        if ($it['id'] === $id) {
            unset($data['itineraries'][$key]);
            $data['itineraries'] = array_values($data['itineraries']);
            echo success(['message' => 'Itinerary deleted']);
            exit;
        }
    }
    echo error('Itinerary not found', 404);
    exit;
}

// ============================================================================
// ROUTE HANDLING - ITINERARY ITEMS
// ============================================================================

if (preg_match('/^\/itineraries\/(\d+)\/items$/', $path, $matches) && $method === 'POST') {
    require_auth();
    $itinerary_id = (int)$matches[1];
    
    $err = validate_required($input, ['type', 'item_id', 'price']);
    if ($err) {
        echo error($err, 400);
        exit;
    }
    
    $item = [
        'id' => $nextIds['itinerary_items']++,
        'itinerary_id' => $itinerary_id,
        'type' => $input['type'],
        'item_id' => (int)$input['item_id'],
        'price' => (int)$input['price'],
        'created_at' => date('c'),
    ];
    
    $data['itinerary_items'][] = $item;
    echo success($item, 201);
    exit;
}

if (preg_match('/^\/itineraries\/(\d+)\/items\/(\d+)$/', $path, $matches) && $method === 'DELETE') {
    require_auth();
    $itinerary_id = (int)$matches[1];
    $item_id = (int)$matches[2];
    
    foreach ($data['itinerary_items'] as $key => $item) {
        if ($item['itinerary_id'] === $itinerary_id && $item['id'] === $item_id) {
            unset($data['itinerary_items'][$key]);
            $data['itinerary_items'] = array_values($data['itinerary_items']);
            echo success(['message' => 'Item removed']);
            exit;
        }
    }
    echo error('Item not found', 404);
    exit;
}

// ============================================================================
// ROUTE HANDLING - BOOKINGS
// ============================================================================

if ($path === '/bookings' && $method === 'GET') {
    require_auth();
    echo success([
        'data' => $data['bookings'],
        'count' => count($data['bookings']),
    ]);
    exit;
}

if ($path === '/bookings' && $method === 'POST') {
    require_role('agent', 'admin', 'staff');
    $err = validate_required($input, ['itinerary_id']);
    if ($err) {
        echo error($err, 400);
        exit;
    }
    
    $booking = [
        'id' => $nextIds['bookings']++,
        'itinerary_id' => (int)$input['itinerary_id'],
        'created_by' => get_auth_user()['id'],
        'status' => 'pending',
        'total_price' => (int)($input['total_price'] ?? 0),
        'notes' => $input['notes'] ?? '',
        'created_at' => date('c'),
        'updated_at' => date('c'),
    ];
    
    $data['bookings'][] = $booking;
    echo success($booking, 201);
    exit;
}

if (preg_match('/^\/bookings\/(\d+)$/', $path, $matches) && $method === 'PUT') {
    require_auth();
    $id = (int)$matches[1];
    
    foreach ($data['bookings'] as &$booking) {
        if ($booking['id'] === $id) {
            if (isset($input['status'])) $booking['status'] = $input['status'];
            if (isset($input['notes'])) $booking['notes'] = $input['notes'];
            $booking['updated_at'] = date('c');
            
            echo success($booking);
            exit;
        }
    }
    echo error('Booking not found', 404);
    exit;
}

if (preg_match('/^\/bookings\/(\d+)$/', $path, $matches) && $method === 'GET') {
    require_auth();
    $id = (int)$matches[1];
    foreach ($data['bookings'] as $booking) {
        if ($booking['id'] === $id) {
            echo success($booking);
            exit;
        }
    }
    echo error('Booking not found', 404);
    exit;
}

// ============================================================================
// ROUTE HANDLING - SEARCH & FILTER
// ============================================================================

if ($path === '/search' && $method === 'GET') {
    $q = $_GET['q'] ?? '';
    if (strlen($q) < 2) {
        echo error('Search query too short', 400);
        exit;
    }
    
    $results = [
        'destinations' => array_filter($data['destinations'], fn($d) => stripos($d['name'], $q) !== false),
        'cities' => array_filter($data['cities'], fn($c) => stripos($c['name'], $q) !== false),
        'hotels' => array_filter($data['hotels'], fn($h) => stripos($h['name'], $q) !== false),
        'sightseeings' => array_filter($data['sightseeings'], fn($s) => stripos($s['name'], $q) !== false),
    ];
    
    echo success($results);
    exit;
}

// ============================================================================
// ROUTE HANDLING - STATISTICS
// ============================================================================

if ($path === '/statistics' && $method === 'GET') {
    require_role('admin', 'staff');
    
    $stats = [
        'users' => count($data['users']),
        'destinations' => count($data['destinations']),
        'cities' => count($data['cities']),
        'hotels' => count($data['hotels']),
        'sightseeings' => count($data['sightseeings']),
        'itineraries' => count($data['itineraries']),
        'bookings' => count($data['bookings']),
        'total_revenue' => array_sum(array_map(fn($b) => $b['total_price'], $data['bookings'])),
    ];
    
    echo success($stats);
    exit;
}

// ============================================================================
// 404 NOT FOUND
// ============================================================================

http_response_code(404);
echo error('Endpoint not found', 404);
