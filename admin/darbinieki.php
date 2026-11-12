<?php
    $page_title   = "Darbinieki";
    $page_js      = "darbinieki-script.js";
    $topbar_title = "Darbinieku pārvaldība";
    $body_class   = ""; 

    require_once 'header.php';

    if (!($_SESSION['loma'] === 'administrators')) { 
        header("Location: admin_dashboard.php"); 
        exit(); 
    }
?>

            <div class="panel form-panel-2">
                <h3 id="form-panel-title">Pievienot jaunu</h3>
                <form id="darbiniekiForma">
                    <div class="form-group">
                        <label>Lietotājvārds</label>
                        <input type="text" id="lietotajvards" name="lietotajvards" placeholder="Ievadiet lietotājvārdu" autofocus onfocus="this.select();" required>
                    </div>
                    <div class="form-group">
                        <label>Parole</label>
                        <input type="password" id="parole" name="parole" placeholder="Ievadiet paroli" required>
                    </div>
                    <div class="form-group">
                        <label>Loma</label>
                        <input type="text" id="loma" name="loma" value="moders" placeholder="Ievadiet lomu" required>
                    </div>
                    <button type="submit" id="btn-submit" class="btn-submit">Saglabāt darbinieku</button>
                    <button type="button" id="btn-cancel-edit" class="btn-submit" style="display:none;">Atcelt</button>
                    <input type="hidden" id="id">
                </form>
            </div>

            <div class="panel table-panel">
                <h2>Visi darbinieki</h2>
                <form id="search-form" class="search-bar">
                    <input type="text" id="search-input" name="meklet" placeholder="Meklēt pēc lietotājvārda" autofocus onfocus="this.select();">
                    <button type="submit" id="search-btn" class="btn-action">Meklēt</button>
                    <button type="button" id="clear-btn" class="btn-action" style="background:#747d8c;">Notīrīt</button>
                </form>
                <table>
                    <thead id="darbinieki-table-head">
                        <tr>
                            <th>ID</th>
                            <th>Lietotājvārds</th>
                            <th>Loma</th>
                            <th>Darbības</th>
                        </tr>
                    </thead>
                    <tbody id="darbinieki-table-body"></tbody>
                </table>
                <div id="darbinieki-pagination" class="pagination"></div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>

        <div class="modal" id="modal-admin-darbinieki-delete">
            <div class="modal-box">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <h2>Dzēst darbinieku?</h2>
                <p id="delete-text"></p>
                <button class="btn" id="confirm-delete">Jā, dzēst</button>
                <button class="btn" id="cancel-delete">Atcelt</button>
            </div>
        </div>
</body>
</html>