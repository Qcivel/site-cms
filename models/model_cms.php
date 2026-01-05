<?php
//Définir la class Series
class Series {

//ATTRIBUTS
private ?int $id_series;
private ?string $title_series;
private ?int $number_series;
private ?int $id_user;
private ?PDO $bdd;

//CONSTRUCTEUR

public function __construct(?PDO $bdd){
    $this->bdd = $bdd;
}

//GETTER ET SETTER

public function getIdSeries(): ?int {return $this->id_series;}
public function setIdSeries(?int $id_series):self {$this->id_series = $id_series; return $this;}

public function getTitleSeries(): ?string {return $this->title_series;}
public function setTitleSeries(?string $title_series):self {$this->title_series = $title_series; return $this;}

public function getNumberSeries(): ?int {return $this->number_series;}
public function setNumberSeries(?int $number_series):self {$this->number_series = $number_series; return $this;}

public function getIdUser(): ?int {return $this->id_user;}
public function setIdUser(?int $id_user):self {$this->id_user = $id_user; return $this;}

public function getBdd(): ?PDO {return $this->bdd;}
public function setBdd(?PDO $bdd):self {$this->bdd = $bdd; return $this;}


//Création des méthodes

//Méthode qui insère des données dans la table Series
    public function addSeries(){

            $req = $this->bdd->prepare('INSERT INTO series (title_series, number_series, id_user) VALUES (?,?,?)');

            //Récupération des données via les getters
            $title_series = $this->getTitleSeries();
            $number_series = $this->getNumberSeries();
            $id_user = $this->getIdUser();

            //Binding des paramètres pour les marqueurs de position
            $req->bindParam(1,$title_series,PDO::PARAM_STR);
            $req->bindParam(2,$number_series,PDO::PARAM_INT);
            $req->bindParam(3,$id_user,PDO::PARAM_INT);

            //Executer la requête
            return $req->execute();

    }
    //Method pour afficher les séries
    public function displaySeriesById(int $userId):array{
        
            $req = $this->bdd->prepare('SELECT id_series, title_series, number_series FROM series WHERE id_user = :userId ORDER BY number_series ASC');

            $req->bindValue(':userId', $userId, PDO::PARAM_INT);
            $req->execute();

            return $req->fetchall(PDO::FETCH_ASSOC);
            
    }

    public function getNextSeries(int $userId):int {
        $req = $this->bdd->prepare('SELECT MAX(number_series) AS max_number FROM series WHERE id_user = :userId');

        $req->bindValue(':userId', $userId, PDO::PARAM_INT);
        $req->execute();

        $result = $req->fetch(PDO::FETCH_ASSOC);

        $max = $result['max_number'] ?? 0;

        return(int)$max+1;
    }

    //Method pour supprimer une serie
    public function deleteSerie (int $id_series):bool{
        //Requête pour récupérer l'url dans la bdd pour ensuite supprimer le fichier dans le dossier upload
        $select_req = $this->bdd->prepare('SELECT p.url_picture FROM picture p
        WHERE id_series = :id_series');

        $req = $this->bdd->prepare('DELETE FROM series WHERE id_series= :id_series');

        $select_req->bindValue(':id_series', $id_series, PDO::PARAM_INT);
        $req->bindValue(':id_series', $id_series, PDO::PARAM_INT);


        $select_req->execute();
        $data_url = $select_req->fetchAll(PDO::FETCH_ASSOC);

        if($data_url){
            foreach($data_url as $row){
                $data = $row['url_picture'];
                if(!empty($data) && file_exists($data)){
                    unlink($data);
                }
            }

        }
        return $req->execute();
    }
}




?>