<?php

namespace controller;

use model\ApiKey;

class KeyGenerator {

    private function renderTemplate($twig, $templateName, $chemin, $categories, $additionalParams = []) {
        $template = $twig->load($templateName);
        $breadcrumb = [
            ['href' => $chemin, 'text' => 'Accueil'],
            ['href' => $chemin . "/search", 'text' => "Recherche"]
        ];
        $params = array_merge(["breadcrumb" => $breadcrumb, "chemin" => $chemin, "categories" => $categories], $additionalParams);
        echo $template->render($params);
    }

    function show($twig, $chemin, $categories) {
        $this->renderTemplate($twig, "key-generator.html.twig", $chemin, $categories);
    }

    function generateKey($twig, $chemin, $categories, $name) {
        $trimmedName = str_replace(' ', '', $name);

        if ($trimmedName === '') {
            $this->renderTemplate($twig, "key-generator-error.html.twig", $chemin, $categories);
        } else {
            $uniqueKey = uniqid();
            $apiKey = new ApiKey();
            $apiKey->id_apikey = $uniqueKey;
            $apiKey->name_key = htmlentities($name);
            $apiKey->save();

            $this->renderTemplate($twig, "key-generator-result.html.twig", $chemin, $categories, ["key" => $uniqueKey]);
        }
    }
}

?>
