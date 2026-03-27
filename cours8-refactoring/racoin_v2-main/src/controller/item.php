<?php

declare(strict_types=1);

namespace Racoin\Controller;

use racoin\Model\Annonce;
use racoin\Model\Annonceur;
use racoin\Model\Departement;
use racoin\Model\Photo;
use racoin\Model\Categorie;
use Twig\Environment;

class Item
{
    private ?Annonce $annonce = null;
    private ?Annonceur $annonceur = null;

    public function afficherItem(
        Environment $twig,
        array $menu,
        string $chemin,
        int $n,
        array $cat
    ): void {
        $this->annonce = Annonce::find($n);

        if (!$this->annonce) {
            echo '404';
            return;
        }

        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil',
            ],
            [
                'href' => $chemin . '/cat/' . $n,
                'text' => Categorie::find($this->annonce->id_categorie)?->nom_categorie,
            ],
            [
                'href' => $chemin . '/item/' . $n,
                'text' => $this->annonce->titre,
            ],
        ];

        $this->annonceur = Annonceur::find($this->annonce->id_annonceur);
        $departement = Departement::find($this->annonce->id_departement);
        $photos = Photo::where('id_annonce', '=', $n)->get();

        echo $twig->load('item.html.twig')->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
            'annonce'    => $this->annonce,
            'annonceur'  => $this->annonceur,
            'dep'        => $departement?->nom_departement,
            'photo'      => $photos,
            'categories' => $cat,
        ]);
    }

    public function supprimerItemGet(
        Environment $twig,
        array $menu,
        string $chemin,
        int $n
    ): void {
        $this->annonce = Annonce::find($n);

        if (!$this->annonce) {
            echo '404';
            return;
        }

        echo $twig->load('delGet.html.twig')->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
            'annonce'    => $this->annonce,
        ]);
    }

    public function supprimerItemPost(
        Environment $twig,
        array $menu,
        string $chemin,
        int $n,
        array $cat
    ): void {
        $this->annonce = Annonce::find($n);

        if (!$this->annonce) {
            echo '404';
            return;
        }

        $reponse = false;

        if (isset($_POST['pass']) && password_verify($_POST['pass'], $this->annonce->mdp)) {
            $reponse = true;

            Photo::where('id_annonce', '=', $n)->delete();
            $this->annonce->delete();
        }

        echo $twig->load('delPost.html.twig')->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
            'annonce'    => $this->annonce,
            'pass'       => $reponse,
            'categories' => $cat,
        ]);
    }

    public function modifyGet(
        Environment $twig,
        array $menu,
        string $chemin,
        int $id
    ): void {
        $this->annonce = Annonce::find($id);

        if (!$this->annonce) {
            echo '404';
            return;
        }

        echo $twig->load('modifyGet.html.twig')->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
            'annonce'    => $this->annonce,
        ]);
    }

    public function modifyPost(
        Environment $twig,
        array $menu,
        string $chemin,
        int $n,
        array $cat,
        array $dpt
    ): void {
        $this->annonce = Annonce::find($n);

        if (!$this->annonce) {
            echo '404';
            return;
        }

        $this->annonceur = Annonceur::find($this->annonce->id_annonceur);

        $categItem = Categorie::find($this->annonce->id_categorie)?->nom_categorie;
        $dptItem   = Departement::find($this->annonce->id_departement)?->nom_departement;

        $reponse = isset($_POST['pass']) && password_verify($_POST['pass'], $this->annonce->mdp);

        echo $twig->load('modifyPost.html.twig')->render([
            'breadcrumb'   => $menu,
            'chemin'       => $chemin,
            'annonce'      => $this->annonce,
            'annonceur'    => $this->annonceur,
            'pass'         => $reponse,
            'categories'   => $cat,
            'departements' => $dpt,
            'dptItem'      => $dptItem,
            'categItem'    => $categItem,
        ]);
    }

    public function edit(
        Environment $twig,
        array $menu,
        string $chemin,
        array $allPostVars,
        int $id
    ): void {
        date_default_timezone_set('Europe/Paris');

        // validation simple PHP 8 (remplace la fonction interne inutile)
        $isEmail = static fn(string $email): bool =>
            (bool) filter_var($email, FILTER_VALIDATE_EMAIL);

        $nom         = trim($_POST['nom'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $ville       = trim($_POST['ville'] ?? '');
        $departement = trim($_POST['departement'] ?? '');
        $categorie   = trim($_POST['categorie'] ?? '');
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = trim($_POST['price'] ?? '');

        $errors = [];

        if ($nom === '') {
            $errors[] = 'Veuillez entrer votre nom';
        }
        if (!$isEmail($email)) {
            $errors[] = 'Veuillez entrer une adresse mail correcte';
        }
        if ($phone === '' || !is_numeric($phone)) {
            $errors[] = 'Veuillez entrer votre numéro de téléphone';
        }
        if ($ville === '') {
            $errors[] = 'Veuillez entrer votre ville';
        }
        if (!is_numeric($departement)) {
            $errors[] = 'Veuillez choisir un département';
        }
        if (!is_numeric($categorie)) {
            $errors[] = 'Veuillez choisir une catégorie';
        }
        if ($title === '') {
            $errors[] = 'Veuillez entrer un titre';
        }
        if ($description === '') {
            $errors[] = 'Veuillez entrer une description';
        }
        if ($price === '' || !is_numeric($price)) {
            $errors[] = 'Veuillez entrer un prix';
        }

        if ($errors !== []) {
            echo $twig->load('add-error.html.twig')->render([
                'breadcrumb' => $menu,
                'chemin'     => $chemin,
                'errors'     => $errors,
            ]);
            return;
        }

        $this->annonce = Annonce::find($id);
        if (!$this->annonce) {
            echo '404';
            return;
        }

        $this->annonceur = Annonceur::find($this->annonce->id_annonceur);

        $this->annonceur->email         = htmlentities($allPostVars['email']);
        $this->annonceur->nom_annonceur = htmlentities($allPostVars['nom']);
        $this->annonceur->telephone     = htmlentities($allPostVars['phone']);

        $this->annonce->ville          = htmlentities($allPostVars['ville']);
        $this->annonce->id_departement = $allPostVars['departement'];
        $this->annonce->prix           = htmlentities($allPostVars['price']);
        $this->annonce->mdp            = password_hash($allPostVars['psw'], PASSWORD_DEFAULT);
        $this->annonce->titre          = htmlentities($allPostVars['title']);
        $this->annonce->description    = htmlentities($allPostVars['description']);
        $this->annonce->id_categorie   = $allPostVars['categorie'];
        $this->annonce->date           = date('Y-m-d');

        $this->annonceur->save();
        $this->annonceur->annonce()->save($this->annonce);

        echo $twig->load('modif-confirm.html.twig')->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
        ]);
    }
}