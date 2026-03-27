<?php

declare(strict_types=1);

namespace Racoin\Controller;

use Racoin\Model\Annonce;
use Racoin\Model\Annonceur;
use Racoin\Model\Photo;
use Twig\Environment;

class ViewAnnonceur
{
    public function afficherAnnonceur(
        Environment $twig,
        array $menu,
        string $chemin,
        int $n,
        array $cat
    ): void {
        $annonceur = Annonceur::find($n);

        if (!$annonceur) {
            echo '404';
            return;
        }

        $tmp = Annonce::where('id_annonceur', '=', $n)->get();

        $annonces = [];

        foreach ($tmp as $a) {
            $a->nb_photo = Photo::where('id_annonce', '=', $a->id_annonce)->count();

            if ($a->nb_photo > 0) {
                $photo = Photo::select('url_photo')
                    ->where('id_annonce', '=', $a->id_annonce)
                    ->first();

                $a->url_photo = $photo?->url_photo;
            } else {
                $a->url_photo = $chemin . '/img/noimg.png';
            }

            $annonces[] = $a;
        }

        echo $twig->load('annonceur.html.twig')->render([
            'nom'        => $annonceur,
            'chemin'     => $chemin,
            'annonces'   => $annonces,
            'categories' => $cat,
        ]);
    }
}