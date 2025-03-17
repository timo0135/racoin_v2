<?php

namespace controller;

use AllowDynamicProperties;
use model\Annonce;
use model\Annonceur;
use model\Departement;
use model\Photo;
use model\Categorie;

#[AllowDynamicProperties]
class ItemController {
    public function __construct() {
    }

    public function afficherItem($twig, $menu, $chemin, $annonceId, $categories): void {
        $annonce = Annonce::find($annonceId);
        if (!$annonce) {
            echo "404";
            return;
        }

        $menu = [
            ['href' => $chemin, 'text' => 'Accueil'],
            ['href' => "$chemin/cat/$annonceId", 'text' => Categorie::find($annonce->id_categorie)?->nom_categorie],
            ['href' => "$chemin/item/$annonceId", 'text' => $annonce->titre]
        ];

        $annonceur = Annonceur::find($annonce->id_annonceur);
        $departement = Departement::find($annonce->id_departement);
        $photos = Photo::where('id_annonce', '=', $annonceId)->get();

        $template = $twig->load("item.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $annonce,
            "annonceur" => $annonceur,
            "dep" => $departement->nom_departement,
            "photo" => $photos,
            "categories" => $categories
        ]);
    }

    public function supprimerItemGet($twig, $menu, $chemin, $annonceId) {
        $annonce = Annonce::find($annonceId);
        if (!$annonce) {
            echo "404";
            return;
        }

        $template = $twig->load("delGet.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $annonce
        ]);
    }

    public function supprimerItemPost($twig, $menu, $chemin, $annonceId, $categories) {
        $annonce = Annonce::find($annonceId);
        $isPasswordValid = password_verify($_POST["pass"], $annonce->mdp);

        if ($isPasswordValid) {
            Photo::where('id_annonce', '=', $annonceId)->delete();
            $annonce->delete();
        }

        $template = $twig->load("delPost.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $annonce,
            "pass" => $isPasswordValid,
            "categories" => $categories
        ]);
    }

    public function modifyGet($twig, $menu, $chemin, $annonceId) {
        $annonce = Annonce::find($annonceId);
        if (!$annonce) {
            echo "404";
            return;
        }

        $template = $twig->load("modifyGet.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $annonce
        ]);
    }

    public function modifyPost($twig, $menu, $chemin, $annonceId, $categories, $departements) {
        $annonce = Annonce::find($annonceId);
        $annonceur = Annonceur::find($annonce->id_annonceur);
        $categorieNom = Categorie::find($annonce->id_categorie)->nom_categorie;
        $departementNom = Departement::find($annonce->id_departement)->nom_departement;

        $isPasswordValid = password_verify($_POST["pass"], $annonce->mdp);

        $template = $twig->load("modifyPost.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $annonce,
            "annonceur" => $annonceur,
            "pass" => $isPasswordValid,
            "categories" => $categories,
            "departements" => $departements,
            "dptItem" => $departementNom,
            "categItem" => $categorieNom
        ]);
    }

    public function edit($twig, $menu, $chemin, $allPostVars, $annonceId) {
        date_default_timezone_set('Europe/Paris');

        function isEmail($email) {
            return preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$/i", $email);
        }

        $fields = [
            'nom' => 'Veuillez entrer votre nom',
            'email' => 'Veuillez entrer une adresse mail correcte',
            'phone' => 'Veuillez entrer votre numéro de téléphone',
            'ville' => 'Veuillez entrer votre ville',
            'departement' => 'Veuillez choisir un département',
            'categorie' => 'Veuillez choisir une catégorie',
            'title' => 'Veuillez entrer un titre',
            'description' => 'Veuillez entrer une description',
            'price' => 'Veuillez entrer un prix'
        ];

        $errors = [];
        foreach ($fields as $field => $errorMessage) {
            if (empty($_POST[$field]) || ($field === 'email' && !isEmail($_POST[$field])) || ($field !== 'email' && !is_numeric($_POST[$field]))) {
                $errors[$field] = $errorMessage;
            }
        }

        if (!empty($errors)) {
            $template = $twig->load("add-error.html.twig");
            echo $template->render([
                "breadcrumb" => $menu,
                "chemin" => $chemin,
                "errors" => $errors
            ]);
        } else {
            $annonce = Annonce::find($annonceId);
            $annonceur = Annonceur::find($annonce->id_annonceur);

            $annonceur->email = htmlentities($allPostVars['email']);
            $annonceur->nom_annonceur = htmlentities($allPostVars['nom']);
            $annonceur->telephone = htmlentities($allPostVars['phone']);
            $annonce->ville = htmlentities($allPostVars['ville']);
            $annonce->id_departement = $allPostVars['departement'];
            $annonce->prix = htmlentities($allPostVars['price']);
            $annonce->mdp = password_hash($allPostVars['psw'], PASSWORD_DEFAULT);
            $annonce->titre = htmlentities($allPostVars['title']);
            $annonce->description = htmlentities($allPostVars['description']);
            $annonce->id_categorie = $allPostVars['categorie'];
            $annonce->date = date('Y-m-d');

            $annonceur->save();
            $annonceur->annonce()->save($annonce);

            $template = $twig->load("modif-confirm.html.twig");
            echo $template->render([
                "breadcrumb" => $menu,
                "chemin" => $chemin
            ]);
        }
    }
}
