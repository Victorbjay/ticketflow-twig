<?php

namespace ResolveHub;

class Database {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    // User Management
    public function createUser($email, $password) {
        $_SESSION['ticketapp_user'] = [
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        return true;
    }
    
    public function authenticateUser($email, $password) {
        if (!isset($_SESSION['ticketapp_user'])) {
            return false;
        }
        
        $user = $_SESSION['ticketapp_user'];
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            $_SESSION['ticketapp_session'] = [
                'token' => bin2hex(random_bytes(32)),
                'email' => $email,
                'timestamp' => time()
            ];
            return true;
        }
        return false;
    }
    
    public function isAuthenticated() {
        return isset($_SESSION['ticketapp_session']);
    }
    
    public function getUser() {
        return $_SESSION['ticketapp_session'] ?? null;
    }
    
    public function logout() {
        unset($_SESSION['ticketapp_session']);
        return true;
    }
    
    // Ticket Management
    public function getTickets() {
        return $_SESSION['ticketapp_tickets'] ?? [];
    }
    
    public function createTicket($data) {
        if (!isset($_SESSION['ticketapp_tickets'])) {
            $_SESSION['ticketapp_tickets'] = [];
        }
        
        $ticket = [
            'id' => uniqid(),
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'status' => $data['status'],
            'priority' => $data['priority'] ?? 'medium',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $_SESSION['ticketapp_tickets'][] = $ticket;
        return $ticket;
    }
    
    public function updateTicket($id, $data) {
        if (!isset($_SESSION['ticketapp_tickets'])) {
            return false;
        }
        
        foreach ($_SESSION['ticketapp_tickets'] as $key => $ticket) {
            if ($ticket['id'] === $id) {
                $_SESSION['ticketapp_tickets'][$key] = array_merge($ticket, $data, [
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                return $_SESSION['ticketapp_tickets'][$key];
            }
        }
        return false;
    }
    
    public function deleteTicket($id) {
        if (!isset($_SESSION['ticketapp_tickets'])) {
            return false;
        }
        
        foreach ($_SESSION['ticketapp_tickets'] as $key => $ticket) {
            if ($ticket['id'] === $id) {
                unset($_SESSION['ticketapp_tickets'][$key]);
                $_SESSION['ticketapp_tickets'] = array_values($_SESSION['ticketapp_tickets']);
                return true;
            }
        }
        return false;
    }
    
    public function getTicketStats() {
        $tickets = $this->getTickets();
        return [
            'total' => count($tickets),
            'open' => count(array_filter($tickets, fn($t) => $t['status'] === 'open')),
            'in_progress' => count(array_filter($tickets, fn($t) => $t['status'] === 'in_progress')),
            'closed' => count(array_filter($tickets, fn($t) => $t['status'] === 'closed'))
        ];
    }
}