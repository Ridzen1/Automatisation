<?php

declare(strict_types=1);

namespace Racoin\Controller;

use racoin\Model\Categorie;
use racoin\Model\Annonce;
use racoin\Model\Photo;
use racoin\Model\Annonceur;
use Twig\Environment;

class getCategorie
{
    private array $annonces = [];

    public function getCategories(): array
    {
        return Categorie::orderBy('nom_categorie')->get()->toArray();
    }

    public function getCategorieContent(string $chemin, int $n): void
    {
        $tmp = Annonce::with('Annonceur')
            ->orderBy('id_annonce', 'desc')
            ->where('id_categorie', '=', $n)
            ->get();

        $annonces = [];

        foreach ($tmp as $t) {
            $t->nb_photo = Photo::where('id_annonce', '=', $t->id_annonce)->count();

            if ($t->nb_photo > 0) {
                $photo = Photo::select('url_photo')
                    ->where('id_annonce', '=', $t->id_annonce)
                    ->first();

                $t->url_photo = $photo?->url_photo;
            } else {
                $t->url_photo = $chemin . '/img/noimg.png';
            }

            $annonceur = Annonceur::select('nom_annonceur')
                ->where('id_annonceur', '=', $t->id_annonceur)
                ->first();

            $t->nom_annonceur = $annonceur?->nom_annonceur;

            $annonces[] = $t;
        }

        $this->annonces = $annonces;
    }

    public function displayCategorie(
        Environment $twig,
        array $menu,
        string $chemin,
        array $cat,
        int $n
    ): void {
        $template = $twig->load('index.html.twig');

        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
            [
                'href' => $chemin . '/cat/' . $n,
                'text' => Categorie::find($n)?->nom_categorie,
            ],
        ];

        $this->getCategorieContent($chemin, $n);

        echo $template->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
            'categories' => $cat,
            'annonces'   => $this->annonces,
        ]);
    }
}