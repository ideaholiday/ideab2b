<?php

// IdeaB2B Travel Platform - Simple REST API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Helper functions
function success($data, $code = 200) {
    http_response_code($code);
    return json_encode(['success' => true, 'data' => $data]);
}

function error($message, $code = 400) {
    http_response_code($code);
    return json_encode(['success' => false, 'error' => $message]);
}

// Parse request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path);
$path = rtrim($path, '/');

// Simple in-memory database (using PHP sessions for now)
session_start();
if (!isset($_SESSION['data'])) {
    $_SESSION['data'] = [
        'users' => [
            ['id' => 1, 'name' => 'Admin User', 'email' => 'admin@ideaholiday.com', 'role' => 'admin', 'password' => password_hash('password', PASSWORD_DEFAULT)],
            ['id' => 2, 'name' => 'Agent 1', 'email' => 'agent1@ideaholiday.com', 'role' => 'agent', 'password' => password_hash('password', PASSWORD_DEFAULT)],
            ['id' => 3, 'name' => 'Operator 1', 'email' => 'operator1@ideaholiday.com', 'role' => 'operator', 'password' => password_hash('password', PASSWORD_DEFAULT)],
        ],
        'destinations' => [
            ['id' => 1, 'name' => 'India', 'description' => 'Exotic destination in South Asia'],
            ['id' => 2, 'name' => 'Thailand', 'description' => 'Paradise in Southeast Asia'],
        ],
        'cities' => [
            ['id' => 1, 'destination_id' => 1, 'name' => 'Delhi', 'description' => 'Capital of India'],
            ['id' => 2, 'destination_id' => 1, 'name' => 'Goa', 'description' => 'Beach paradise'],
            ['id' => 3, 'destination_id' => 2, 'name' => 'Bangkok', 'description' => 'Capital of Thailand'],
        ],
        'sightseeings' => [
            ['id' => 1, 'city_id' => 1, 'name' => 'Red Fort', 'description' => 'Historic fort', 'price' => 500],
            ['id' => 2, 'city_id' => 1, 'name' => 'India Gate', 'description' => 'Iconic monument', 'price' => 300],
            ['id' => 3, 'city_id' => 2, 'name' => 'Colva Beach', 'description' => 'Beautiful beach', 'price' => 200],
            ['id' => 4, 'city_id' => 3, 'name' => 'Grand Palace', 'description' => 'Royal palace', 'price' => 400],
        ],
        'hotels' => [
            ['id' => 1, 'city_id' => 1, 'name' => 'Taj Hotel', 'description' => '5-star luxury', 'price' => 5000],
            ['id' => 2, 'city_id' => 2, 'name' => 'Goa Beach Resort', 'description' => 'Beachfront resort', 'price' => 3000],
            ['id' => 3, 'city_id' => 3, 'name' => 'Bangkok Plaza', 'description' => 'City center hotel', 'price' => 4000],
        ],
        'itineraries' => [],
        'itinerary_items' => [],
    ];
    $_SESSION['next_ids'] = ['users' => 4, 'destinations' => 3, 'cities' => 4, 'sightseeings' => 5, 'hotels' => 4, 'itineraries' => 1, 'itinerary_items' => 1];
}

$data = &$_SESSION['data'];
$nextIds = &$_SESSION['next_ids'];

// Route handling
if ($path === '/health') {
    echo success(['status' => 'OK', 'timestamp' => date('c'), 'api' => 'IdeaB2B Travel Platform']);
    exit;
}

if ($path === '/auth/register' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $user = [
        'id' => $nextIds['users']++,
        'name' => $input['name'] ?? '',
        'email' => $input['email'] ?? '',
        'role' => $input['role'] ?? 'agent',
        'password' => password_hash($input['password'] ?? 'password', PASSWORD_DEFAULT)
    ];
    $data['users'][] = $user;
    unset($user['password']);
    echo success($user, 201);
    exit;
}

