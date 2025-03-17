<?php

namespace controller;

use model\Categorie;
use model\Annonce;
use model\Photo;
use model\Annonceur;

class CategorieController {

    protected $annonces = [];

    public function getCategories() {
        return Categorie::orderBy('nom_categorie')->get()->toArray();
    }

    public function getCategorieContent($chemin, $categorieId) {
        $annonces = Annonce::with("Annonceur")
            ->orderBy('id_annonce', 'desc')
            ->where('id_categorie', $categorieId)
            ->get();

        foreach ($annonces as $annonce) {
            $annonce->nb_photo = Photo::where("id_annonce", $annonce->id_annonce)->count();
            $annonce->url_photo = $annonce->nb_photo > 0 
                ? Photo::where("id_annonce", $annonce->id_annonce)->first()->url_photo 
                : $chemin . '/img/noimg.png';
            $annonce->nom_annonceur = Annonceur::where("id_annonceur", $annonce->id_annonceur)->first()->nom_annonceur;
        }

        $this->annonces = $annonces;
    }

    public function displayCategorie($twig, $chemin, $categorieId) {
        $template = $twig->load("index.html.twig");
        $breadcrumb = [
            ['href' => $chemin, 'text' => 'Accueil'],
            ['href' => $chemin . "/cat/" . $categorieId, 'text' => Categorie::find($categorieId)->nom_categorie]
        ];

        $categories = $this->getCategories();
        $this->getCategorieContent($chemin, $categorieId);

        echo $template->render([
            "breadcrumb" => $breadcrumb,
            "chemin" => $chemin,
            "categories" => $categories,
            "annonces" => $this->annonces
        ]);
    }
}
