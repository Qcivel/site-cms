<?php
echo "Bonjour";
//IMPORT DE RESSOURCE
$style="../public/style/cms.css";

include '../utils/functions.php';
include '../models/model_cms.php';
include '../models/model_picture.php';

//Démarrer la séssion
session_start();

$message_status = "";
$message_picture = "" ;
$message_delete = "";

// 1. On vérifie si un message a été enregistré en session
if (isset($_SESSION['message_status'])) {
    
    // 2. On transfère le message de la session vers notre variable locale
    $message_status = $_SESSION['message_status'];
    
    // 3. On efface le message de la session (on déchire la page du carnet)
    unset($_SESSION['message_status']);
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


$userId = (int)$_SESSION['user_id']; // Définit l'ID de l'utilisateur connecté
// -----------------------------------------------------------------


// Connexion BDD
try {
    $bdd = new PDO('mysql:host=localhost;dbname=theoRenaut;charset=utf8mb4', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la BDD : " . $e->getMessage());
}

$series = new Series($bdd);

//Ajout de serie
if (isset($_POST['submit_series'])){
    $title_series = sanitize($_POST['title_series'] ?? '');

    $number_series = $series->getNextSeries($userId);
    
    if(!empty($title_series)) {

        try {
            // 2. Hydratation de l'objet (Action du Contrôleur)
            $series->setIdUser($userId); 
            $series->setTitleSeries($title_series);
            $series->setNumberSeries($number_series);

            if ($series->addSeries()) {
                $message_status = "<h3 style='color: green;'> SUCCÈS : La série '{$title_series}' a été ajoutée en BDD !</h3>";
            } else {
                $message_status = "<h3 style='color: red;'> ÉCHEC : L'insertion a échoué.</h3>";
            }

        } catch (PDOException $e){
            if($e->getCode() == 23000){
                $message_status = "<h3 style='color:red;'> ÉCHEC : Vous avez déjà une série portant ce numéro (Numéro {$number_series}). Veuillez choisir un numéro unique.</h3>";
            }
            
        } catch (Exception $e) {
            // Gère les exceptions, souvent dues à des erreurs SQL (doublon UNIQUE, NOT NULL, etc.)
            $message_status = "<h3 style='color: red;'> ÉCHEC : Erreur BDD ou violation de contrainte.</h3>";
            $message_status .= "Message : " . $e->getMessage();
            
        }

    }else {
        $message_status = "<h3 style='color:red;'>Veuillez remplir tout les champs</h3>";
    }
}
//Affiche les séries créer par id_user (utiliser dans le <select>)
$userSeriesList = $series->displaySeriesById($userId);

//Supprimer une série sélectionné 
if(isset($_POST['submit_delete_series'])){
    $id_serie_delete = (int)($_POST['id_series'] ?? 0);

    if($id_serie_delete > 0 ){
        $serie_manager = new Series($bdd);

        if($serie_manager ->deleteSerie($id_serie_delete)){
            //Message de succes en session
            $_SESSION['message_status'] = "<h3 style='color: green;'> SUCCES : La série a été supprimée avec toutes ses photos.</h3>";
            //Redirection 
            header('Location: cms.php');
            exit;
        } else {
            $message_status = "<h3 style='color: red;'> ÉCHEC : La suppression de la serie a échoué.</h3>";
        }
    }
}


$picture = new Picture($bdd);

//Ajoute des photos 
try{
    if (isset($_POST['submit_picture'])){

        $file_key = 'file_picture';

        
        $description_picture = sanitize($_POST['description_picture'] ?? '');
        $id_series = (int)($_POST['id_series'] ?? 0); 

        if (empty($id_series)){ // Vérification de la clé étrangère
            $message_picture = "<h3 style='color:red;'> L'identifiant de la série est manquant.</h3>";
        
        } else if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] === UPLOAD_ERR_NO_FILE) {
            $message_picture = "<h3 style='color:red;'> Veuillez sélectionner une photo pour l'upload.</h3>";
        
        } else {//Si tout est correct
            $count_files = count($_FILES['file_picture']['name']);

            $uploadDir = '/site-galerie/uploads/';

            if(!is_dir($uploadDir)){//Vérifie si le dossier uploads n'existe pas
                mkdir($uploadDir, 0755, true);//Création du dossier
            }
            //Boucle pour parcourir toute les données des photos ajoutées
            for ($i = 0; $i< $count_files; $i++){

                if($_FILES['file_picture']['error'][$i] == UPLOAD_ERR_OK){
                    // On récupère le nom original du fichier actuel
                    $current_name = $_FILES['file_picture']['name'][$i];

                    // On récupère l'emplacement temporaire du fichier actuel
                    $current_tmp_path = $_FILES['file_picture']['tmp_name'][$i];
            
                    $filename = uniqid() . '_' . basename($current_name); //créer un identifiant avec uniqid() et on va chercher le nom de la photo grace a basename
                    $uploadPath = $uploadDir . $filename; //Donne le chemin d'accès

                    if(move_uploaded_file($current_tmp_path, $uploadPath)){
                
                        // Hydratation de l'objet (Action du Contrôleur) 
                        $picture->setTitlePicture($current_name);
                        $picture->setDescriptionPicture($description_picture);
                        $picture->setUrlPicture($uploadPath);
                        $picture->setIdSeries($id_series);
                
                        if ($picture->addPicture()) {
                                $message_picture .= "<h3 style='color: green;'> SUCCÈS : La photo '{$current_name}' a été ajoutée en BDD !</h3>";
                        } else {
                            $message_picture = "<h3 style='color: red;'> ÉCHEC : L'insertion a échoué.</h3>";
                        }

                    } else {
                        $message_picture = "<h3 style='color:red;'> Échec : Impossible de déplacer le fichier sur le serveur. Vérifiez les permissions du dossier 'uploads'.</h3>";
                    }
                }else {
                    $message_picture = "<h3 style='color:red;'> Échec : Une erreur s'est produite lors du téléchargement (Code: " . $_FILES[$file_key]['error'] . ").</h3>"; 
                }
            }
        }
        echo $message_picture;
    }
}catch (PDOException $e){
    if($e->getCode() == 23000){
        $message_picture = "<h3 style='color:red;'> Échec : Vous avez déjà une photo portant ce nom. Veuillez choisir un nom unique.</h3>";
    }
}


