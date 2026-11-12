console.log("JS darbojas!");

let deleteGramataId = null;
document.querySelector('#confirm-delete').addEventListener('click', async () => {
    if(deleteGramataId){
        await deleteGramata(deleteGramataId);
        hideDeleteModal();
    }
});
document.querySelector('#cancel-delete').addEventListener('click', hideDeleteModal);

function showDeleteModal(){
    document.querySelector('#modal-admin-gramata-delete').style.display = 'flex';
}

function hideDeleteModal(){
    document.querySelector('#modal-admin-gramata-delete').style.display = 'none';
    deleteGramataId = null;
}

document.querySelector('#gramatasForma').addEventListener('submit', handleFormSubmit);
document.querySelector('#gramatas-saraksts').addEventListener('click', handleTableClick);
document.querySelector('#btn-cancel-edit').addEventListener('click', resetForm);
document.querySelector('#clear-btn')?.addEventListener('click', () => {
        document.querySelector('#search-input').value = '';
        currSearch = '';
        currPage = 1;
        fetchGramatas();
    });
document.querySelector('#search-form')?.addEventListener('submit', handleSearch);

// const LIMITS = 20;
let currPage = 1;
let currSearch = '';

fetchGramatas();

async function fetchGramatas() {
    try {
        const url = currSearch ? `api/gramatas-api.php?search=${encodeURIComponent(currSearch)}&page=${currPage}` : `api/gramatas-api.php?page=${currPage}`; // meklēšana
        const response = await fetch(url);
        const atbilde = await response.json();
        const gramatas = atbilde.dati ?? [];
        
        let template = '';
        if (gramatas.length === 0) {
            template = `<tr><td colspan="4">Grāmatu saraksts ir tukšs.</td></tr>`;
        } else {
            gramatas.forEach(g => {
                template += `
                    <tr data-id="${g.id}" data-nosaukums="${g.nosaukums}">
                        <td><img src="../img/${g.bilde}" class='thumbnail' alt='Bilde'></td>
                        <td><strong>${g.nosaukums}</strong><br><small style='color:#747d8c;'>${g.apraksts}</small></td>
                        <td>${g.autors}</td>
                        <td>
                            <a class='btn-delete' style="background:#ff4757;">Dzēst</a>
                            <a class='btn-edit' style="background:#eccc68;">Rediģēt</a>
                        </td>
                    </tr>
                `;
            });
        }
        renderPagination(atbilde.kopa ?? 0, atbilde.limits ?? 5);
        document.querySelector('#gramatas-saraksts').innerHTML = template;

    } catch (err) {
        showNotif("Neizdevās ielādēt grāmatas!", "error");
        console.error(err);
    }
}

async function handleTableClick(e) {
    const deleteBtn = e.target.closest('.btn-delete');
    const editBtn = e.target.closest('.btn-edit');

    if (deleteBtn) {
        e.preventDefault();
        const row = deleteBtn.closest('tr');
        const id = row.getAttribute('data-id');

        const nosaukums = row.getAttribute('data-nosaukums');
        deleteGramataId = id;

        document.querySelector('#delete-text').innerHTML = `Vai tiešām vēlies dzēst grāmatu "<span id="delete-food">${nosaukums}</span>"?`;

        showDeleteModal(deleteGramataId);
    }

    if(editBtn){
        e.preventDefault();
        const row = editBtn.closest('tr');
        const id = row.getAttribute('data-id');
        console.log(id);
        await editGramata(id);
    }
}

async function handleFormSubmit(e) {
    e.preventDefault();

    // FormData - vajag file upload
    const formData = new FormData();
    const idValue = document.querySelector('#id')?.value;
    if (idValue) {
        formData.append('id', idValue);
    }

    formData.append('nosaukums', document.querySelector('#nosaukums').value);
    formData.append('apraksts', document.querySelector('#apraksts').value);
    formData.append('autors', document.querySelector('#autors').value);
    formData.append('statuss', document.querySelector('#statuss').value);

    const attelsFile = document.querySelector('#bilde').files[0];
    if (attelsFile) {
        formData.append('bilde', attelsFile);
    }

    const url = 'api/gramatas-api.php';

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (!response.ok) {
            showNotif(data.error || "Kļūda saglabājot datus!", 'error');
        } else {
            // hideModal();
            showNotif(data.message || "Grāmata veiksmīgi saglabāta!", 'success');
            resetForm();
            await fetchGramatas();
        }
    } catch (err) {
        showNotif("Saziņas kļūda ar serveri!", 'error');
        console.error(err);
    }
}

async function deleteGramata(id) {
    try {
        const response = await fetch(`api/gramatas-api.php?id=${id}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (!response.ok) {
            showNotif(data.error || "Neizdevās dzēst ierakstu!", 'error');
        } else {
            showNotif(data.message || "Ieraksts dzēsts!", 'success');
            await fetchGramatas();
        }
    } catch (err) {
        showNotif("Neizdevās dzēst grāmatu!", 'error');
        console.error(err);
    }
}

async function editGramata(id){
    try{
        const response = await fetch(`api/gramatas-api.php?id=${id}`);
        const gramata = await response.json();
        document.querySelector("#nosaukums").value = gramata.nosaukums;
        document.querySelector("#apraksts").value = gramata.apraksts;
        document.querySelector("#autors").value = gramata.autors;
        document.querySelector("#statuss").value = gramata.statuss;
        
        const idField = document.querySelector("#id");
        if (idField) idField.value = gramata.id;

        const title = document.querySelector('#form-panel-title');
        if (title) title.textContent = `Rediģēt "${gramata.nosaukums}"`;

        const cancelBtn = document.querySelector('#btn-cancel-edit');
        if (cancelBtn) cancelBtn.style.display = 'inline-block';
    }catch(err){
        alert("Neizdevās ielādēt grāmatu!");
        console.error(err);
    }
}

function resetForm() {
    const form = document.querySelector('#gramatasForma');
    if (form) form.reset();

    const idField = document.querySelector('#id');
    if (idField) idField.value = '';

    const title = document.querySelector('#form-panel-title');
    if (title) title.textContent = 'Pievienot jaunu';

    const cancelBtn = document.querySelector('#btn-cancel-edit');
    if (cancelBtn) cancelBtn.style.display = 'none';
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
    currSearch = document.querySelector('#search-input').value.trim();
    currPage = 1;
    await fetchGramatas();
    }catch(err){
        showNotif("Neizdevās sameklēt datus!", 'error');
        console.error(err);
    }
}

function renderPagination(gramatas, limits) {
    const buttons = document.querySelector('#gramatas-pagination');
    if (!buttons) return;
 
    const totalPages = Math.max(1, Math.ceil(gramatas / limits));
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
                fetchGramatas();
            }
        });
    });
}