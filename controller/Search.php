<?php

namespace controller;

use model\Annonce;
use model\Categorie;
use Twig\Environment;

class Search {

    function show(Environment $twig, array $menu, string $path, array $categories): void {
        $template = $twig->load("search.html.twig");
        $breadcrumb = [
            ['href' => $path, 'text' => 'Accueil'],
            ['href' => $path."/search", 'text' => "Recherche"]
        ];
        echo $template->render(["breadcrumb" => $breadcrumb, "chemin" => $path, "categories" => $categories]);
    }

    function research(array $params, Environment $twig, array $menu, string $path, array $categories): void {
        $template = $twig->load("index.html.twig");
        $breadcrumb = [
            ['href' => $path, 'text' => 'Accueil'],
            ['href' => $path."/search", 'text' => "Résultats de la recherche"]
        ];

        $keyword = str_replace(' ', '', $params['motclef']);
        $postalCode = str_replace(' ', '', $params['codepostal']);

        $query = Annonce::select();

        if (empty($keyword) && empty($postalCode) && 
            ($params['categorie'] === "Toutes catégories" || $params['categorie'] === "-----") &&
            $params['prix-min'] === "Min" && 
            ($params['prix-max'] === "Max" || $params['prix-max'] === "nolimit")) {
            $annonces = Annonce::all();
        } else {
            if (!empty($keyword)) {
                $query->where('description', 'like', '%'.$params['motclef'].'%');
            }

            if (!empty($postalCode)) {
                $query->where('ville', '=', $params['codepostal']);
            }

            if ($params['categorie'] !== "Toutes catégories" && $params['categorie'] !== "-----") {
                $categoryId = Categorie::select('id_categorie')
                    ->where('id_categorie', '=', $params['categorie'])
                    ->first()
                    ->id_categorie;
                $query->where('id_categorie', '=', $categoryId);
            }

            if ($params['prix-min'] !== "Min" && $params['prix-max'] !== "Max") {
                if ($params['prix-max'] !== "nolimit") {
                    $query->whereBetween('prix', [$params['prix-min'], $params['prix-max']]);
                } else {
                    $query->where('prix', '>=', $params['prix-min']);
                }
            } elseif ($params['prix-max'] !== "Max" && $params['prix-max'] !== "nolimit") {
                $query->where('prix', '<=', $params['prix-max']);
            } elseif ($params['prix-min'] !== "Min") {
                $query->where('prix', '>=', $params['prix-min']);
            }

            $annonces = $query->get();
        }

        echo $template->render(["breadcrumb" => $breadcrumb, "chemin" => $path, "annonces" => $annonces, "categories" => $categories]);
    }

}

?>
