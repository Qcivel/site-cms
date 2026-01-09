<?php
//Définir la class Todo
class Todo {

    //ATTRIBUTS
    private ?int $id_todo;
    private ?string $title_todo;
    private ?string $text_todo;
    private ?int $importance_todo;
    private ?int $id_user;
    private ?PDO $bdd;

    //CONSTRUCTEUR

    public function __construct(?PDO $bdd){
        $this->bdd = $bdd;
    }

    //GETTER ET SETTER 

    public function getIdTodo(): ?int {return $this->id_todo;}
    public function setIdTodo(?int $id_todo):self {$this->id_todo = $id_todo; return $this;}

    public function getTitleTodo(): ?string {return $this->title_todo;}
    public function setTitleTodo(?string $title_todo): self {$this->title_todo = $title_todo; return $this;}

    public function getTextTodo(): ?string {return $this->text_todo;}
    public function setTextTodo(?string $text_todo): self {$this->text_todo = $text_todo; return $this;}

    public function getImportanceTodo(): ?int {return $this->importance_todo;}
    public function setImportanceTodo(?int $importance_todo): self {$this->importance_todo = $importance_todo; return $this;}

    public function getIdUser(): ?int {return $this->id_user;}
    public function setIdUser(?int $id_user):self {$this->id_user = $id_user; return $this;}

    public function getBdd(): ?PDO {return $this->bdd;}
    public function setBdd(?PDO $bdd):self {$this->bdd = $bdd; return $this;}


//Création des méthodes 

//Méthode pour créer une tâche
    public function addTodo ():bool{
        $req = $this->bdd->prepare('INSERT INTO todo (title_todo, text_todo, importance_todo, id_user) VALUES (:title_todo, :text_todo,:importance_todo,:id_user)');

        //Récupération des données via les GETTERS
        $title_todo = $this->getTitleTodo();
        $text_todo = $this->getTextTodo();
        $importance_todo = $this->getImportanceTodo();
        $id_user = $this->getIdUser();


        $req->bindValue(':title_todo', $title_todo, PDO::PARAM_STR);
        $req->bindValue(':text_todo', $text_todo, PDO::PARAM_STR);
        $req->bindValue(':importance_todo', $importance_todo, PDO::PARAM_INT);
        $req->bindValue(':id_user', $id_user, PDO::PARAM_INT);

        //Execution de la requete
        return $req->execute();

    }

//Méthode pour afficher les tâches 
    public function getAllTask(int $userId):array{
        $req = $this->bdd->prepare('SELECT title_todo,text_todo,importance_todo FROM todo WHERE id_user = :userId ORDER BY importance_todo DESC');

        $req->bindValue(':userId', $userId, PDO::PARAM_INT);

        $req->execute();

        return $req->fetchall(PDO::FETCH_ASSOC);
    }

//Méthode pour supprimer une tâche
    public function deleteTask(int $userId):bool{

        $req = $this->bdd->prepare('DELETE FROM todo WHERE id_todo = :id_todo');

        $req->bindValue(';id_todo', $id_todo, PDO::PARAM_INT);

        return $req->execute();

    }
}

?>