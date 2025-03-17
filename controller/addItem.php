<?php

namespace controller;

use model\Annonce;
use model\Annonceur;
use Twig\Environment;

class addItem
{
    public function addItemView(Environment $twig, array $menu, string $chemin, array $categories, array $departements): void
    {
        $template = $twig->load("add.html.twig");
        echo $template->render([
            "breadcrumb"   => $menu,
            "chemin"       => $chemin,
            "categories"   => $categories,
            "departements" => $departements
        ]);
    }

    private function isEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function addNewItem(Environment $twig, array $menu, string $chemin, array $allPostVars): void
    {
        date_default_timezone_set('Europe/Paris');

        $fields = [
            'nom' => 'Veuillez entrer votre nom',
            'email' => 'Veuillez entrer une adresse mail correcte',
            'phone' => 'Veuillez entrer votre numéro de téléphone',
            'ville' => 'Veuillez entrer votre ville',
            'departement' => 'Veuillez choisir un département',
            'categorie' => 'Veuillez choisir une catégorie',
            'title' => 'Veuillez entrer un titre',
            'description' => 'Veuillez entrer une description',
            'price' => 'Veuillez entrer un prix',
            'psw' => 'Les mots de passes ne sont pas identiques',
            'confirm-psw' => 'Les mots de passes ne sont pas identiques'
        ];

        $errors = [];
        foreach ($fields as $field => $errorMessage) {
            if (empty($allPostVars[$field]) || ($field === 'email' && !$this->isEmail($allPostVars[$field])) || ($field === 'phone' && !is_numeric($allPostVars[$field])) || (($field === 'departement' || $field === 'categorie' || $field === 'price') && !is_numeric($allPostVars[$field])) || ($field === 'psw' && $allPostVars['psw'] !== $allPostVars['confirm-psw'])) {
                $errors[$field] = $errorMessage;
            }
        }

        if (!empty($errors)) {
            $template = $twig->load("add-error.html.twig");
            echo $template->render([
                "breadcrumb" => $menu,
                "chemin"     => $chemin,
                "errors"     => $errors
            ]);
        } else {
            $annonceur = new Annonceur();
            $annonceur->email = htmlentities($allPostVars['email']);
            $annonceur->nom_annonceur = htmlentities($allPostVars['nom']);
            $annonceur->telephone = htmlentities($allPostVars['phone']);

            $annonce = new Annonce();
            $annonce->ville = htmlentities($allPostVars['ville']);
            $annonce->id_departement = (int)$allPostVars['departement'];
            $annonce->prix = htmlentities($allPostVars['price']);
            $annonce->mdp = password_hash($allPostVars['psw'], PASSWORD_DEFAULT);
            $annonce->titre = htmlentities($allPostVars['title']);
            $annonce->description = htmlentities($allPostVars['description']);
            $annonce->id_categorie = (int)$allPostVars['categorie'];
            $annonce->date = date('Y-m-d');

            $annonceur->save();
            $annonceur->annonce()->save($annonce);

            $template = $twig->load("add-confirm.html.twig");
            echo $template->render([
                "breadcrumb" => $menu,
                "chemin"     => $chemin
            ]);
        }
    }
}