if ($path === '/auth/login' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $user = null;
    foreach ($data['users'] as $u) {
        if ($u['email'] === ($input['email'] ?? '')) {
            $user = $u;
            break;
        }
    }
    if (!$user) {
        echo error('Invalid credentials', 401);
        exit;
    }
    if (!password_verify($input['password'] ?? '', $user['password'])) {
        echo error('Invalid credentials', 401);
        exit;
    }
    unset($user['password']);
    $_SESSION['auth_token'] = bin2hex(random_bytes(32));
    $_SESSION['auth_user'] = $user;
    echo success(['token' => $_SESSION['auth_token'], 'user' => $user]);
    exit;
}

if ($path === '/auth/logout' && $method === 'POST') {
    session_destroy();
    echo success(['message' => 'Logged out']);
    exit;
}

if ($path === '/auth/me' && $method === 'GET') {
    if (!isset($_SESSION['auth_user'])) {
        echo error('Unauthorized', 401);
        exit;
    }
    echo success($_SESSION['auth_user']);
    exit;
}

if ($path === '/users' && $method === 'GET') {
    $users = array_map(function($u) {
        unset($u['password']);
        return $u;
    }, $data['users']);
    echo success(['data' => $users]);
    exit;
}

if ($path === '/users' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $user = [
        'id' => $nextIds['users']++,
        'name' => $input['name'] ?? '',
        'email' => $input['email'] ?? '',
        'role' => $input['role'] ?? 'agent'
    ];
    $data['users'][] = $user;
    echo success($user, 201);
    exit;
}

if ($path === '/destinations' && $method === 'GET') {
    echo success(['data' => $data['destinations']]);
    exit;
}

if ($path === '/destinations' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $dest = [
        'id' => $nextIds['destinations']++,
        'name' => $input['name'] ?? '',
        'description' => $input['description'] ?? ''
    ];
    $data['destinations'][] = $dest;
    echo success($dest, 201);
    exit;
}

if ($path === '/cities' && $method === 'GET') {
    echo success(['data' => $data['cities']]);
    exit;
}

if ($path === '/cities' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $city = [
        'id' => $nextIds['cities']++,
        'destination_id' => intval($input['destination_id'] ?? 1),
        'name' => $input['name'] ?? '',
        'description' => $input['description'] ?? ''
    ];
    $data['cities'][] = $city;
    echo success($city, 201);
    exit;
}

if ($path === '/sightseeings' && $method === 'GET') {
    echo success(['data' => $data['sightseeings']]);
    exit;
}

if ($path === '/sightseeings' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $sight = [
        'id' => $nextIds['sightseeings']++,
        'city_id' => intval($input['city_id'] ?? 1),
        'name' => $input['name'] ?? '',
        'description' => $input['description'] ?? '',
        'price' => intval($input['price'] ?? 0)
    ];
    $data['sightseeings'][] = $sight;
    echo success($sight, 201);
    exit;
}

if ($path === '/hotels' && $method === 'GET') {
    echo success(['data' => $data['hotels']]);
    exit;
}

if ($path === '/hotels' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $hotel = [
        'id' => $nextIds['hotels']++,
        'city_id' => intval($input['city_id'] ?? 1),
        'name' => $input['name'] ?? '',
        'description' => $input['description'] ?? '',
        'price' => intval($input['price'] ?? 0)
    ];
    $data['hotels'][] = $hotel;
    echo success($hotel, 201);
    exit;
}

if ($path === '/itineraries' && $method === 'GET') {
    $itineraries = array_map(function($it) use ($data) {
        $it['items'] = array_filter($data['itinerary_items'], function($i) use ($it) {
            return $i['itinerary_id'] === $it['id'];
        });
        return $it;
    }, $data['itineraries']);
    echo success(['data' => $itineraries]);
    exit;
}

if ($path === '/itineraries' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $itinerary = [
        'id' => $nextIds['itineraries']++,
        'user_id' => $_SESSION['auth_user']['id'] ?? 1,
        'name' => $input['name'] ?? 'New Itinerary',
        'destination_id' => intval($input['destination_id'] ?? 1),
        'start_date' => $input['start_date'] ?? date('Y-m-d'),
        'end_date' => $input['end_date'] ?? date('Y-m-d', strtotime('+5 days')),
        'status' => 'draft',
        'created_at' => date('c'),
        'items' => []
    ];
    $data['itineraries'][] = $itinerary;
    echo success($itinerary, 201);
    exit;
}

// 404 response
http_response_code(404);
echo error('Endpoint not found', 404);

