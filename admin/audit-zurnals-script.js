console.log("JS darbojas!");

let logiData = [];
document.querySelector('#clear-btn')?.addEventListener('click', () => {
        document.querySelector('#search-input').value = '';
        search = '';
        currPage = 1;
        fetchLogi();
    });
document.querySelector('#search-form')?.addEventListener('submit', handleSearch);

const LIMITS = 20;
let currPage = 1;
let search = '';

fetchLogi();

async function fetchLogi() {
    const tbody = document.querySelector('#logi-table-body');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Ielādē datus...</td></tr>';

    try {
        const url = search ? `api/audit-zurnals-api.php?search=${encodeURIComponent(search)}&page=${currPage}` : `api/audit-zurnals-api.php?page=${currPage}`;
        const response = await fetch(url);
        const result = await response.json();

        logiData = result.dati ?? [];
        renderPagination(result.kopa ?? 0);

        renderTable(logiData);

    } catch (err) {
        showNotif("Neizdevās ielādēt logus!", 'error');
        console.error(err);
    }
}

function renderTable(logi) {
    const tbody = document.querySelector('#logi-table-body');
    tbody.innerHTML = '';

    if (!logi || logi.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Neviens logs nav atrasts.</td></tr>';
        return;
    }

    logi.forEach(l => {
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', l.id);
        tr.innerHTML = `
            <td>#${l.id}</td>
            <td>${l.darbinieks}</td>
            <td>${l.darbiba}</td>
            <td>${l.tabula}</td>
            <td>${l.ieraksta_nosaukums}</td>
            <td>${l.laiks}</td>
        `;
        tbody.appendChild(tr);
    });
}

function showNotif(message, type = 'success') {
    const box = document.querySelector(`#notif-box`);
    if (!box) return;

    box.textContent = message;
    box.className = `alert alert-${type}`;
    box.style.display = 'block';

    setTimeout(() => { box.style.display = 'none'; }, 3000);
}

async function handleSearch(e) {
    if (e) e.preventDefault();
    try{
    search = document.querySelector('#search-input').value.trim();
    currPage = 1;
    await fetchLogi();
    }catch(err){
        showNotif("Neizdevās meklēt datus!", 'error');
        // alert("Neizdevās sameklēt datus!");
        console.error(err);
    }
}

function renderPagination(logs) {
    const buttons = document.querySelector('#logs-pagination');
    if (!buttons) return;
 
    const totalPages = Math.max(1, Math.ceil(logs / LIMITS));
    let html = '';
 
    // Poga "atpakaļ"
    html += `<button class="page-btn" data-page="${currPage - 1}" ${currPage === 1 ? 'disabled' : ''}>‹</button>`;

    // Lapu numuru logs ap pašreizējo lapu (max 2 uz katru pusi)
    let start = Math.max(1, currPage - 2);
    let end = Math.min(totalPages, currPage + 2);

    if (start > 1) {
        html += `<button class="page-btn" data-page="1">1</button>`;
        if (start > 2) html += `<span class="page-dots">...</span>`;
    }
 
    for (let i = start; i <= end; i++) {
        html += `<button class="page-btn ${i === currPage ? 'aktiva' : ''}" data-page="${i}">${i}</button>`;
    }
 
    if (end < totalPages) {
        if (end < totalPages - 1) html += `<span class="page-dots">...</span>`;
        html += `<button class="page-btn" data-page="${totalPages}">${totalPages}</button>`;
    }
 
    // Poga "uz priekšu"
    html += `<button class="page-btn" data-page="${currPage + 1}" ${currPage === totalPages ? 'disabled' : ''}>›</button>`;

    buttons.innerHTML = html;

    buttons.querySelectorAll('.page-btn:not([disabled])').forEach(button => {
        button.addEventListener('click', () => {
            let newPage = parseInt(button.getAttribute('data-page'));
            if (!isNaN(newPage) && newPage !== currPage) {
                currPage = newPage;
                fetchLogi();
            }
        });
    });
}