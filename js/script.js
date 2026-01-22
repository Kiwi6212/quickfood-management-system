let isOpen = false;

const toggleMenu = () => {
    isOpen = !isOpen;
    const menuContent = document.getElementById('menu-content');
    if (isOpen) {
        menuContent.classList.add('open');
    } else {
        menuContent.classList.remove('open');
    }
};

const goToCompte = () => {
    window.location.href = 'ComptePage.html'; // Navigue vers la page "Compte"
};

document.getElementById('menu-button').addEventListener('click', toggleMenu);
document.getElementById('compte-button').addEventListener('click', goToCompte);