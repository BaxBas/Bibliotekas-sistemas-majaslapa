<?php
    $page_title   = "Rezervācijas";
    $page_js      = "rezervacijas-script.js";
    $topbar_title = "Rezervācīju pārvaldība";
    $body_class   = "rezervacijasBody"; 

    require_once 'header.php';
?>

            <div class="table-container">
                <h2>Visas rezervācijas</h2>
                <table>
                    <thead id="rezervacijas-table-head">
                        <tr>
                            <th>ID</th>
                            <th>Lasītājs</th>
                            <th>E-pasts</th>
                            <th>Grāmatas</th>
                            <th>Atgriezts</th>
                            <th>Statuss</th>
                            <th>Darbības</th>
                        </tr>
                    </thead>
                    <tbody id="rezervacijas-table-body"></tbody>
                </table>
                <div id="rezervacijas-pagination" class="pagination"></div>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
    </div>
</body>
</html>