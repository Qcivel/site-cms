<?php
session_start();

//Accès 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

//Format des données envoyées
header("Content-Type: application/json; charset=UTF-8 ");

//Méthode autorisées
header("Access-Control-Allow-Methods:GET");

include '../models/model_todo.php';

//On Teste la méthode la requête pour savoir si elle correspond bien à la méthode autorisée (ici GET)
if($_SERVER['REQUEST_METHOD'] !== 'GET'){
    //Si la Requête n'utilise pas la méthode GET :
    //1) J'encode un code de réponse HTTP
    http_response_code(405); // 405 -> Code d'erreur pour : Méthode non autorisé

    //2) J'encode la réponse (qui est en tableau associatif) sous forme de JSON
    $json = json_encode(["message" => "Vous n'utilisez pas la bonne méthode GET"]);

    //3) J'envoie la réponse en effectuant son affichae
    echo $json;
    return;
}

//Connexion à la BDD
//Vérification si l'utilisateur est bien connecté 
if (isset($_SESSION['user_id'])){
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=theoRenaut;charset=utf8mb4', 'root', 'root', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]);

            //Instancier la class Picture 
            $taskModel = new Todo($bdd);

            //Appele de la fonction
            $taskList = $taskModel->getAllTask($_SESSION['user_id']);
        
            http_response_code(200);
            
            $response = json_encode($taskList);

            echo $response;
            exit;
        
    } catch (PDOException $e) {
        die("Erreur de connexion à la BDD : " . $e->getMessage());
    }
} else{
    $response = ["message" => "Utilisateur non connecté"];
        http_response_code(401);//Erreur non autorisé
}
echo json_encode($response);