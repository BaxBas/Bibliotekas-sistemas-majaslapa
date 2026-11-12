console.log("JS darbojas!");

let deleteDarbinieksId = null;
let currPage = 1;
let currSearch = '';


document.querySelector('#confirm-delete')?.addEventListener('click', async () => {
    if (deleteDarbinieksId) {
        await deleteDarbinieks(deleteDarbinieksId);
        hideDeleteModal();
    }
});
document.querySelector('#cancel-delete')?.addEventListener('click', hideDeleteModal);

function showDeleteModal() {
    document.querySelector('#modal-admin-darbinieki-delete').style.display = 'flex';
}

function hideDeleteModal() {
    document.querySelector('#modal-admin-darbinieki-delete').style.display = 'none';
    deleteDarbinieksId = null;
}

document.querySelector('#darbinieki-table-body')?.addEventListener('click', handleTableClick);
document.querySelector('#darbiniekiForma')?.addEventListener('submit', handleFormSubmit);
document.querySelector('#btn-cancel-edit')?.addEventListener('click', resetForm);
document.querySelector('#clear-btn')?.addEventListener('click', () => {
    document.querySelector('#search-input').value = '';
    currSearch = '';
    currPage = 1;
    fetchDarbinieki();
});
document.querySelector('#search-form')?.addEventListener('submit', handleSearch);

fetchDarbinieki();

async function fetchDarbinieki() {
    const tbody = document.querySelector('#darbinieki-table-body');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Ielādē darbiniekus...</td></tr>';

    try {
        const url = currSearch ? `api/darbinieki-api.php?search=${encodeURIComponent(currSearch)}&page=${currPage}` : `api/darbinieki-api.php?page=${currPage}`;
        const response = await fetch(url);
        const darbinieki = await response.json();

        if (response.ok) {
            renderTable(darbinieki.dati ?? []);
            renderPagination(darbinieki.kopa ?? 0);
        } else {
            showNotif(darbinieki.error || "Neizdevās ielādēt darbiniekus!", 'error');
        }
    } catch (err) {
        showNotif("Kļūda savienojumā ar serveri!", 'error');
        console.error(err);
    }
}

function renderTable(darbinieki) {
    const tbody = document.querySelector('#darbinieki-table-body');
    tbody.innerHTML = '';

    if (!darbinieki || darbinieki.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">Nav neviena darbinieka.</td></tr>';
        return;
    }

    darbinieki.forEach(d => {
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', d.id);
        tr.setAttribute('data-lietotajvards', d.lietotajvards);

        tr.innerHTML = `
            <td>#${d.id}</td>
            <td>${d.lietotajvards}</td>
            <td>${d.loma}</td>
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
        const lietotajvards = row.getAttribute('data-lietotajvards');

        deleteDarbinieksId = id;
        document.querySelector('#delete-text').innerHTML = `Vai tiešām vēlies dzēst darbinieku <span id="delete-darbinieks">${lietotajvards}</span>?`;
        showDeleteModal();
    }

    if (editBtn) {
        e.preventDefault();
        const row = editBtn.closest('tr');
        const id = row.getAttribute('data-id');
        await editDarbinieks(id);
    }
}

async function editDarbinieks(id) {
    try {
        const response = await fetch(`api/darbinieki-api.php?id=${id}`);
        const darbinieks = await response.json();

        if (!response.ok) {
            showNotif(darbinieks.error || "Neizdevās ielādēt darbinieka datus!", 'error');
            return;
        }

        document.querySelector('#lietotajvards').value = darbinieks.lietotajvards;
        document.querySelector('#loma').value = darbinieks.loma;
        document.querySelector('#id').value = darbinieks.id;

        // Rediģējot parole nav obligāta - ja atstāj tukšu, tā netiek mainīta
        const paroleInput = document.querySelector('#parole');
        if (paroleInput) {
            paroleInput.value = '';
            paroleInput.required = false;
            paroleInput.placeholder = 'Atstāt tukšu, lai nemainītu paroli';
        }

        const title = document.querySelector('#form-panel-title');
        if (title) title.textContent = `Rediģēt "${darbinieks.lietotajvards}"`;

        const cancelBtn = document.querySelector('#btn-cancel-edit');
        if (cancelBtn) cancelBtn.style.display = 'inline-block';

        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (err) {
        showNotif("Neizdevās ielādēt darbinieka datus!", 'error');
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

    formData.append('lietotajvards', document.querySelector('#lietotajvards').value);
    formData.append('loma', document.querySelector('#loma').value);
    formData.append('parole', document.querySelector('#parole').value);

    try {
        const response = await fetch('api/darbinieki-api.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (!response.ok) {
            showNotif(data.error || "Neizdevās saglabāt datus!", 'error');
        } else {
            showNotif(data.message || "Dati veiksmīgi saglabāti!", 'success');
            resetForm();
            await fetchDarbinieki();
        }
    } catch (err) {
        showNotif("Kļūda saglabājot datus!", 'error');
        console.error(err);
    }
}

function resetForm() {
    const form = document.querySelector('#darbiniekiForma');
    if (form) form.reset();

    document.querySelector('#id').value = '';

    // Atjauno paroles lauku uz "jauns darbinieks" režīmu
    const paroleInput = document.querySelector('#parole');
    if (paroleInput) {
        paroleInput.required = true;
        paroleInput.placeholder = 'Ievadiet paroli';
    }
    
    const title = document.querySelector('#form-panel-title');
    if (title) title.textContent = 'Pievienot jaunu';

    const cancelBtn = document.querySelector('#btn-cancel-edit');
    if (cancelBtn) cancelBtn.style.display = 'none';
}

async function deleteDarbinieks(id) {
    try {
        const response = await fetch(`api/darbinieki-api.php?id=${id}`, {
            method: 'DELETE'
        });
        const data = await response.json();

        if (!response.ok) {
            showNotif(data.error || "Neizdevās dzēst darbinieku!", 'error');
        } else {
            showNotif(data.message || "Darbinieks dzēsts!", 'success');
            await fetchDarbinieki();
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
    await fetchDarbinieki();
}

function renderPagination(kopa, limits = 10) {
    const buttons = document.querySelector('#darbinieki-pagination');
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
                fetchDarbinieki();
            }
        });
    });
}