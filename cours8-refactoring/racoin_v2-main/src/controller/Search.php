<?php

declare(strict_types=1);

namespace Racoin\Controller;

use Racoin\Model\Annonce;
use Racoin\Model\Categorie;
use Twig\Environment;

class Search
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

        echo $twig->load('search.html.twig')->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
            'categories' => $cat,
        ]);
    }

    public function research(
        array $array,
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
                'text' => 'Résultats de la recherche',
            ],
        ];

        $mc = trim(str_replace(' ', '', $array['motclef'] ?? ''));
        $cp = trim(str_replace(' ', '', $array['codepostal'] ?? ''));

        $query = Annonce::query();

        $emptySearch =
            $mc === '' &&
            $cp === '' &&
            (($array['categorie'] ?? '') === 'Toutes catégories' || ($array['categorie'] ?? '') === '-----') &&
            ($array['prix-min'] ?? 'Min') === 'Min' &&
            (($array['prix-max'] ?? 'Max') === 'Max' || ($array['prix-max'] ?? '') === 'nolimit');

        if ($emptySearch) {
            $annonces = Annonce::all();
        } else {

            if ($mc !== '') {
                $query->where('description', 'like', '%' . $mc . '%');
            }

            if ($cp !== '') {
                $query->where('ville', '=', $cp);
            }

            $categorie = $array['categorie'] ?? null;

            if ($categorie && $categorie !== 'Toutes catégories' && $categorie !== '-----') {
                $catId = Categorie::where('id_categorie', '=', $categorie)
                    ->value('id_categorie');

                if ($catId) {
                    $query->where('id_categorie', '=', $catId);
                }
            }

            $prixMin = $array['prix-min'] ?? 'Min';
            $prixMax = $array['prix-max'] ?? 'Max';

            if ($prixMin !== 'Min' && $prixMax !== 'Max') {

                if ($prixMax !== 'nolimit') {
                    $query->whereBetween('prix', [$prixMin, $prixMax]);
                } else {
                    $query->where('prix', '>=', $prixMin);
                }

            } elseif ($prixMax !== 'Max' && $prixMax !== 'nolimit') {
                $query->where('prix', '<=', $prixMax);
            } elseif ($prixMin !== 'Min') {
                $query->where('prix', '>=', $prixMin);
            }

            $annonces = $query->get();
        }

        echo $twig->load('index.html.twig')->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
            'annonces'   => $annonces,
            'categories' => $cat,
        ]);
    }
}