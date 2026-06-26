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
});
