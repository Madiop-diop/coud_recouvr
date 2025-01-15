<?php
include('fonction.php');
$pavillon = "B1";
$etu = $_GET['etu'];
$id_etu = $_GET['id_etu'];
$info = info($etu);
$data = details($id_etu, $conn) ;
$total= 0;


?>
<?php
include_once("head.php");
?>

<head>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
</head>
<div class="container-fluid">
    <center>
        <h2> details Paiement</h2><br>
        <h1> <?= htmlspecialchars($info[4]) ?> <?= htmlspecialchars($info[3]) ?></h1>
    </center>
    <br><br>
    <center>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Quittance</th>
                    <th scope="col">Date Payement</th>
                    <th scope="col">Libelle</th>
                    <th scope="col">Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($data) && !empty($data)) : ?>
                <?php foreach ($data as $index => $row) : ?>

                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($row['id_paie']) ?></td>
                    <td><?= htmlspecialchars($row['dateTime_paie']) ?></td>
                    <td><?= htmlspecialchars($row['libelle']) ?></td>
                    <td><?= htmlspecialchars($row['montant']) ?></td>
                </tr>
                <?php $total += $row['montant']; // Calcul du total ?>
                <?php endforeach; ?>
                <tr style="font-weight: bold;">
                    <td colspan="3"></td>
                    <td>Total :</td>
                    <td><?= htmlspecialchars($total) ?></td>
                </tr>
                <?php else : ?>
                <tr>
                    <td colspan="6">Aucun étudiant trouvé pour ce pavillon.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <br><br>
        <button class="btn btn-success" onclick="goBack()">Retour</button>

        <script>
        function goBack() {
            window.history.back();
        }
        </script>
    </center>
</div>
<?php include('footer.php'); ?>

<script src="assets/js/script.js"></script>
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/plugins.js"></script>
<script src="assets/js/main.js"></script>
</body>

</html>