<?php
require("controleur/ControleurVisiteur.php");
/**
 * Controleur pour les utilisateurs
 */
class ControleurUtilisateur extends ControleurVisiteur
{
    /**
     * Constructeur d'un controleur pour les utilisateurs
     */
    function __construct()
    {
        try {
            if(!isset($_REQUEST["action"]))
            {
                $action = NULL;
            }
            else
            {
                $action = Validation::nettoyerString($_REQUEST["action"]);
            }
            switch ($action) {
                case 'accueil':
                case 'afficherListe':
                case 'seConnecter':
                case 'connexionEnCours':
                case NULL:
                    $this->afficherListe();
                    break;
                case 'sInscrire':
                    $this->sInscrire();
                    break;
                case 'ajoutListe':
                    require("vues/ajoutListe.php");
                    break;
                case 'ajouteLaListe':
                    $this->ajoutListe();
                    break;
                case 'modifierListe':
                    require("vues/modifierListe.php");
                    break;
                case 'modifieLaListe':
                    $this->modifierListe();
                    break;
                case 'supprimerListe':
                    $this->supprimerListe();
                    break;
                case 'supprimerTache':
                    $this->supprimerTache();
                    break;
                case 'modifierTache':
                    $this->modifierTache();
                    break;
                case 'ajouterTache':
                    $this->ajouterTache();
                    break;
                case 'tacheFaite':
                    $this->tacheFaite();
                    break;
                case 'afficherTaches':
                    require("vues/liste.php");
                    break;
                case 'deconnexion':
                    $this->deconnexion();
                    break;
                default:
                    $VueErreur[] ="Erreur d'appel php";
                    require("vues/connexion.php");
            }
        } catch (Exception $e) {
            require("vues/erreur.php");
        }
    }

    /**
     * @brief permet ?? un utilisateur de se d??connecter de sa session
     */
    function deconnexion()
    {
        $mdl = new ModeleUtilisateur();

        // Destruction de la session par le modele
        $mdl->deconnexion();

        // Redirection vers la page de connection
        new ControleurVisiteur();
    }

    /**
     * @brief permet ?? un utilisateur de voir ses To-Do List priv??es
     * @throws Exception
     */
    function afficherListe()
    {
        $modele=new modeleUtilisateur();
        $listes = $modele->getListesPriv();
        require("vues/accueil.php");
    }
}
