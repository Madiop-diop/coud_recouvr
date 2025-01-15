<?php
include('fonction.php');
//$pavillonDonne = "G(ESP)";
$pavillonDonne = $_GET['pavillon'];
$result = getPaymentDetailsByPavillon($pavillonDonne, $conn);


?>
<?php
include_once("head.php");
?>

<head>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
</head>
<div class="container-fluid">
    <center>
        <h2> GESTION DES RECOUVREMENTS</h2><br>
        <h2> PAVILLON : <?= htmlspecialchars($pavillonDonne) ?></h2>
    </center>
    <center>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Chambre</th>
                    <th scope="col">Lit</th>
                    <th scope="col">Num Etudiant</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Montant Facturé</th>
                    <th scope="col">Montant Payé</th>
                    <th scope="col">Restant</th>
                    <th scope="col">Rappel</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $counter = 1;
                    $currentChambre = null;
                    $litCount = 0;
                    $chambreRows = [];

                    foreach ($result as $row):
                        if ($currentChambre !== $row['chambre']):
                            if ($currentChambre !== null):
                                ?>
                <tr>
                    <th scope="row" rowspan="<?= $litCount ?>"><?= $counter ?></th>
                    <td rowspan="<?= $litCount ?>"><?= htmlspecialchars($currentChambre) ?></td>
                    <?php foreach ($chambreRows as $i => $litRow): ?>
                    <?php 
                                        // Vérification du statut du rappel pour chaque étudiant dans la ligne
                                        $resteAPayer = (int)$litRow['reste_a_payer'];
                                        $canRemind = false;

                                        // Vérification du montant restant à payer et de la date du dernier rappel
                                        if ($resteAPayer >= 6000) {
                                            if (!empty($litRow['rappel_envoye'])) {
                                                $lastReminderDate = new DateTime($litRow['rappel_envoye']);
                                                $currentDate = new DateTime();
                                                $interval = $lastReminderDate->diff($currentDate);

                                                // Si le dernier rappel a plus de 2 mois, autoriser le rappel
                                                if ($interval->m >= 2) {
                                                    $canRemind = true;
                                                }
                                            } else {
                                                $canRemind = true;  // Si aucun rappel n'a été envoyé
                                            }
                                        }
                                        ?>
                    <?php if ($i > 0): ?>
                <tr>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($litRow['lit']) ?></td>
                    <td><?= htmlspecialchars($litRow['num_etu']) ?></td>
                    <td><?= htmlspecialchars($litRow['etudiant_prenoms'] . " " . $litRow['etudiant_nom']) ?></td>
                    <td><?= number_format($litRow['montant_facture'], 0, ',', ' ') ?> F CFA</td>
                    <td>
                        <a
                            href="details.php?id_etu=<?= urlencode($litRow['etudiant_id']) ?>&etu=<?= urlencode($litRow['num_etu']) ?>">
                            <?= number_format($litRow['montant_paye'], 0, ',', ' ') ?> F CFA
                        </a>
                    </td>
                    <td><?= number_format($litRow['reste_a_payer'], 0, ',', ' ') ?> F CFA</td>
                    <td>
                        <button class="btn btn-secondary " disabled="disabled">rappel</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php
                                $counter++;
                            endif;

                            $currentChambre = $row['chambre'];
                            $litCount = 1;
                            $chambreRows = [$row];
                        else:
                            $litCount++;
                            $chambreRows[] = $row;
                        endif;
                    endforeach;

                    if ($currentChambre !== null):
                        ?>
                <tr>
                    <th scope="row" rowspan="<?= $litCount ?>"><?= $counter ?></th>
                    <td rowspan="<?= $litCount ?>"><?= htmlspecialchars($currentChambre) ?></td>
                    <?php foreach ($chambreRows as $i => $litRow): ?>
                    <?php 
                                // Vérification du statut du rappel pour chaque étudiant dans la ligne
                                $resteAPayer = (int)$litRow['reste_a_payer'];
                                $canRemind = false;

                                if ($resteAPayer >= 6000) {
                                    if (!empty($litRow['rappel_envoye'])) {
                                        $lastReminderDate = new DateTime($litRow['rappel_envoye']);
                                        $currentDate = new DateTime();
                                        $interval = $lastReminderDate->diff($currentDate);

                                        if ($interval->m >= 2) {
                                            $canRemind = true;
                                        }
                                    } else {
                                        $canRemind = true; // Si aucun rappel n'a été envoyé
                                    }
                                }
                                ?>
                    <?php if ($i > 0): ?>
                <tr>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($litRow['lit']) ?></td>
                    <td><?= htmlspecialchars($litRow['num_etu']) ?></td>
                    <td><?= htmlspecialchars($litRow['etudiant_prenoms'] . " " . $litRow['etudiant_nom']) ?></td>
                    <td><?= number_format($litRow['montant_facture'], 0, ',', ' ') ?> F CFA</td>
                    <td>
                        <a
                            href="details.php?id_etu=<?= urlencode($litRow['etudiant_id']) ?>&etu=<?= urlencode($litRow['num_etu']) ?>">
                            <?= number_format($litRow['montant_paye'], 0, ',', ' ') ?> F CFA
                        </a>
                    </td>
                    <td><?= number_format($litRow['reste_a_payer'], 0, ',', ' ') ?> F CFA</td>
                    <td>
                        <button class="btn btn-secondary " disabled>rappel</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <br><br>
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