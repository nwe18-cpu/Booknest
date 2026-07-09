document.addEventListener('DOMContentLoaded', function() {
    const middlePage = document.getElementById('middle-page');
    const rightPage = document.querySelector('.right-page');
    const toRegisterBtn = document.getElementById('to-register');
    const toLoginBtn = document.getElementById('to-login');
    const toLoginLink = document.getElementById('to-login-link'); // New link in Register form

    if (middlePage) {
        if (toRegisterBtn) {
            toRegisterBtn.addEventListener('click', function(e) {
                e.preventDefault();
                middlePage.classList.add('flipped');
                // Mobile responsive handling
                if (window.innerWidth <= 768 && rightPage) {
                    rightPage.classList.add('active-mobile');
                    middlePage.style.display = 'none';
                }
            });
        }

        const flipToLogin = function(e) {
            e.preventDefault();
            middlePage.classList.remove('flipped');
            // Mobile responsive handling
            if (window.innerWidth <= 768 && rightPage) {
                rightPage.classList.remove('active-mobile');
                middlePage.style.display = 'block';
            }
        };

        if (toLoginBtn) toLoginBtn.addEventListener('click', flipToLogin);
        if (toLoginLink) toLoginLink.addEventListener('click', flipToLogin);

        // Mobile responsive handling on page load if page starts flipped (due to errors)
        if (middlePage.classList.contains('flipped')) {
            if (window.innerWidth <= 768 && rightPage) {
                rightPage.classList.add('active-mobile');
                middlePage.style.display = 'none';
            }
        }
    }
    
    // Reset view states if window is resized above mobile threshold
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            if (middlePage) middlePage.style.display = '';
            if (rightPage) rightPage.classList.remove('active-mobile');
        }
    });

    // Toggle Password Visibility
    const toggleIcons = document.querySelectorAll('.password-toggle-icon');
    toggleIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                } else {
                    input.type = 'password';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                }
            }
        });
    });

    // Form Submit Loading Effect (For both Customer & Admin auth forms)
    const authForms = document.querySelectorAll('form');
    authForms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                submitBtn.style.pointerEvents = 'none';
                submitBtn.style.opacity = '0.85';
                submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Processing...';
            }
        });
    });
});
