<?php
/**
 * Controleur pour les visiteurs
 */
class ControleurVisiteur
{
    /**
     * Constructeur d'un controleur pour les visiteurs
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
                case NULL:
                    $this->accueil();
                    break;
                case 'sInscrire':
                    require("vues/inscription.php");
                    break;
                case 'ajoutListe':
                    require("vues/ajoutListe.php");
                    break;
                case 'veutInscrire':
                    $this->sInscrire();
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
                case 'modifieLaTache':
                    $this->modifierTache();
                    break;
                case 'modifierTache':
                    require("vues/modifierTache.php");
                    break;
                case 'ajouterTache':
                    require("vues/ajoutTache.php");
                    break;
                case 'ajouteLaTache':
                    $this->ajouterTache();
                    break;
                case 'tacheFaite':
                    $this->tacheFaite();
                    break;
                case 'afficherTaches':
                    $taches=$this->getTaches();
                    require("vues/liste.php");
                    break;
                case 'connexionEnCours':
                    $this->seConnecter();
                    break;
                case 'afficherListe':
                    break;
                case 'seConnecter':
                    require("vues/connexion.php");
                    break;
                default:
                    $VueErreur[] ="Erreur d'appel php";
                    require("vues/connexion.php");
            }
        }
        catch (PDOException $e)
        {
            // Si erreur BD
            $VueErreur[] =	"Erreur avec la base de donn??es ";
            require ('vues/erreur.php');

        }
        catch (Exception $e2)
        {
            $VueErreur[] =	"Erreur inattendue ";
            require ('vues/erreur.php');
        }
    }

    /**
     * @brief
     * @throws Exception
     */
    function accueil() {
        // Si la page n'est pas set, on prend la premi??re page
        if(!isset($_REQUEST["page"]) || empty($_REQUEST["page"])) {
            $page = 1;
        } else {
            // Si la validation a ??chou??e, on prend la premi??re page
            $page = Validation::validerIntPossitif($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
        }

        // Si le nombre d'??l??ment n'est pas set, on en prend par d??faut 10
        if(!isset($_GET["nbElements"]) || empty($_GET["nbElements"])) {
            $nbElements = 10;
        } else
        {
            // Si la validation a ??chou??, on prend 10 ??l??ments, sinon, le nombre renseign??
            $nbElements = Validation::validerIntPossitif($_GET["nbElements"]) ? $_GET["nbElements"] : 10;
        }

        $modele = new modeleVisiteur();

        if($modele->estConnecte()) {
            // R??cup??ration des listes de l'utilisateur.trice conn??ct??.e par le mod??le
            $modele=new modeleUtilisateur();
            $listes = $modele->getListesPriv();
        }else {
            $listes = $modele->getListes();
        }

        // Affichage de la vue
        require("vues/accueil.php");
    }

    /**
     * @brief permet ?? un visiteur de se connecter s'il a d??j?? un compte
     * @throws Exception
     */
    function seConnecter()
    {
        if(!isset($_REQUEST["pseudonyme"]) || !isset($_REQUEST["motDePasse"]))
        {
            throw new Exception("Erreur lors de la transmission des informations de connection");
        }
        //nettoyage des donn??es rentr??es par l'utilisateur
        $login = Validation::nettoyerString($_REQUEST["pseudonyme"]);
        $mdp = Validation::nettoyerString($_REQUEST["motDePasse"]);

        if(is_null($login) || is_null($mdp))
        {
            throw new ValueError("Veuillez entrer votre login ET votre mot de passe ");
        }
        $mdl = new modeleVisiteur();
        //appel au model
        $compte = $mdl->connexion($login, $mdp);
        if(!is_null($compte))
        {
            require_once("controleur/ControleurUtilisateur.php");
            new ControleurUtilisateur();
            $_REQUEST["action"] = "afficherListe";
        }
        else
        {
            throw new Exception("Erreur lors de la connexion au compte");
        }
    }

    /**
     * @throws Exception
     */
    function sInscrire()
    {
        if(!isset($_REQUEST["pseudonyme"]))
        {
            throw new Exception("Le pseudonyme doit ??tre renseign??");
        }
        if(empty($_REQUEST["pseudonyme"]))
        {
            throw new Exception("Le pseudonyme renseign?? est nul");
        }

        if(!isset($_REQUEST["mdp"]))
        {
            throw new Exception("Le mot de passe n'a pas ??t?? envoy?? au serveur");
        }
        if(empty($_REQUEST["mdp"]))
        {
            throw new Exception("Le mot de passe renseign?? est nul");
        }

        $pseudo = Validation::nettoyerString($_REQUEST["pseudonyme"]);
        $email=$_REQUEST["email"];
        if(is_null($pseudo))
        {
            throw new Exception("Le pseudonyme est nul");
        }
        $mdl = new modeleVisiteur();
        $mdl->sInscrire($pseudo,$email, $_REQUEST["mdp"]);

        $_REQUEST["action"] = "seConnecter";
        new ControleurVisiteur();
    }


    /**
     * @brief permet ?? un visiteur/utilisateur de creer une nouvelle To-Do List
     * @throws Exception
     */
    function ajoutListe()
    {
        $mdl = new modeleVisiteur();
        if(!isset($_REQUEST["nomNvleListe"]))
        {
            throw new Exception("La nouvelle liste doit avoir un nom!");
        }
        if(empty($_REQUEST["nomNvleListe"]))
        {
            throw new Exception("La liste ?? creer ne peut pas avoir un nom nul");
        }

        if (isset($_REQUEST["publique"])) {
            $pub=true;
        } else {
            $pub=false;
        }
        $nom = Validation::nettoyerString($_REQUEST["nomNvleListe"]);

        if(is_null($nom))
        {
            throw new Exception("Veuillez entrer un nom");
        }

        $mdl->creerListe($nom,$pub);

        $_REQUEST["action"]="accueil";
        require_once("controleur/ControleurVisiteur.php");
        new ControleurVisiteur();
    }

    /**
     * @brief permet ?? un visiteur/utilisateur de modifier une To-Do List
     * @throws Exception
     */
    function modifierListe()
    {
        $mdl = new modeleVisiteur();
        if(!isset($_REQUEST["liste"]))
        {
            throw new Exception("La liste n'existe pas");
        }
        if(empty($_REQUEST["liste"]))
        {
            throw new Exception("La liste ne peut pas ??tre nul");
        }
        if(!Validation::validerIntPossitif($_REQUEST["liste"]))
        {
            throw new Exception("L'ID de la liste doit ??tre positif");
        }

        if(!isset($_REQUEST["nomListe"]))
        {
            throw new Exception("Le nom doit exister");
        }
        if(empty($_REQUEST["nomListe"]))
        {
            throw new Exception("Le nouveau nom ne peut pas ??tre nul");
        }
        $nouveauNom = Validation::nettoyerString($_REQUEST["nomListe"]);
        if(is_null($nouveauNom))
        {
            throw new Exception("Le nom entr?? n'est pas correct");
        }

        // Modification du nom de la liste par le mod??le
        $mdl->modifierNomListe($_REQUEST["liste"], $nouveauNom);

        $_REQUEST["action"]="accueil";
        new ControleurVisiteur();
    }

    /**
     * @brief permet ?? un visiteur/utilisateur de voir les taches d'une To-Do List
     * @throws Exception
     */
    function getTaches(): array
    {
        $mdl = new modeleVisiteur();
        if(!isset($_REQUEST["liste"]))
        {
            throw new Exception("La liste n'existe pas");
        }
        if(empty($_REQUEST["liste"]))
        {
            throw new Exception("La liste ne peut pas ??tre nul");
        }
        if(!Validation::validerIntPossitif($_REQUEST["liste"]))
        {
            throw new Exception("L'ID de la liste doit ??tre positif");
        }

        // Modification du nom de la liste par le mod??le
        return $mdl->getTaches($_REQUEST["liste"]);
    }

    /**
     * @brief permet ?? un visiteur/utilisateur de supprimer une To-Do List s'il en a les droits
     * @throws Exception
     */
    function supprimerListe()
    {
        $mdl = new modeleVisiteur();
        if(!isset($_REQUEST["liste"]))
        {
            throw new Exception("La liste doit exister");
        }
        if(empty($_REQUEST["liste"]))
        {
            throw new Exception("La liste doit contenir une valeur");
        }

        if(!Validation::validerIntPossitif($_REQUEST["liste"]))
        {
            throw new Exception("L'ID de la liste doit ??tre positif");
        }

        $mdl->supprimerListe($_REQUEST["liste"]);

        // Redirection vers l'accueil
        $_REQUEST["action"] = "accueil";

        require_once("controleur/ControleurVisiteur.php");
        new ControleurVisiteur();
    }

    /**
     * @brief permet ?? un visiteur/utilisateur d'ajouter une t??che ?? une To-Do List
     * @throws Exception
     */
    function ajouterTache()
    {
        $mdl = new modeleVisiteur();

        if(!isset($_REQUEST["nomNvTache"]))
        {
            throw new Exception("La tache n'existe pas");
        }
        if(empty($_REQUEST["nomNvTache"]))
        {
            throw new Exception("Le nom de la nouvelle tache ne doit pas ??tre vide");
        }

        // Si le num??ro de la liste est vide ou n'est pas set, on l??ve une exception
        if(!isset($_REQUEST["liste"]))
        {
            throw new Exception("Le numero de liste doit exister");
        }
        if(empty($_REQUEST["liste"]))
        {
            throw new Exception("Le numero de liste doit ??tre d??finit");
        }

        // Validation des param??tres
        $liste = Validation::validerIntPossitif($_REQUEST["liste"]) ? $_REQUEST["liste"] : null;
        $nom = Validation::nettoyerString($_REQUEST["nomNvTache"]);

        // Verification des param??tre, si il y en a 1 qui vas pas, on l??ve une exception
        if(is_null($nom) || is_null($liste))
        {
            throw new Exception("Le nom, la liste ou le commentaire de la nouvelle tache contiennent des caract??rent ill??gales!");
        }

        // Cr??ation de la tache par le mod??le
        $mdl->creerTache($nom, $liste);

        $_REQUEST["action"] = "accueil";
        new ControleurVisiteur();
    }

    /**
     * @brief permet ?? un visiteur/utilisateur de modifier une tache
     * @throws Exception
     */
    function modifierTache()
    {
        $mdl = new modeleVisiteur();

        if(!isset($_REQUEST["tache"]))
        {
            throw new Exception("La tache doit exister");
        }
        if(empty($_REQUEST["tache"]))
        {
            throw new Exception("La tache doit contenir une valeur");
        }
        if(!Validation::validerIntPossitif($_REQUEST["tache"]))
        {
            throw new Exception("L'Id de la tache doit ??tre positif");
        }

        if(!isset($_REQUEST["nom"]))
        {
            throw new Exception("Le nom ne peut pas ??tre vide");
        }
        if(empty($_REQUEST["nom"]))
        {
            throw new Exception("Le nom doit contenir une valeur");
        }
        $nom = Validation::nettoyerString($_REQUEST["nom"]);
        if(is_null($nom))
        {
            throw new Exception("Le nom contient des valeurs ill??gales");
        }

        // Modification de la tache par le mod??le
        $mdl->modifierNomTache($_REQUEST["tache"], $nom);

        // Redirection vers l'affichage le la liste list
        $_REQUEST["action"] = "accueil";
        new ControleurVisiteur();
    }

    /**
     * @brief permet ?? un visiteur/utilisateur de changer le statut fait d'une t??che
     */
    function TacheFaite()
    {
        $mdl = new modeleVisiteur();
        $mdl->tacheFaite($_REQUEST["tache"]);
        $_REQUEST["action"] = "accueil";
        new ControleurVisiteur();
    }

    /**
     * @brief permet ?? un visiteur/utilisateur de supprimer une t??che d'une To-Do List
     * @throws Exception
     */
    function supprimerTache()
    {
        $mdl = new modeleVisiteur();

        // Si la tache est vide, pas set ou <= 0, on l??ve une exception
        if(!isset($_REQUEST["tache"]))
        {
            throw new Exception("Le parametre task doit exister");
        }
        if(empty($_REQUEST["tache"]))
        {
            throw new Exception("Le param??tre task doit contenire une valeur");
        }
        if(!Validation::validerIntPossitif($_REQUEST["tache"]))
        {
            throw new Exception("Le parametre task doit ??tre un entier strictement superieur ?? 0");
        }

        // Suppression de la tache par le mod??le
        $mdl->supprimerTache($_REQUEST["tache"]);

        $_REQUEST["action"] = "accueil";
        new ControleurVisiteur();
    }
}