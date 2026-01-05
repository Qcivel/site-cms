
<button><a href="../controller/deco.php">Se déconnecter</a></button>

<?php if (isset($_SESSION['login'])){
    echo "<h2>Utilisateur connécté : {$_SESSION['login']}</h2>";
}?>

<hr>


<h2>Créer une Nouvelle Série</h2>
<form action="" method="post" >
    <label for="">Nom serie</label>
    <input type="text" name="title_series" id="title_series" minlength="3" placeholder="Nom de la série *">

    <input type="hidden" name="id_user_owner" value="1">
    
    <input type="submit" name="submit_series" value="Créer une nouvelle série">
    <p><?php echo  $message_status ?></p>
</form><br>
<hr>

<h2>Supprimer une série</h2>
<form action="" method="post">
    <label for="id_series">Sélectionner la Série :</label>
    <select name="id_series" class="class_series"  required>
        <?php $index = 1;?>
        <?php foreach ($userSeriesList as $seriesItem): ?>
            <option value="<?php echo $seriesItem['id_series']; ?>">
                Série n°<?php echo $index ++; ?> : <?php echo htmlspecialchars($seriesItem['title_series']); ?>
            </option>
        <?php endforeach; ?>
    </select>
        <input type="submit" name="submit_delete_series" value="Supprimer la serie">
</form>

<h2>Filtrer les photos</h2>

<?php if (empty($userSeriesList)): ?>
    <p class="error">Vous n'avez pas encore créé de série. Veuillez commencer par en créer une ci-dessus.</p>
<?php else: ?>
    
    <form action="cms.php" method="post" enctype="multipart/form-data">
        <label for="id_series">Sélectionner la Série :</label>
        
        <select name="id_series" class="class_series" required>
            <?php $index = 1;?>
            <?php foreach ($userSeriesList as $seriesItem): ?>
                <option value="<?php echo $seriesItem['id_series']; ?>">
                    Série n°<?php echo $index ++; ?> : <?php echo htmlspecialchars($seriesItem['title_series']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label for="description_picture">Description de la photo :</label>
        <input type="text" name="description_picture" id="description_picture" placeholder="Description de la photo" >

        <label for="file_picture">Fichier photo :</label>
        <input type="file"  name="file_picture[]" id="file_picture" multiple required>
        
        <input type="submit" name="submit_picture" value="Ajouter la photo">
        <p><?php echo  $message_picture ?></p>
    </form>
<?php endif; ?>

<h2>Affichage des photos</h2>
    <form action="" method="post">
        <select name="filter_id_series" id="filter_id_series" onchange="this.form.submit()">
            <?php $index = 1;?>
            <?php foreach ($userSeriesList as $seriesItem): ?>
                <option value="<?php echo $seriesItem['id_series']; ?>"
                    <?php if ($seriesItem['id_series'] == $id_series_show) { echo 'selected'; } ?> >
                    Série n°<?php echo $index ++; ?> : <?php echo htmlspecialchars($seriesItem['title_series']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <h3>Photo de la série sélectionnée</h3>
    <?php if (!empty($picture_view)):?>
        <?php foreach ($picture_view as $picture_show): ?>
            <div class="photo_card_cms">
                <h3><?php echo htmlspecialchars($picture_show['title_picture']); ?></h3>
                <img src="<?php echo $picture_show['url_picture']; ?>" alt="<?php echo $picture_show['title_picture']; ?>" width="400">
                
                <?php if(!empty($picture_show['description_picture'])):?>
                    <p><?php echo htmlspecialchars($picture_show['description_picture']); ?></p>
                <?php endif; ?>

                <form action="cms.php" method="post">
                    <input type="hidden" name="id_picture_delete" value="<?php echo $picture_show['id_picture']; ?>">
                    <button type="submit" name="delete_picture" ;>Supprimer la photo</button>
                </form>
            </div>
    <?php endforeach; ?>
    <?php else : echo "La série ne contient aucune photo"; ?>
    <?php endif; ?>

    