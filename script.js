let grozs = [];

function pievienotGrozam(elements) {
    let id = elements.getAttribute('data-id');
    let nosaukums = elements.getAttribute('data-nosaukums');

    grozs.push({ id: id, nosaukums: nosaukums});
    document.getElementById('cart-count-btn').innerText = grozs.length;
}

function atvertGrozu() {
    document.getElementById('cart-modal').style.display = 'flex';
    atjaunotGrozaSkatu();
}

function aizvertGrozu() {
    document.getElementById('cart-modal').style.display = 'none';
}

function atjaunotGrozaSkatu() {
    let list = document.getElementById('cart-items-list');
    list.innerHTML = "";

    if(grozs.length === 0) {
        list.innerHTML = "<p>Pievieno grāmatas rezervēšanai!</p>";
    } else {
        for(let i=0; i<grozs.length; i++) {
            // poga x
            list.innerHTML += `<div class='cart-item-row'>
                                <span>${grozs[i].nosaukums}</span>
                                    <div>
                                        <a class="remove-from-cart" data-index="${i}" onclick="nonemtNoGroza(this)"><i class="fa-solid fa-xmark"></i></a>
                                    </div>
                                </div>`;
        }
    }
}

function parietUzNoformesanu() {
    if(grozs.length === 0) {
        alert("Jūsu grozs ir tukšs!");
        return;
    }
    document.getElementById('cart-items-view').style.display = 'none';
    document.getElementById('checkout-form-view').style.display = 'block';
    document.getElementById('hidden_cart_data').value = JSON.stringify(grozs);
}

function atpakalUzGrozu() {
    document.getElementById('checkout-form-view').style.display = 'none';
    document.getElementById('cart-items-view').style.display = 'block';
}

function nonemtNoGroza(poga) {
    let index = parseInt(poga.getAttribute('data-index'), 10);
    // ja indeksa nav
    if (isNaN(index)) return;

    // izņem tieši vienu elementu no groza
    grozs.splice(index, 1);
    document.getElementById('cart-count-btn').innerText = grozs.length;
    atjaunotGrozaSkatu(); // pārzīmē sarakstu, kopsummu un jaunos indeksus
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
