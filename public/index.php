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
    $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

    if ($db->authenticateUser($email, $password)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Login successful']);
        } else {
            header('Location: /dashboard');
        }
    } else {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        } else {
            header('Location: /login?error=invalid');
        }
    }
    exit;
});

// Signup POST
$router->post('/signup', function() use ($db) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

    if ($password !== $confirmPassword) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        } else {
            header('Location: /signup?error=mismatch');
        }
        exit;
    }

    $db->createUser($email, $password);
    $db->authenticateUser($email, $password);
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Account created']);
    } else {
        header('Location: /dashboard');
    }
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
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Your session has expired — please log in again.']);
        } else {
            header('Location: /login');
        }
        exit;
    }
    $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    $ticket = $db->createTicket($_POST);
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Ticket created', 'ticket' => $ticket]);
    } else {
        header('Location: /tickets?success=created');
    }
    exit;
});

// Update Ticket POST
$router->post('/tickets/update', function() use ($db) {
    if (!$db->isAuthenticated()) {
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Your session has expired — please log in again.']);
        } else {
            header('Location: /login');
        }
        exit;
    }
    $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    $id = $_POST['id'] ?? '';
    unset($_POST['id']);

    $result = $db->updateTicket($id, $_POST);
    if ($isAjax) {
        if ($result) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Ticket updated', 'ticket' => $result]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update ticket']);
        }
    } else {
        header('Location: /tickets?success=updated');
    }
    exit;
});

// Delete Ticket POST
$router->post('/tickets/delete', function() use ($db) {
    if (!$db->isAuthenticated()) {
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Your session has expired — please log in again.']);
        } else {
            header('Location: /login');
        }
        exit;
    }
    $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    $id = $_POST['id'] ?? '';
    $ok = $db->deleteTicket($id);
    if ($isAjax) {
        if ($ok) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Ticket deleted']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to delete ticket']);
        }
    } else {
        header('Location: /tickets?success=deleted');
    }
    exit;
});

// Logout
$router->get('/logout', function() use ($db) {
    $db->logout();
    header('Location: /');
    exit;
});

$router->run();