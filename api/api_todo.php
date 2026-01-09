<?php

//Accès 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

//Format des données envoyées
header("Content-Type: application/json; charset=UTF-8 ");

//Méthode autorisées
header("Access-Control-Allow-Methods:POST");

include '../models/model_todo.php';
include '../utils/functions.php';


if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    //Si la Requête n'utilise pas la méthode post :
    //1) J'encode un code de réponse HTTP
    http_response_code(405); // 405 -> Code d'erreur pour : Méthode non autorisé

    //2) J'encode la réponse (qui est en tableau associatif) sous forme de JSON
    $json = json_encode(["message" => "Vous n'utilisez pas la bonne méthode POST"]);

    //3) J'envoie la réponse en effectuant son affichae
    echo $json;
    return;
}

//Connexion à la bdd 
try{
    $bdd = new PDO('mysql:host=localhost;dbname=theoRenaut;charset=utf8mb4', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]);

    //Instancier la class Todo
    $todoModel = new Todo($bdd);

    // 1. Récupérer le contenu brut envoyé par le JS
    $json = file_get_contents("php://input");

    // 2. Transformer ce texte JSON en objet PHP utilisable
    $data = json_decode($json);
    
    //Hydratation de l'objet
    if(isset($data->title_todo) && isset($data->text_todo) && isset($data->importance_todo) && isset($data->id_user)) {

        if(!empty($data->title_todo) && !empty($data->text_todo) ){
            $data->title_todo =sanitize($data->title_todo ?? '');
            $data->text_todo =sanitize($data->text_todo ?? '');

            $todoModel->setTitleTodo($data->title_todo);
            $todoModel->setTextTodo($data->text_todo);
            $todoModel->setImportanceTodo($data->importance_todo);
            $todoModel->setIdUser($data->id_user);

            //Appele de la fonction
            if ($todoModel->addTodo() ){
                $response = [
                    "message" => "Tâche ajoutée avec succes",
                ];
                http_response_code(201);
            }else {
                $response = [
                    "message" => "la tâche n'a pas pu être ajouté",
                ];
                http_response_code(500); // Erreur serveur
            }
        }else{
            $response = ["message" => "Veuillez remplir le titre et le texte"];
            http_response_code(400); // 400 = Mauvaise requête
        }
    }else {
        // Données manquantes
        $response = ["message" => "Données incomplètes"];
        http_response_code(400);
    }

        echo json_encode($response);

} catch (PDOException $e) {
    die("Erreur de connexion à la BDD : " . $e->getMessage());
}

?>