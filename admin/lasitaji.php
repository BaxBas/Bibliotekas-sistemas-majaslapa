<?php
    $page_title   = "Lasītāji";
    $page_js      = "lasitaji-script.js";
    $topbar_title = "Lasītāju pārvaldība";
    $body_class   = ""; 

    require_once 'header.php';
?>

            <div class="panel form-panel-2">
                <h3 id="form-panel-title">Pievienot jaunu</h3>
                <form id="lasitajiForma">
                    <div class="form-group">
                        <label>Vārds</label>
                        <input type="text" id="vards" name="vards" placeholder="Ievadiet vārdu" autofocus onfocus="this.select();" required>
                    </div>
                    <div class="form-group">
                        <label>Uzvārds</label>
                        <input type="text" id="uzvards" name="uzvards" placeholder="Ievadiet uzvārdu" required>
                    </div>
                    <div class="form-group">
                        <label>E-pasts</label>
                        <input type="email" id="epasts" name="epasts" placeholder="Ievadiet e-pastu" required>
                    </div>
                    <button type="submit" id="btn-submit" class="btn-submit">Saglabāt lasītāju</button>
                    <button type="button" id="btn-cancel-edit" class="btn-submit" style="display:none;">Atcelt</button>
                    <input type="hidden" id="id">
                </form>
            </div>

            <div class="panel table-panel">
                <h2>Visi lasītāji</h2>
                <form id="search-form" class="search-bar">
                    <input type="text" id="search-input" name="meklet" placeholder="Meklēt pēc vārda, uzvārda vai epasta..." autofocus onfocus="this.select();">
                    <button type="submit" id="search-btn" class="btn-action">Meklēt</button>
                    <button type="button" id="clear-btn" class="btn-action" style="background:#747d8c;">Notīrīt</button>
                </form>
                <table>
                    <thead id="lasitaji-table-head">
                        <tr>
                            <th>ID</th>
                            <th>Vārds</th>
                            <th>Uzvārds</th>
                            <th>E-pasts</th>
                            <th>Darbības</th>
                        </tr>
                    </thead>
                    <tbody id="lasitaji-table-body"></tbody>
                </table>
                <div id="lasitaji-pagination" class="pagination"></div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>

        <div class="modal" id="modal-admin-lasitaji-delete">
            <div class="modal-box">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <h2>Dzēst lasītāju?</h2>
                <p id="delete-text"></p>
                <button class="btn" id="confirm-delete">Jā, dzēst</button>
                <button class="btn" id="cancel-delete">Atcelt</button>
            </div>
        </div>
</body>
</html>