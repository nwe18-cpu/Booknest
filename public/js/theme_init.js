(function () {
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'warm') {
        document.documentElement.classList.add('warm-theme');
    }
})();
