// ============================================
// TOAST NOTIFICATION SYSTEM
// ============================================
const Toast = {
    container: null,
    
    init() {
        if (!this.container) {
            this.container = document.getElementById('toastContainer');
            if (!this.container) {
                this.container = document.createElement('div');
                this.container.id = 'toastContainer';
                this.container.className = 'toast-container';
                document.body.appendChild(this.container);
            }
        }
    },

    show(message, type = 'info', duration = 5000) {
        this.init();
        
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const content = document.createElement('div');
        content.className = 'toast-content';
        
        const icon = document.createElement('span');
        icon.className = 'toast-icon';
        icon.textContent = this.getIcon(type);
        
        const text = document.createElement('span');
        text.textContent = message;
        
        const closeBtn = document.createElement('button');
        closeBtn.className = 'toast-close';
        closeBtn.innerHTML = '×';
        closeBtn.onclick = () => this.dismiss(toast);
        
        content.appendChild(icon);
        content.appendChild(text);
        content.appendChild(closeBtn);
        toast.appendChild(content);
        
        this.container.appendChild(toast);
        
        if (duration > 0) {
            setTimeout(() => this.dismiss(toast), duration);
        }
        
        return toast;
    },
    
    success(message, duration) {
        return this.show(message, 'success', duration);
    },
    
    error(message, duration) {
        return this.show(message, 'error', duration);
    },
    
    warning(message, duration) {
        return this.show(message, 'warning', duration);
    },
    
    info(message, duration) {
        return this.show(message, 'info', duration);
    },
    
    dismiss(toast) {
        toast.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => toast.remove(), 300);
    },
    
    getIcon(type) {
        switch (type) {
            case 'success': return '✓';
            case 'error': return '⚠️';
            case 'warning': return '⚠';
            default: return 'ℹ';
        }
    }
};

// Migrate any server-rendered inline toasts (id="toast") to the new Toast system
document.addEventListener('DOMContentLoaded', () => {
    try {
        const serverToast = document.getElementById('toast');
        if (serverToast) {
            // Determine type from classes
            let type = 'info';
            if (serverToast.classList.contains('toast-success')) type = 'success';
            else if (serverToast.classList.contains('toast-error')) type = 'error';
            else if (serverToast.classList.contains('toast-warning')) type = 'warning';

            // Extract visible text
            const text = serverToast.textContent.trim();

            // Show with our dynamic system and remove the old node
            if (text) Toast.show(text, type, 4000);
            serverToast.remove();
        }
    } catch (err) {
        // Non-fatal: keep console info for debugging
        console.error('Toast migration error:', err);
    }
});

// ============================================
// AUTH FORM VALIDATION
// ============================================
function validateAuthForm(form) {
    const email = form.email.value.trim();
    const password = form.password.value;
    const confirmPassword = form.confirm_password?.value;
    
    if (!email) {
        Toast.error('Email is required');
        return false;
    }
    
    if (!email.includes('@') || !email.includes('.')) {
        Toast.error('Please enter a valid email address');
        return false;
    }
    
    if (!password || password.length < 6) {
        Toast.error('Password must be at least 6 characters');
        return false;
    }
    
    if (confirmPassword !== undefined && password !== confirmPassword) {
        Toast.error('Passwords do not match');
        return false;
    }
    
    return true;
}

// ============================================
// MODAL FUNCTIONS
// ============================================
function validateTicketForm(form) {
    const title = form.title.value.trim();
    const status = form.status.value;
    
    if (!title) {
        Toast.error('Title is required');
        return false;
    }
    
    if (!['open', 'in_progress', 'closed'].includes(status)) {
        Toast.error('Invalid status value');
        return false;
    }
    
    if (form.description.value.length > 500) {
        Toast.error('Description must be less than 500 characters');
        return false;
    }
    
    return true;
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    if (!validateTicketForm(e.target)) {
        return;
    }
    
    const formData = new FormData(e.target);
    const method = e.target.method.toUpperCase();
    
    fetch(e.target.action, {
        method: method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        redirect: 'follow'
    })
    .then(async (response) => {
        // If server responded with JSON, parse it and handle success/error accordingly.
        const contentType = response.headers.get('content-type') || '';

        if (contentType.includes('application/json')) {
            const data = await response.json();
            return { json: true, data };
        }

        // If response is a redirect or a non-JSON 200/201, assume the server processed the form
        // (this happens in this simulated PHP app which often does redirects) — reload to show changes.
        if (response.redirected || response.status === 200 || response.status === 201) {
            closeModal();
            location.reload();
            // Return a sentinel so downstream .then doesn't try to access data
            return { json: false };
        }

        // Otherwise try to read text for debugging and treat as error
        const text = await response.text();
        throw new Error('Unexpected server response: ' + text);
    })
    .then((result) => {
        if (!result) return;
        if (result.json) {
            const data = result.data;
            if (data.success) {
                Toast.success(data.message || 'Operation successful');
                closeModal();
                location.reload();
            } else {
                Toast.error(data.message || 'Operation failed');
            }
        }
    })
    .catch((error) => {
        // If we already reloaded above, suppress the error handling
        if (String(error) === 'Error: Redirected' || String(error) === 'non-json-success') return;
        Toast.error('An error occurred. Please try again.');
        console.error('Form submission error:', error);
    });
}

function openModal(mode, ticket = null) {
    const modal = document.getElementById('ticketModal');
    const form = document.getElementById('ticketForm');
    const modalTitle = document.getElementById('modalTitle');
    
    // Set up form submission handler
    form.removeEventListener('submit', handleFormSubmit);
    form.addEventListener('submit', handleFormSubmit);
    
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

// Note: form validation is handled via the validateAuthForm and validateTicketForm helpers
// and the ticket form submission uses AJAX via handleFormSubmit when opened through the modal.