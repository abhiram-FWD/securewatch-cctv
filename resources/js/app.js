import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Toast functionality
window.showAlertToast = function(data) {
    let bgClass = data.is_emergency ? 'bg-danger text-white' : 'bg-teal text-white';
    
    // Check if toast container exists, create if not
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    let toastId = 'toast-' + Date.now();
    let toastHtml = `
        <div id="${toastId}" class="toast align-items-center ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${data.is_emergency ? '🚨 EMERGENCY' : '🔔 New Alert'}</strong><br>
                    Camera: ${data.camera}<br>
                    Type: ${data.type}<br>
                    <small>${data.created_at || 'Just now'}</small>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    let toastElement = document.getElementById(toastId);
    let bsToast = new bootstrap.Toast(toastElement);
    bsToast.show();
    
    // Remove after hidden
    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
};

window.showResolvedToast = function(data) {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    let toastId = 'toast-' + Date.now();
    let toastHtml = `
        <div id="${toastId}" class="toast align-items-center bg-success text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    <strong>✅ Alert Resolved</strong><br>
                    Camera: ${data.camera}<br>
                    Resolved by: ${data.resolved_by}<br>
                    <small>Type: ${data.type}</small>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    let toastElement = document.getElementById(toastId);
    let bsToast = new bootstrap.Toast(toastElement);
    bsToast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
};

window.updateBellBadge = function() {
    let badge = document.getElementById('alert-bell-badge');
    if (badge) {
        let count = parseInt(badge.innerText || '0');
        badge.innerText = count + 1;
        badge.classList.remove('d-none');
    }
};

window.playAlertSound = function(isEmergency) {
    let audio = new Audio('/sounds/alert.mp3');
    if (isEmergency) {
        audio.loop = true;
    }
    audio.play().catch(e => console.log('Audio autoplay prevented'));
};

// Listen for new alerts
window.Echo.channel('alerts')
    .listen('.alert.raised', (data) => {
        window.updateBellBadge();
        window.showAlertToast(data);
        if (window.isOnDuty) {
            window.playAlertSound(data.is_emergency);
        }
    })
    .listen('.alert.resolved', (data) => {
        window.showResolvedToast(data);
    });
