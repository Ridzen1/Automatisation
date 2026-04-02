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
            'breadcrumb' => $menu,
            'chemin' => $chemin,
            'categories' => $cat,
            'departements' => $dpt,
        ]);
    }

    public function addNewItem(
        Environment $twig,
        array $menu,
        string $chemin,
        array $allPostVars
    ): void {
        date_default_timezone_set('Europe/Paris');

        $nom = trim($allPostVars['nom'] ?? '');
        $email = trim($allPostVars['email'] ?? '');
        $phone = trim($allPostVars['phone'] ?? '');
        $ville = trim($allPostVars['ville'] ?? '');
        $departement = trim($allPostVars['departement'] ?? '');
        $categorie = trim($allPostVars['categorie'] ?? '');
        $title = trim($allPostVars['title'] ?? '');
        $description = trim($allPostVars['description'] ?? '');
        $price = trim($allPostVars['price'] ?? '');
        $password = trim($allPostVars['psw'] ?? '');
        $password_confirm = trim($allPostVars['confirm-psw'] ?? '');

        $errors = [];

        if ($nom === '') {
            $errors[] = 'Veuillez entrer votre nom';
        }

        // Remplacement de la Regex par la fonction native PHP
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

        if (!empty($errors)) {
            $template = $twig->load('add-error.html.twig');

            echo $template->render([
                'breadcrumb' => $menu,
                'chemin' => $chemin,
                'errors' => $errors,
            ]);

            return;
        }

        $annonce = new Annonce();
        $annonceur = new Annonceur();

        $annonceur->email = htmlspecialchars($email);
        $annonceur->nom_annonceur = htmlspecialchars($nom);
        $annonceur->telephone = htmlspecialchars($phone);

        $annonce->ville = htmlspecialchars($ville);
        $annonce->id_departement = $departement;
        $annonce->prix = htmlspecialchars($price);
        $annonce->mdp = password_hash($password, PASSWORD_DEFAULT);
        $annonce->titre = htmlspecialchars($title);
        $annonce->description = htmlspecialchars($description);
        $annonce->id_categorie = $categorie;
        $annonce->date = date('Y-m-d');

        $annonceur->save();
        $annonceur->annonce()->save($annonce);

        $template = $twig->load('add-confirm.html.twig');

        echo $template->render([
            'breadcrumb' => $menu,
            'chemin' => $chemin,
        ]);
    }
}