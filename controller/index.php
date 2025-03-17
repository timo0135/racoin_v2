<?php

namespace controller;

use model\Annonce;
use model\Photo;
use model\Annonceur;
use Twig\Environment;

class IndexController
{
    protected array $annonces = [];

    public function displayAllAnnonces(Environment $twig, array $menu, string $chemin, array $categories): void
    {
        $template = $twig->load("index.html.twig");
        $menu = [
            [
                'href' => $chemin,
                'text' => 'Accueil'
            ],
        ];

        $this->loadAllAnnonces($chemin);
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "categories" => $categories,
            "annonces"   => $this->annonces
        ]);
    }

    public function loadAllAnnonces(string $chemin): void
    {
        $annonces = Annonce::with("Annonceur")->orderBy('id_annonce', 'desc')->take(12)->get();
        foreach ($annonces as $annonce) {
            $annonce->nb_photo = Photo::where("id_annonce", $annonce->id_annonce)->count();
            $annonce->url_photo = $annonce->nb_photo > 0 
                ? Photo::where("id_annonce", $annonce->id_annonce)->first()->url_photo 
                : '/img/noimg.png';
            $annonce->nom_annonceur = Annonceur::where("id_annonceur", $annonce->id_annonceur)->first()->nom_annonceur;
            $this->annonces[] = $annonce;
        }
    }
}
