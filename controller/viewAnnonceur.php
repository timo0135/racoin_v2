<?php
/**
 * Created by PhpStorm.
 * User: ponicorn
 * Date: 26/01/15
 * Time: 00:25
 */

namespace controller;

use model\Annonce;
use model\Annonceur;
use model\Photo;

class ViewAnnonceur {
    public function __construct() {
    }

    public function afficherAnnonceur($twig, $menu, $chemin, $annonceurId, $categories) {
        $annonceur = Annonceur::find($annonceurId);
        if (!$annonceur) {
            echo "404";
            return;
        }

        $annonces = Annonce::where('id_annonceur', '=', $annonceurId)->get()->map(function ($annonce) use ($chemin) {
            $annonce->nb_photo = Photo::where('id_annonce', '=', $annonce->id_annonce)->count();
            $annonce->url_photo = $annonce->nb_photo > 0 
                ? Photo::select('url_photo')->where('id_annonce', '=', $annonce->id_annonce)->first()->url_photo 
                : $chemin . '/img/noimg.png';
            return $annonce;
        });

        $template = $twig->load("annonceur.html.twig");
        echo $template->render([
            'nom' => $annonceur,
            'chemin' => $chemin,
            'annonces' => $annonces,
            'categories' => $categories
        ]);
    }
}
