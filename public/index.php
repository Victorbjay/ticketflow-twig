<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ResolveHub\Database;
use ResolveHub\Router;

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
$twig = new \Twig\Environment($loader, [
    'cache' => false, // Disable cache for development
    'debug' => true
]);

$db = new Database();
$router = new Router();

// Home/Landing
$router->get('/', function() use ($twig, $db) {
    if ($db->isAuthenticated()) {
        header('Location: /dashboard');
        exit;
    }
    echo $twig->render('landing.twig');
});

// Login Page
$router->get('/login', function() use ($twig, $db) {
    if ($db->isAuthenticated()) {
        header('Location: /dashboard');
        exit;
    }
    echo $twig->render('auth.twig', ['isLogin' => true]);
});

// Signup Page
$router->get('/signup', function() use ($twig, $db) {
    if ($db->isAuthenticated()) {
        header('Location: /dashboard');
        exit;
    }
    echo $twig->render('auth.twig', ['isLogin' => false]);
});

// Login POST
$router->post('/login', function() use ($db) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($db->authenticateUser($email, $password)) {
        header('Location: /dashboard');
    } else {
        header('Location: /login?error=invalid');
    }
    exit;
});

// Signup POST
$router->post('/signup', function() use ($db) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if ($password !== $confirmPassword) {
        header('Location: /signup?error=mismatch');
        exit;
    }
    
    $db->createUser($email, $password);
    $db->authenticateUser($email, $password);
    header('Location: /dashboard');
    exit;
});

// Dashboard
$router->get('/dashboard', function() use ($twig, $db) {
    if (!$db->isAuthenticated()) {
        header('Location: /login');
        exit;
    }
    
    $user = $db->getUser();
    $stats = $db->getTicketStats();
    
    echo $twig->render('dashboard.twig', [
        'user' => $user,
        'stats' => $stats
    ]);
});

// Tickets
$router->get('/tickets', function() use ($twig, $db) {
    if (!$db->isAuthenticated()) {
        header('Location: /login');
        exit;
    }
    
    $user = $db->getUser();
    $tickets = $db->getTickets();
    
    echo $twig->render('tickets.twig', [
        'user' => $user,
        'tickets' => $tickets
    ]);
});

// Create Ticket POST
$router->post('/tickets/create', function() use ($db) {
    if (!$db->isAuthenticated()) {
        header('Location: /login');
        exit;
    }
    
    $db->createTicket($_POST);
    header('Location: /tickets?success=created');
    exit;
});

// Update Ticket POST
$router->post('/tickets/update', function() use ($db) {
    if (!$db->isAuthenticated()) {
        header('Location: /login');
        exit;
    }
    
    $id = $_POST['id'] ?? '';
    unset($_POST['id']);
    
    $db->updateTicket($id, $_POST);
    header('Location: /tickets?success=updated');
    exit;
});

// Delete Ticket POST
$router->post('/tickets/delete', function() use ($db) {
    if (!$db->isAuthenticated()) {
        header('Location: /login');
        exit;
    }
    
    $id = $_POST['id'] ?? '';
    $db->deleteTicket($id);
    header('Location: /tickets?success=deleted');
    exit;
});

// Logout
$router->get('/logout', function() use ($db) {
    $db->logout();
    header('Location: /');
    exit;
});

$router->run();