//Récupère l'id de la series selectionné 
if (!empty ($_POST['filter_id_series']) ){
    $id_series_show = (int) $_POST['filter_id_series'];
    
//Récupère la 1er série si l'utilisateur n'a rien choisie 
}else if (!empty($userSeriesList)) { 
    $id_series_show = $userSeriesList[0]['id_series'];

//Si l'utilisateur n'a aucune série du tout, on met l'ID à 0
} else {
    $id_series_show =0;
}

$pictureModel = new Picture($bdd);
$picture_view = $pictureModel->getPicture($id_series_show);


//Appel des methods pour supprimer les photos
if (isset($_POST['delete_picture'])){
    $id_delete = $_POST['id_picture_delete'] ?? 0;

    if($id_delete > 0){
        $picture_delete = new Picture($bdd);

        $id_serie_to_show =  $picture_delete->getSeriesByPictureId($id_delete);
    
        if($picture_delete ->deletePicture($id_delete)){
            
            header('Location: cms.php?filter_id_series=' . $id_serie_to_show);
            exit; // Arrête l'exécution du script après la redirection
        } else {
            $message_delete = "<h3 style='color: red;'> ÉCHEC : La suppression de la photo a échoué.</h3>";
        }
    }else {
        $message_delete = "<h3 style='color: red;'> ERREUR : ID de photo invalide.</h3>";
        }
}



//Appel des vues HTML 
include '../views/view_header.php';
include '../views/view_cms.php';
include '../views/view_footer.php';

?>