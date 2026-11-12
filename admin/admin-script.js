function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.querySelector('.toggle-password');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.textContent = 'Slēpt';
    } else {
        passwordInput.type = 'password';
        toggleBtn.textContent = 'Rādīt';
    }
}

// Hamburger menu
const toggleBtn = document.getElementById('menu-toggle');
const sidebar = document.getElementById('sidebar');
const closeBtn = document.getElementById('menu-close');
const navLinks = sidebar ? sidebar.querySelectorAll('a') : [];


// Atver menu
toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    sidebar.classList.toggle('active');
});

// Aizver ar ieksējo
closeBtn.addEventListener('click', () => {
    sidebar.classList.remove('active');
});

// aizver, kad spiez uz cilni
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        sidebar.classList.remove('active');
    });
});

// aziver, kad spiez jebkur citur
document.addEventListener('click', (e) => {
    if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
        sidebar.classList.remove('active');
    }
});

// scroll to up poga
function checkMovement() {
    let btn = document.getElementById('uz-augsu-btn');
    if (!btn) return; // šī poga nav visās admin lapās

    let konteiners = document.querySelector('.content-container');
    let scrollTop = document.documentElement.scrollTop
                 || document.body.scrollTop
                 || (konteiners ? konteiners.scrollTop : 0);

    btn.style.display = scrollTop > 300 ? 'block' : 'none';
}

window.addEventListener('scroll', checkMovement);
document.querySelector('.content-container')?.addEventListener('scroll', checkMovement);

function uzAugsu() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    document.querySelector('.content-container')?.scrollTo({ top: 0, behavior: 'smooth' });
}