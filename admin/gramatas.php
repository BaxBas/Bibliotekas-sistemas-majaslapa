<?php
    $page_title   = "Grāmatas";
    $page_js      = "gramatas-script.js";
    $topbar_title = "Grāmatu pārvaldība";
    $body_class   = "gramatasBody";

    require_once 'header.php';
?>

            <div class="panel form-panel">
                <h3 id="form-panel-title">Pievienot jaunu</h3>
                <form id="gramatasForma" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nosaukums</label>
                        <input type="text" id="nosaukums" name="nosaukums" placeholder="Ievadiet grāmatas nosaukumu" autofocus onfocus="this.select();" required>
                    </div>
                    <div class="form-group">
                        <label>Autors</label>
                        <input type="text" id="autors" name="autors" placeholder="Ievadiet grāmatas autoru" required>
                    </div>
                    <div class="form-group">
                        <label>Apraksts</label>
                        <textarea id="apraksts" name="apraksts" rows="3" placeholder="Ievadiet grāmatas aprakstu" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Statuss</label>
                        <input type="text" id="statuss" name="statuss" value="Pieejams" placeholder="Pieejams/Rezervēts/Paņemts" required>
                    </div>
                    <div class="form-group">
                        <label>Attēls</label>
                        <input type="file" id="bilde" name="bilde" accept="image/jpeg, image/png">
                    </div>
                    <button type="submit" id="btn-submit" name="pievienot_edienu" class="btn-submit">Saglabāt grāmatu</button>
                    <button type="button" id="btn-cancel-edit" class="btn-submit">Atcelt</button>
                    <input type="hidden" id="id">
                </form>
            </div>

            <div class="panel table-panel">
                <h2>Esošās grāmatas</h2>
                <form id="search-form" class="search-bar">
                    <input type="text" id="search-input" name="meklet" placeholder="Meklēt pēc nosaukuma vai autora" autofocus onfocus="this.select();">
                    <button type="submit" id="search-btn" class="btn-action">Meklēt</button>
                    <button type="button" id="clear-btn" class="btn-action" style="background:#747d8c;">Notīrīt</button>
                </form>
                <table>
                    <thead id="gramatas-saraksts-head">
                        <tr>
                            <th>Attēls</th>
                            <th>Nosaukums</th>
                            <th>Autors</th>
                            <th>Darbības</th>
                        </tr>
                    </thead>
                    <tbody id="gramatas-saraksts"></tbody>
                </table>
                <div id="gramatas-pagination" class="pagination"></div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
    </div>
    <div class="modal" id="modal-admin-gramata-delete">
        <div class="modal-box">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <h2>Dzēst grāmatu?</h2>
            <p id="delete-text"></p>
            <button class="btn" id="confirm-delete">Jā, dzēst</button>
            <button class="btn" id="cancel-delete">Atcelt</button>
        </div>
    </div>
</body>
</html>