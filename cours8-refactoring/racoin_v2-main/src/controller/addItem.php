<?php

declare(strict_types=1);

namespace Racoin\Controller;

use Racoin\Model\Annonce;
use Racoin\Model\Annonceur;
use Twig\Environment;

class AddItem
{
    public function addItemView(
        Environment $twig,
        array $menu,
        string $chemin,
        array $cat,
        array $dpt
    ): void {
        $template = $twig->load('add.html.twig');

        echo $template->render([
            'breadcrumb'   => $menu,
            'chemin'       => $chemin,
            'categories'   => $cat,
            'departements' => $dpt,
        ]);
    }

    private function isEmail(string $email): bool
    {
        return (bool) preg_match(
            "/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(...))$/i",
            $email
        );
    }

    public function addNewItem(
        Environment $twig,
        array $menu,
        string $chemin,
        array $allPostVars
    ): void {
        date_default_timezone_set('Europe/Paris');

        // Récupération sécurisée
        $nom              = trim($allPostVars['nom'] ?? '');
        $email            = trim($allPostVars['email'] ?? '');
        $phone            = trim($allPostVars['phone'] ?? '');
        $ville            = trim($allPostVars['ville'] ?? '');
        $departement      = trim($allPostVars['departement'] ?? '');
        $categorie        = trim($allPostVars['categorie'] ?? '');
        $title            = trim($allPostVars['title'] ?? '');
        $description      = trim($allPostVars['description'] ?? '');
        $price            = trim($allPostVars['price'] ?? '');
        $password         = trim($allPostVars['psw'] ?? '');
        $password_confirm = trim($allPostVars['confirm-psw'] ?? '');

        // Tableau d'erreurs
        $errors = [];

        if ($nom === '') {
            $errors[] = 'Veuillez entrer votre nom';
        }

        if (!$this->isEmail($email)) {
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

        if ($password === '' || $password_confirm === '' || $password !== $password_confirm) {
            $errors[] = 'Les mots de passes ne sont pas identiques';
        }

        // Gestion des erreurs
        if (!empty($errors)) {
            $template = $twig->load('add-error.html.twig');

            echo $template->render([
                'breadcrumb' => $menu,
                'chemin'     => $chemin,
                'errors'     => $errors,
            ]);

            return;
        }

        // Création des objets
        $annonce   = new Annonce();
        $annonceur = new Annonceur();

        $annonceur->email         = htmlentities($allPostVars['email']);
        $annonceur->nom_annonceur = htmlentities($allPostVars['nom']);
        $annonceur->telephone     = htmlentities($allPostVars['phone']);

        $annonce->ville          = htmlentities($allPostVars['ville']);
        $annonce->id_departement = $allPostVars['departement'];
        $annonce->prix           = htmlentities($allPostVars['price']);
        $annonce->mdp            = password_hash($allPostVars['psw'], PASSWORD_DEFAULT);
        $annonce->titre          = htmlentities($allPostVars['title']);
        $annonce->description    = htmlentities($allPostVars['description']);
        $annonce->id_categorie   = $allPostVars['categorie'];
        $annonce->date           = date('Y-m-d');

        $annonceur->save();
        $annonceur->annonce()->save($annonce);

        $template = $twig->load('add-confirm.html.twig');

        echo $template->render([
            'breadcrumb' => $menu,
            'chemin'     => $chemin,
        ]);
    }
}