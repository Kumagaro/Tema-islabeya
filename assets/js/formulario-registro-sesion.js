(function() {
    'use strict';
    
    function initPasswordToggles() {
        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        toggleButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                
                if (!passwordInput) return;
                
                const eyeClosed = this.querySelector('.eye-closed');
                const eyeOpen = this.querySelector('.eye-open');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    if (eyeClosed) eyeClosed.style.display = 'none';
                    if (eyeOpen) eyeOpen.style.display = 'flex';
                } else {
                    passwordInput.type = 'password';
                    if (eyeClosed) eyeClosed.style.display = 'flex';
                    if (eyeOpen) eyeOpen.style.display = 'none';
                }
            });
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPasswordToggles);
    } else {
        initPasswordToggles();
    }
})();