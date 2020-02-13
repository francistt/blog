<?php
require_once "view/view.php";
require_once "controller/chapter.php";
require_once "controller/comment.php";
/**
 * 
 */
class Front{
  
  public $html;
  private $title;
  function __construct($uri)
  {
    global $config;
    switch ($uri[0]) {
      case 'contact':
        $this->contact();
        break;
      case 'chapitre':
        $this->chapter(array_slice($uri, 1));
        break;
      case 'bio':
        $this->bio();
        break;
      
      default:
        $this->home();
        break;
    }
    $chapitre   = new Chapter(["lastChapter"=>true]);
    $vue = new View(
      [
        "{{ content }}"     => $this->html,
        "{{ title }}"       => $this->title,
        "{{ lastChapter }}" => $chapitre->lastChapter,
      ],
      "main"
    );
    $this->html = $vue->html;
  }

  private function contact(){
    
  }
  private function chapter($uri){
    $slug = $uri[0];
    $chapitre = new Chapter(["slug" => $slug]);
    $this->html        = $chapitre->html;
    $this->title       = $chapitre->title;
    $this->lastChapter = $chapitre->lastChapter;
    $commentData = [
      "chapitre" => $chapitre->id,
      "slug"     => $slug
    ];
    if (isset ($uri[1])){
      if ($uri[1] === "moderate"){
        $commentData["moderate"] = [
          "id"    => $uri[3],
          "state" => $uri[2]
        ];
      }
    }
    $commentaire = new Comment($commentData);
    $this->html .= $commentaire->html;
  }
  
  private function bio(){
    $this->html   = file_get_contents("template/bio.html");
    $this->title  = "Biographie de Jean Forteroche";
  }

  private function home(){
    $chapitre = new Chapter(["featured" => true]);
    $comments = new Comment([
      "chapitre" => $chapitre->id,
      "slug"     => $chapitre->slug
    ]);

    $chapitre->data["{{ numberOfComments }}"] = $comments->numberOfComments;
    $featuredView = new View($chapitre->data,"home");
    $this->html   = $featuredView->html;
    $this->title  = $chapitre->title;
  }
}