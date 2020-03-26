<?php
require_once "model/model.php";

class ChapterModel extends Model{

	public $content;
	public $date;
	public $id;
	public $numeroChapitre;
	public $slug;
	public $title;

/**
 * __construct permet de définir quelles sont les actions à faire pour créer une itération unique de la classe en fonction des arguments donnés 
 * @param Array   $args   soit un id, soit un slug, soit un tableau "save" ou "update" contenant les données à enregistrer
 */
	function __construct($args){
		parent::__construct();
		extract($args);
		if ( isset($id)       ) return $this->readChapterFromId($id);
		if ( isset($slug)     ) return $this->readChapterFromSlug($slug);
		if ( isset($list)     ) return $this->getListChapters($list);
		if ( isset($featured) ) return $this->readFeaturedPost($featured);
		if ( isset($update)   ) return $this->updatePost($update);
		if (isset($save)){
			//requete pour enregister les données
			$req = $sql->prepare('INSERT INTO chapters (numeroChapitre, title, content)');
			$req->execute(array(
				'numeroChapitre' => $numeroChapitre,
				'title' => $title,
				'content' => $content,	
			));
		}
		if (isset($delete)){
        $req = $sql->prepare('DELETE FROM chapters WHERE id = ?');
        $req->execute(array($id));
        //return $suppr;
		}


	}


	private function readChapterFromId($id){
		$sql= "SELECT * FROM chapters WHERE id = '$id'";
		$request = $this->query($sql); //fonction dans la classe Model
		$this->checkSucced($request,"hydrate");
	}


	private function readChapterFromSlug($slug){
		$sql = "SELECT * FROM chapters WHERE slug = '$slug'";
		$request = $this->query($sql); //fonction dans la classe Model
		$this->checkSucced($request,"hydrate");
	}

	private function getListChapters($list){
		$sql = "SELECT `numeroChapitre` AS `{{ numeroChapitre }}` ,`slug` AS `{{ slug }}`,`title` AS `{{ titre }}` FROM `chapters` ORDER BY numeroChapitre DESC limit $list";
		$request = $this->query($sql,true);
		$this->slugList = $request["data"];
	}

	private function readFeaturedPost($featured){
				// $sql= "SELECT * FROM `chapters` ORDER BY date DESC limit 1";
		$sql= "SELECT title AS '{{ title }}', DATE_FORMAT(date, '%d-%m-%Y') AS '{{ date }}', slug AS '{{ slug }}', content AS '{{ content }}' FROM `chapters` ORDER BY date DESC limit 1";
		$request = $this->query($sql);
		$this->data	= $request["data"];
		$sql = "SELECT title, id, slug FROM `chapters` ORDER BY date DESC limit 1";
		$request = $this->query($sql);
		$this->checkSucced($request,"hydrate");
	}
	private function updatePost($update){
		$sql = "UPDATE `chapters` SET `title` = ':titre', `content` = ':chapitre', `date` = NOW, `slug` = ':slug' WHERE `chapters`.`id` = ':id'";

		try{
			$request = $this->db->prepare($sql);
    	$request->execute($update);
    	$this->succeed = true;
		}
		catch (Exception $e) {
			$this->succeed = false;
		}
	}
}
			//requete pour mettre à jour les données