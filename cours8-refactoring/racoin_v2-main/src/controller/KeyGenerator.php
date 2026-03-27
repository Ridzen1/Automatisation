<?php

declare(strict_types=1);

namespace racoin\controller;

use racoin\model\ApiKey;
use Twig\Environment;

class KeyGenerator
{
    public function show(
        Environment $twig,
        array $menu,
        string $chemin,
        array $cat
    ): void {
        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
            [
                'href' => $chemin . '/search',
                'text' => 'Recherche',
            ],
        ];

        echo $twig->load('key-generator.html.twig')->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
            'categories' => $cat,
        ]);
    }

    public function generateKey(
        Environment $twig,
        array $menu,
        string $chemin,
        array $cat,
        string $nom
    ): void {
        $nomClean = trim(str_replace(' ', '', $nom));

        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
            [
                'href' => $chemin . '/search',
                'text' => 'Recherche',
            ],
        ];

        if ($nomClean === '') {
            echo $twig->load('key-generator-error.html.twig')->render([
                'breadcrumb' => $menu,
                'chemin'     => $chemin,
                'categories' => $cat,
            ]);
            return;
        }

        // Génération clé unique (plus propre que uniqid seul)
        $key = bin2hex(random_bytes(8));

        $apikey = new ApiKey();
        $apikey->id_apikey = $key;
        $apikey->name_key  = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');
        $apikey->save();

        echo $twig->load('key-generator-result.html.twig')->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
            'categories' => $cat,
            'key'        => $key,
        ]);
    }
}