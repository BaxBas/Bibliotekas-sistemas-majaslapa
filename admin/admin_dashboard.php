<?php
    $page_title   = "Vadības panelis";
    $topbar_title = "Vadības panelis";
    $body_class   = "dashboardBody";

    require_once 'header.php';
    include '../database/db_connect.php';

    // skaita datus stats-grid sekcijai
    // skaita cik kopā grāmatu ir sistēmā
    $gramatas_q = mysqli_query($conn, "SELECT COUNT(*) AS kopa_gramatu FROM gramatas WHERE statuss != 'dzēsts'");
    $gramatas_count = mysqli_fetch_assoc($gramatas_q)['kopa_gramatu'] ?? 0; // <-- ?? apzīmē, ka, ja nebūs vertības, tad deafault vertība būs 0

    // skaita lasītājus
    $lasitaji_q = mysqli_query($conn, "SELECT COUNT(*) AS kopa_lasitaju FROM lasitaji WHERE statuss = 'Aktīvs'");
    $lasitaji_count = mysqli_fetch_assoc($lasitaji_q)['kopa_lasitaju'] ?? 0;

    // skaita aktīvas rezervācijas (gaida vai aktīva) tikai aktīviem lasītājiem 
    $rezerv_q = mysqli_query($conn, "SELECT COUNT(*) AS kopa_rezerv FROM rezervacijas AS r LEFT JOIN lasitaji AS l ON r.lasitajs_id = l.id WHERE l.statuss = 'Aktīvs'");
    $rezerv_count = mysqli_fetch_assoc($rezerv_q)['kopa_rezerv'] ?? 0;
?>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Grāmatas sistēmā</h3>
                    <div class="value"><?= $gramatas_count ?></div>
                </div>
                <div class="stat-card">
                    <h3>Reģistrēti lasītāji</h3>
                    <div class="value"><?= $lasitaji_count ?></div>
                </div>
                <div class="stat-card">
                    <h3>Aktīvas rezervācijas</h3>
                    <div class="value"><?= $rezerv_count ?></div>
                </div>
            </div>

            <div class="table-container">
                <h3>Jaunākās grāmatas</h3>
                <table>
                    <thead id="admin_dashboard-table-head">
                        <tr>
                            <th>ID</th>
                            <th>Nosaukums</th>
                            <th>Autors</th>
                            <th>Statuss</th>
                        </tr>
                    </thead>
                    <tbody id="admin_dashboard-table-body">
                        <?php
                            $recent_books = mysqli_query($conn, "SELECT * FROM gramatas WHERE statuss!='dzēsts' ORDER BY id DESC LIMIT 6");
                            
                            if (mysqli_num_rows($recent_books) > 0) {
                                while($book = mysqli_fetch_assoc($recent_books)) {
                                    ?>

                                    <tr>
                                        <td>#<?= $book['id'] ?></td>
                                        <td><?= htmlspecialchars($book['nosaukums']) ?></td>
                                        <td><?= htmlspecialchars($book['autors']) ?></td>
                                        <td><b><?= htmlspecialchars($book['statuss']) ?></b></td>
                                    </tr>

                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='4'>Nav reģistrētu grāmatu.</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php require_once 'footer.php'; ?>
    </div>
</body>
</html>