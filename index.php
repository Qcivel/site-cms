<?php
session_start();

// 1. IMPORT DES RESSOURCES (Chemins corrigés : on utilise ./)
include './utils/functions.php';
include './models/model_cms.php';
include './models/model_picture.php';
 
$page = $_GET['page'] ?? 'cms';
if($page === 'toDoList'){
        $style = './public/style/toDoList.css';
    }else {
        $style = './public/style/style.css'; 
    }
$message = ''; 

// 2. TRAITEMENT DU LOGIN
if(isset($_POST['submit_login'])){
    if(!empty($_POST['login']) && !empty($_POST['password'])){
        $loginSignUp = sanitize($_POST['login']);
        $passwordSignUp = sanitize($_POST['password']);
        
        try {
            $bdd = new PDO('mysql:host=localhost;dbname=theoRenaut','root','root',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            
            // Requête SQL (Note : j'ai ajouté r.name_rights car tu l'utilises après)
            $req = $bdd->prepare('SELECT u.id_user, u.login_user, u.password_user, r.name_rights FROM user u JOIN rights r ON u.id_rights = r.id_rights WHERE u.login_user = ? LIMIT 1');
            $req->execute([$loginSignUp]);
            $data = $req->fetch();

            if ($data && password_verify($passwordSignUp, $data['password_user'])) {
                // On remplit la session
                $_SESSION['user_id'] = $data['id_user'];
                $_SESSION['login'] = $data['login_user'];
                $_SESSION['rights_name'] = $data['name_rights'];

                // On recharge l'index pour que le "Routeur" ci-dessous s'active
                header('Location: index.php');
                exit;
            } else {
                $message = "Identifiant ou mot de passe incorrect";
            }
        } catch(EXCEPTION $error){
            die($error->getMessage());
        }
    } else {
        $message = "Veuillez remplir tous les champs";
    }
}

// 3. AFFICHAGE (Le Routeur)
include './views/view_header.php';

// Si l'utilisateur est connecté (sa session existe)
if(isset($_SESSION['user_id'])) {
    

    if($page === 'toDoList'){
        include './controller/toDoList.php';
    }else {
        include './controller/cms.php'; 
    }
} else {
    include './views/view_login.php';
}

include './views/view_footer.php';
?>