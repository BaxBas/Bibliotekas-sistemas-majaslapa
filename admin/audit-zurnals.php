<?php
    $page_title   = "Žurnālfaili";
    $page_js      = "audit-zurnals-script.js";
    $topbar_title = "Žurnālfaili";
    $body_class   = "auditSarakstsBody";

    require_once 'header.php';

    if (!($_SESSION['loma'] === 'administrators')) { 
        header("Location: admin_dashboard.php"); 
        exit(); 
    }
?>

            <div class="table-container">
                
                <form id="search-form" class="search-bar">
                    <input type="text" id="search-input" name="meklet" placeholder="Meklēt..." autofocus onfocus="this.select();">
                    <button type="submit" id="search-btn" class="btn-action">Meklēt</button>
                    <button type="button" id="clear-btn" class="btn-action" style="background:#747d8c;">Notīrīt</button>
                </form>

                <table>
                    <thead id="logi-table-body-head">
                        <tr>
                            <th>ID</th>
                            <th>Darbinieks</th>
                            <th>Darbība</th>
                            <th>Tabula</th>
                            <th>Nosaukums</th>
                            <th>Laiks</th>
                        </tr>
                    </thead>
                    <tbody id="logi-table-body"></tbody>
                </table>
                <div id="logs-pagination" class="pagination"></div>
            </div>
        </div>
    </div>
</body>
</html>