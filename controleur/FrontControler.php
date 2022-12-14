<?php
require("config/Validation.php");
require("controleur/ControleurUtilisateur.php");
require("modele/modeleUtilisateur.php");
require("config/config.php");

class FrontControler{
    public function start(){
        $actions = array(
            "Utilisateur" => [
                "deconnexion"
            ],
            "Visiteur" => [
                "seConnecter", "sInscrire", "accueil", "connexionEnCours",
                "ajoutListe", "modifierListe", "afficherListe", "supprimerListe", "veutInscrire",
                "supprimerTache", "modifierTache", "ajouterTache","tacheFaite","ajouteLaListe","modifieLaListe","afficherTaches"
            ]
        );
        session_start();
        $modele = new modeleVisiteur();
        $action = Validation::nettoyerString($_GET["action"] ?? "");
        $utilisateur=$modele->estConnecte();
        if(in_array($action,$actions['Utilisateur'])) {
            if ($utilisateur == null) {
                require("vues/connexion.php");
                echo "<br>ERREUR : Vous n'êtes pas connecté, veuillez vous connecter pour accèder à cette fonctionnalité";
            } else{
                new ControleurUtilisateur();
            }
        }else{
            new ControleurVisiteur();
        }
    }
}
