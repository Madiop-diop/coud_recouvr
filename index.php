<?php
include('fonction.php');
// Appel de la fonction pour récupérer les pavillons
$pavillons = getAllPavillons($conn);

?>
<?php
include_once("head.php");
?>
<div class="container">
    <div class="row add-bottom">
        <div class="row contact__main">
            <div class="col-eight tab-full contact__form1">
                <form id="loginForm" method="GET" action="index1.php">
                    <center>
                        <strong>VEUILLEZ RENSEIGNER LE CHAMPS</strong>
                    </center>
                    <fieldset>
                        <div class="form-field">
                            <select id="pavillon" name="pavillon" required class="full-width">
                                <option value="">Sélectionnez un pavillon</option>
                                <?php
                                    // Boucle pour ajouter les options
                                    foreach ($pavillons as $pavillon) {
                                         echo "<option value='" . htmlspecialchars($pavillon) . "'>" . htmlspecialchars($pavillon) . "</option>";
                                     }?>
                            </select>
                        </div>
                        <div class="form-field">
                            <button type="submit" class="full-width btn--primary">Rechercher</button>
                            <br><br>
                            <center> <a href=''>Retour</a> </center>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- <div id="preloader">
    <div id="loader"></div>
  </div> -->
<!-- Java Script================================================== -->
<script src="assets/js/script.js"></script>
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/plugins.js"></script>
<script src="assets/js/main.js"></script>
</body>

</html>