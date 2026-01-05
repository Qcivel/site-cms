<?php

//Définir la class Picture

class Picture{
    //ATTRIBUTS
    private ?int $id_picture;
    private ?string $title_picture;
    private ?string $description_picture;
    private ?string $url_picture;
    private ?int $id_series;

    private ?PDO $bdd;


    //Constructeur
    public function __construct(?PDO $bdd){
    $this->bdd = $bdd;
}

//GETTER ET SETTER

public function getIdPicture(): ?int {return $this->id_picture;}
public function setIdPicture(?int $id_picture):self {$this->id_picture = $id_picture; return $this;}

public function getTitlePicture(): ?string {return $this->title_picture;}
public function setTitlePicture(?string $title_picture):self {$this->title_picture = $title_picture; return $this;}

public function getDescriptionPicture(): ?string {return $this->description_picture;}
public function setDescriptionPicture(?string $description_picture):self {$this->description_picture = $description_picture; return $this;}

public function getUrlPicture(): ?string {return $this->url_picture;}
public function setUrlPicture(?string $url_picture):self {$this->url_picture = $url_picture; return $this;}

public function getIdSeries(): ?int {return $this->id_series;}
public function setIdSeries(?int $id_series):self {$this->id_series = $id_series; return $this;}



//Création des méthodes

//Method pour insérer des photos dans la BDD

    public function addPicture ():bool{
        
        $req = $this->bdd->prepare('INSERT INTO picture (title_picture, description_picture, url_picture, id_series) VALUES (?,?,?,?)');

        //Récupération des données via les Getters
        $title_picture = $this->getTitlePicture();
        $description_picture =  $this->getDescriptionPicture();
        $url_picture = $this->getUrlPicture();
        $id_series = $this->getIdSeries();

        $req->bindParam(1,$title_picture,PDO::PARAM_STR);
        $req->bindParam(2,$description_picture,PDO::PARAM_STR);
        $req->bindParam(3,$url_picture,PDO::PARAM_STR);
        $req->bindParam(4,$id_series,PDO::PARAM_INT);

        return $req->execute();
        
    }


    public function getPicture(int $id_series):array{

        $req = $this->bdd->prepare('SELECT id_picture, title_picture,description_picture, url_picture, id_series FROM picture WHERE id_series = :id_series' );

        $req->bindValue(':id_series', $id_series, PDO::PARAM_INT);

        $req->execute();

        return $req->fetchall(PDO::FETCH_ASSOC);

    }



    public function deletePicture(int $id_picture):bool{
        $req = $this->bdd->prepare('DELETE FROM picture WHERE id_picture = :id_picture');

        //Requête pour récupérer l'url dans la bdd pour ensuite supprimer le fichier dans le dossier upload
        $select_req = $this->bdd->prepare('SELECT url_picture, id_series FROM picture WHERE id_picture = :id_picture');

        $select_req->bindValue(':id_picture', $id_picture, PDO::PARAM_INT);
        $req->bindValue(':id_picture', $id_picture, PDO::PARAM_INT);

        $select_req->execute();
        $picture_data = $select_req->fetch(PDO::FETCH_ASSOC);

        if($picture_data){
            $picture_path = $picture_data['url_picture'];
            $series_id_return = $picture_data['id_series'];
        
            if (!empty($picture_path) && file_exists($picture_path)){
                unlink($picture_path);
            }
        }
        return $req->execute();
    }

    public function getSeriesByPictureId(int $id_picture):int {
        $req = $this->bdd->prepare('SELECT id_series FROM picture WHERE id_picture = :id_picture');
        $req->bindValue(':id_picture', $id_picture, PDO::PARAM_INT);

        $req->execute();

        return $data = $req->fetchColumn();

        
    }

    //Méthode pour récupérer toute les photos de la bdd pour json

    public function getAllPicture():array{

        $req = $this->bdd->prepare('SELECT p.id_picture, p.title_picture,p.description_picture, p.url_picture, p.id_series, s.title_series FROM picture p 
        INNER JOIN series s ON p.id_series = s.id_series 
        ORDER BY id_picture ASC' );

        $req->execute();

        return $req->fetchall(PDO::FETCH_ASSOC);

    }

}
