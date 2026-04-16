import Swal from 'sweetalert2';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import 'tippy.js/themes/light.css';
import { Notyf } from 'notyf';

// Make libraries globally available
window.Swal = Swal;
window.tippy = tippy;
window.Notyf = Notyf;

// Global Notyf instance
window.notyf = new Notyf({
    duration: 3000,
    position: { x: 'right', y: 'top' },
    types: [
        {
            type: 'success',
            background: '#10b981',
            icon: { className: 'notyf__icon--success', tagName: 'i' },
        },
        {
            type: 'error',
            background: '#ef4444',
            duration: 5000,
            icon: { className: 'notyf__icon--error', tagName: 'i' },
        },
        {
            type: 'warning',
            background: '#f59e0b',
            icon: false,
        },
        {
            type: 'info',
            background: '#3b82f6',
            icon: false,
        },
    ],
});

// SweetAlert2 preset for confirmations
window.confirmAction = (options = {}) => {
    return Swal.fire({
        title: options.title || 'Are you sure?',
        text: options.text || 'This action cannot be undone.',
        icon: options.icon || 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4a7342',
        cancelButtonColor: '#6b7280',
        confirmButtonText: options.confirmText || 'Yes, proceed',
        cancelButtonText: options.cancelText || 'Cancel',
        reverseButtons: true,
    });
};

// SweetAlert2 preset for success
window.showSuccess = (title, text) => {
    return Swal.fire({
        title: title || 'Success!',
        text: text || '',
        icon: 'success',
        confirmButtonColor: '#4a7342',
        timer: 2000,
        timerProgressBar: true,
    });
};

// Initialize tooltips on elements with data-tippy-content
document.addEventListener('DOMContentLoaded', () => {
    tippy('[data-tippy-content]', {
        theme: 'light',
        arrow: true,
        delay: [200, 0],
    });
});

// Re-initialize tooltips after Livewire updates
document.addEventListener('livewire:navigated', () => {
    tippy('[data-tippy-content]', {
        theme: 'light',
        arrow: true,
        delay: [200, 0],
    });
});

// Livewire event listeners for notifications
document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        if (data.type === 'warning' || data.type === 'info') {
            window.notyf.open({ type: data.type, message: data.message });
        } else {
            window.notyf[data.type || 'success'](data.message);
        }
    });

    Livewire.on('swal', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        Swal.fire({
            title: data.title || '',
            text: data.text || '',
            icon: data.icon || 'info',
            confirmButtonColor: '#4a7342',
        });
    });
});
