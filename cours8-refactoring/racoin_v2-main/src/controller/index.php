<?php

declare(strict_types=1);

namespace Racoin\Controller;

use racoin\Model\Annonce;
use racoin\Model\Photo;
use racoin\Model\Annonceur;
use Twig\Environment;

class index
{
    private array $annonces = [];

    public function displayAllAnnonce(
        Environment $twig,
        array $menu,
        string $chemin,
        array $cat
    ): void {
        $template = $twig->load('index.html.twig');

        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
        ];

        $this->getAll($chemin);

        echo $template->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
            'categories' => $cat,
            'annonces'   => $this->annonces,
        ]);
    }

    public function getAll(string $chemin): void
    {
        $tmp = Annonce::with('Annonceur')
            ->orderBy('id_annonce', 'desc')
            ->take(12)
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
                $t->url_photo = '/img/noimg.png';
            }

            $annonceur = Annonceur::select('nom_annonceur')
                ->where('id_annonceur', '=', $t->id_annonceur)
                ->first();

            $t->nom_annonceur = $annonceur?->nom_annonceur;

            $annonces[] = $t;
        }

        $this->annonces = $annonces;
    }

    /**
     * @throws \Exception
     */
    public function displayException(
        Environment $twig,
        array $menu,
        string $chemin,
        array $cat
    ): void {
        throw new \Exception('Cette méthode déclenche une exception.');
    }
}