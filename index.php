<?php
//Démarrer la session
session_start();

//IMPORT DE RESSOURCE
$style='./public/style/style.css';
include '../utils/functions.php';
include '../models/model_cms.php';

//Initialiser la variable d'affichage
$message ='';

//Formulaire de connexion

if(isset($_POST['submit_login'])){
    if(!empty($_POST['login']) && !empty($_POST['password'])){
        //Nettoyer les données
        $loginSignUp = sanitize($_POST['login']);
        $passwordSignUp = sanitize($_POST['password']);
        
        //Création d'une variable d'affichage pour tester
        $data =[];
        
        try{
            //Connection à la BDD
            $bdd = new PDO('mysql:host=localhost;dbname=theoRenaut','root','root',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

            $req = $bdd->prepare('SELECT u.id_user, u.login_user, u.password_user FROM user u JOIN rights r ON u.id_rights = r.id_rights WHERE u.login_user = ? LIMIT 1 ');

            $req->bindParam(1,$loginSignUp,PDO::PARAM_STR);

            //Execute la requête
            $req->execute();

            //Récupérer la réponse de la BDD
            $data = $req->fetch();

            //Vérification du MDP
            if ($data && password_verify($passwordSignUp, $data['password_user'])) {

                //Crétion de la session
                $_SESSION['user_id'] = $data['id_user'];
                $_SESSION['login'] = $data['login_user'];
                $_SESSION['rights_name'] = $data['name_rights'];

                //Redirection vers la page CMS
                header('location: cms.php');
                exit; //Arrête l'execution du script apres la redirection
            } else {
                $message = "Identifiant ou mot de passe incorrect";
            }

           
        }catch(EXCEPTION $error){
            die($error->getMessage());
        }
    } else{
        $message="Veuillez remplir tous les champs";
    }
}
print_r($_SESSION);




include '../views/view_header.php' ;
include '../views/view_login.php' ;
include '../views/view_footer.php' ;


?>
