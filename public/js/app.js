// ============================================
// TOAST AUTO-HIDE
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const toast = document.getElementById('toast');
    if (toast) {
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});

// ============================================
// MODAL FUNCTIONS
// ============================================
function openModal(mode, ticket = null) {
    const modal = document.getElementById('ticketModal');
    const form = document.getElementById('ticketForm');
    const modalTitle = document.getElementById('modalTitle');
    
    if (mode === 'create') {
        modalTitle.textContent = 'Create New Ticket';
        form.action = '/tickets/create';
        form.reset();
        document.getElementById('ticketId').value = '';
    } else if (mode === 'edit' && ticket) {
        modalTitle.textContent = 'Edit Ticket';
        form.action = '/tickets/update';
        document.getElementById('title').value = ticket.title;
        document.getElementById('description').value = ticket.description || '';
        document.getElementById('status').value = ticket.status;
        document.getElementById('priority').value = ticket.priority || 'medium';
        document.getElementById('ticketId').value = ticket.id;
    }
    
    modal.classList.add('active');
}

function closeModal() {
    const modal = document.getElementById('ticketModal');
    modal.classList.remove('active');
}

function openDeleteModal(ticket) {
    const modal = document.getElementById('deleteModal');
    const message = document.getElementById('deleteMessage');
    const ticketId = document.getElementById('deleteTicketId');
    
    message.textContent = `Are you sure you want to delete "${ticket.title}"? This action cannot be undone.`;
    ticketId.value = ticket.id;
    
    modal.classList.add('active');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('active');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

// ============================================
// FORM VALIDATION
// ============================================
const authForm = document.getElementById('authForm');
if (authForm) {
    authForm.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password');
        
        if (password.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long');
            return;
        }
        
        if (confirmPassword && password !== confirmPassword.value) {
            e.preventDefault();
            alert('Passwords do not match');
            return;
        }
    });
}

const ticketForm = document.getElementById('ticketForm');
if (ticketForm) {
    ticketForm.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const status = document.getElementById('status').value;
        
        if (!title) {
            e.preventDefault();
            alert('Title is required');
            return;
        }
        
        if (title.length > 100) {
            e.preventDefault();
            alert('Title must be less than 100 characters');
            return;
        }
        
        if (!['open', 'in_progress', 'closed'].includes(status)) {
            e.preventDefault();
            alert('Invalid status selected');
            return;
        }
    });
}