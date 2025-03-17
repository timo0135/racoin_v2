<?php

namespace controller;

use model\ApiKey;
use Twig\Environment;

class KeyGenerator {

    private function renderTemplate(Environment $twig, string $templateName, string $chemin, array $categories, array $additionalParams = []): void {
        $template = $twig->load($templateName);
        $breadcrumb = [
            ['href' => $chemin, 'text' => 'Accueil'],
            ['href' => $chemin . "/search", 'text' => "Recherche"]
        ];
        $params = array_merge(["breadcrumb" => $breadcrumb, "chemin" => $chemin, "categories" => $categories], $additionalParams);
        echo $template->render($params);
    }

    public function show(Environment $twig, string $chemin, array $categories): void {
        $this->renderTemplate($twig, "key-generator.html.twig", $chemin, $categories);
    }

    public function generateKey(Environment $twig, string $chemin, array $categories, string $name): void {
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
