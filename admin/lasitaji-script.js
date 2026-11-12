console.log("JS darbojas!");

let deleteLasitajsId = null;
let currPage = 1;
let currSearch = '';

document.querySelector('#confirm-delete')?.addEventListener('click', async () => {
    if (deleteLasitajsId) {
        await deleteLasitajs(deleteLasitajsId);
        hideDeleteModal();
    }
});
document.querySelector('#cancel-delete')?.addEventListener('click', hideDeleteModal);

function showDeleteModal() {
    document.querySelector('#modal-admin-lasitaji-delete').style.display = 'flex';
}

function hideDeleteModal() {
    document.querySelector('#modal-admin-lasitaji-delete').style.display = 'none';
    deleteLasitajsId = null;
}

document.querySelector('#lasitaji-table-body')?.addEventListener('click', handleTableClick);
document.querySelector('#lasitajiForma')?.addEventListener('submit', handleFormSubmit);
document.querySelector('#btn-cancel-edit')?.addEventListener('click', resetForm);
document.querySelector('#clear-btn')?.addEventListener('click', () => {
    document.querySelector('#search-input').value = '';
    currSearch = '';
    currPage = 1;
    fetchLasitaji();
});
document.querySelector('#search-form')?.addEventListener('submit', handleSearch);

fetchLasitaji();

async function fetchLasitaji() {
    const tbody = document.querySelector('#lasitaji-table-body');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Ielādē lasītājus...</td></tr>';

    try {
        const url = currSearch ? `api/lasitaji-api.php?search=${encodeURIComponent(currSearch)}&page=${currPage}` : `api/lasitaji-api.php?page=${currPage}`;
        const response = await fetch(url);
        const lasitaji = await response.json();

        if (response.ok) {
            renderTable(lasitaji.dati ?? []);
            renderPagination(lasitaji.kopa ?? 0);
        } else {
            showNotif(lasitaji.error || "Neizdevās ielādēt lasītājus!", 'error');
        }
    } catch (err) {
        showNotif("Kļūda savienojumā ar serveri!", 'error');
        console.error(err);
    }
}

function renderTable(lasitaji) {
    const tbody = document.querySelector('#lasitaji-table-body');
    tbody.innerHTML = '';

    if (!lasitaji || lasitaji.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Nav neviena lasītāja.</td></tr>';
        return;
    }

    lasitaji.forEach(l => {
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', l.id);
        tr.setAttribute('data-vards', l.vards);

        tr.innerHTML = `
            <td>#${l.id}</td>
            <td>${l.vards}</td>
            <td>${l.uzvards}</td>
            <td>${l.epasts}</td>
            <td>
                <a class="btn-edit" style="background:#eccc68;">Rediģēt</a>
                <a class="btn-delete" style="background:#ff4757;">Dzēst</a>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

async function handleTableClick(e) {
    const deleteBtn = e.target.closest('.btn-delete');
    const editBtn = e.target.closest('.btn-edit');

    if (deleteBtn) {
        e.preventDefault();
        const row = deleteBtn.closest('tr');
        const id = row.getAttribute('data-id');
        const vards = row.getAttribute('data-vards');

        deleteLasitajsId = id;
        document.querySelector('#delete-text').innerHTML = `Vai tiešām vēlies dzēst lasītāju <span id="delete-lasitajs">${vards}</span>?`;
        showDeleteModal();
    }

    if (editBtn) {
        e.preventDefault();
        const row = editBtn.closest('tr');
        const id = row.getAttribute('data-id');
        await editLasitajs(id);
    }
}

async function editLasitajs(id) {
    try {
        const response = await fetch(`api/lasitaji-api.php?id=${id}`);
        const lasitajs = await response.json();

        document.querySelector('#vards').value = lasitajs.vards;
        document.querySelector('#uzvards').value = lasitajs.uzvards;
        document.querySelector('#epasts').value = lasitajs.epasts;
        document.querySelector('#id').value = lasitajs.id;

        const title = document.querySelector('#form-panel-title');
        if (title) title.textContent = `Rediģēt "${lasitajs.vards} ${lasitajs.uzvards}"`;

        const cancelBtn = document.querySelector('#btn-cancel-edit');
        if (cancelBtn) cancelBtn.style.display = 'inline-block';

        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (err) {
        showNotif("Neizdevās ielādēt lasītāja datus!", 'error');
        console.error(err);
    }
}

async function handleFormSubmit(e) {
    e.preventDefault();

    const formData = new FormData();
    const idValue = document.querySelector('#id').value;

    if (idValue) {
        formData.append('id', idValue);
        formData.append('action', 'update');
    } else {
        formData.append('action', 'create');
    }

    formData.append('vards', document.querySelector('#vards').value);
    formData.append('uzvards', document.querySelector('#uzvards').value);
    formData.append('epasts', document.querySelector('#epasts').value);

    try {
        const response = await fetch('api/lasitaji-api.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (!response.ok) {
            showNotif(data.error || "Neizdevās saglabāt datus!", 'error');
        } else {
            showNotif(data.message || "Dati veiksmīgi saglabāti!", 'success');
            resetForm();
            await fetchLasitaji();
        }
    } catch (err) {
        showNotif("Kļūda saglabājot datus!", 'error');
        console.error(err);
    }
}

function resetForm() {
    const form = document.querySelector('#lasitajiForma');
    if (form) form.reset();

    document.querySelector('#id').value = '';
    
    const title = document.querySelector('#form-panel-title');
    if (title) title.textContent = 'Pievienot jaunu';

    const cancelBtn = document.querySelector('#btn-cancel-edit');
    if (cancelBtn) cancelBtn.style.display = 'none';
}

async function deleteLasitajs(id) {
    try {
        const response = await fetch(`api/lasitaji-api.php?id=${id}`, {
            method: 'DELETE'
        });
        const data = await response.json();

        if (!response.ok) {
            showNotif(data.error || "Neizdevās dzēst lasītāju!", 'error');
        } else {
            showNotif(data.message || "Lasītājs dzēsts!", 'success');
            await fetchLasitaji();
        }
    } catch (err) {
        showNotif("Kļūda dzēšot ierakstu!", 'error');
        console.error(err);
    }
}

function showNotif(message, type = 'success') {
    const box = document.querySelector('#notif-box');
    if (!box) return;

    box.textContent = message;
    box.className = `alert alert-${type}`;
    box.style.display = 'block';
    setTimeout(() => { box.style.display = 'none'; }, 3000);
}

async function handleSearch(e) {
    if (e) e.preventDefault();
    currSearch = document.querySelector('#search-input').value.trim();
    currPage = 1;
    await fetchLasitaji();
}

function renderPagination(kopa, limits = 10) {
    const buttons = document.querySelector('#lasitaji-pagination');
    if (!buttons) return;

    const totalPages = Math.max(1, Math.ceil(kopa / limits));
    let html = `<button class="page-btn" data-page="${currPage - 1}" ${currPage === 1 ? 'disabled' : ''}>‹</button>`;

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

    html += `<button class="page-btn" data-page="${currPage + 1}" ${currPage === totalPages ? 'disabled' : ''}>›</button>`;
    buttons.innerHTML = html;

    buttons.querySelectorAll('.page-btn:not([disabled])').forEach(button => {
        button.addEventListener('click', () => {
            let newPage = parseInt(button.getAttribute('data-page'));
            if (!isNaN(newPage) && newPage !== currPage) {
                currPage = newPage;
                fetchLasitaji();
            }
        });
    });
}