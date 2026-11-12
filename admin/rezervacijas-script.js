console.log("JS darbojas!");

const LIMITS = 20;
let currPage = 1;

fetchRezervacijas();

async function fetchRezervacijas() {
    const tbody = document.querySelector('#rezervacijas-table-body');
        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Ielādē rezervācijas...</td></tr>';
    try {
        const response = await fetch(`api/rezervacijas-api.php?page=${currPage}`);
        const rezervacijas = await response.json();

        if (response.ok) {
            renderTable(rezervacijas.dati);
            renderPagination(rezervacijas.kopa);
        } else {
            showNotif(rezervacijas.error || "Neizdevās ielādēt rezervācijas!", 'error');
        }

    } catch (err) {
        showNotif("Kļūda savienojumā ar serveri!", 'error');
        console.error(err);
    }
}

function renderTable(rezervacijas) {
    const tbody = document.querySelector('#rezervacijas-table-body');
    tbody.innerHTML = '';

    if (!rezervacijas || rezervacijas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Nav nevienas rezervācijas.</td></tr>';
        return;
    }

    rezervacijas.forEach(r => {
        const tr = document.createElement('tr');

        let actionButtons = '';
        if (r.statuss === 'gaida') {
            actionButtons = `
                <a class="btn-status btn-gatavs" onclick="updateStatuss(${r.id}, 'aktīva')">Nodots</a><br>
                <a class="btn-status btn-atcelts" style="margin-top:4px;" onclick="updateStatuss(${r.id}, 'atcelta')">Atcelt</a>
            `;
        }else if (r.statuss === 'aktīva') {
            // atpakaļ poga, lai darbinieks varētu pārlikt rezervāciju atpakaļ uz iepriekšējo statusu, ja netīšam uzspieda "Nodots"
            actionButtons = `
                <a class="btn-status btn-gatavs" onclick="updateStatuss(${r.id}, 'pabeigta')">Pabeigt</a><br>
                <a class="btn-status btn-atcelts" style="margin-top:4px;" onclick="updateStatuss(${r.id}, 'gaida')">Atpakaļ</a>
            `;
        }

            tr.innerHTML = `
                <td>#${r.id}</td>
                <td>${r.lasitajs || 'Nezināms'}</td>
                <td>${r.epasts || '-'}</td>
                <td>${r.gramata}</td>
                <td>${r.atdosanas_datums || '-'}</td>
                <td><b>${r.statuss}</b></td>
                <td>${actionButtons}</td>
            `;

            tbody.appendChild(tr);
        });

}

async function updateStatuss(id, newStatus) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('statuss', newStatus);

    try {
        const response = await fetch(`api/rezervacijas-api.php`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (response.ok) {
            showNotif(data.message, 'success');
            await fetchRezervacijas();
        } else {
            showNotif(data.error || "Neizdevās atjaunot statusu!", 'error');
        }
    } catch (err) {
        showNotif("Neizdevās atjaunot statusu rezervācijai!", 'error');
        console.error(err);
    }
}

function showNotif(message, type = 'success') {
    const box = document.querySelector('#notif-box');

    box.textContent = message;
    box.className = `alert alert-${type}`;
    box.style.display = 'block';

    setTimeout(() => { box.style.display = 'none'; }, 3000);
}

function renderPagination(rezervacijas) {
    const buttons = document.querySelector('#rezervacijas-pagination');
    if (!buttons) return;
 
    const totalPages = Math.max(1, Math.ceil(rezervacijas / LIMITS));
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
                fetchRezervacijas();
            }
        });
    });
}