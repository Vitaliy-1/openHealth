document.addEventListener('DOMContentLoaded', function() {
    const openIcon = document.getElementById('openIcon');
    const closeIcon = document.getElementById('closeIcon');
    const menuButton = document.querySelector('.menu');
    const header = document.getElementById('header');
    const body = document.getElementById('body');
    const responsiveMenu = document.getElementById('responsiveMenu');
    const menuToggleIcons = document.querySelectorAll('#menuToggle i');

    //! Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    //! Toggle Menu
    const toggleMenu = function() {
        menuButton.classList.toggle('open');
        const isMenuOpen = header.classList.toggle('h-screen');

        openIcon.classList.toggle('hidden', isMenuOpen);
        closeIcon.classList.toggle('hidden', !isMenuOpen);

        body.classList.toggle('h-screen', isMenuOpen);
        body.classList.toggle('overflow-hidden', isMenuOpen);

        responsiveMenu.classList.toggle('hidden');
        menuToggleIcons.forEach(icon => icon.classList.toggle('hidden'));
    };

    document.getElementById('menuToggle').addEventListener('click', toggleMenu);
    responsiveMenu.addEventListener('click', toggleMenu);
});
