<?php

namespace App\Services;

//recherche de l'index du tableau correspondant à l'id de la tâche

class IndexArrayUriService
{
    public static function search(int $idTaskTest, array $arrayUri):int
    {
        foreach($arrayUri as $uri) {
          //récupération du numéro id dans l'uri
          $int = (int) filter_var($uri, FILTER_SANITIZE_NUMBER_INT);
          //on vérifie si id à tester existe bien
          if($idTaskTest === $int){
            //récupération de l'index de l'uri 
            $indexUri = array_search($uri, $arrayUri);
          }
        }
        return $indexUri;
    }
}
