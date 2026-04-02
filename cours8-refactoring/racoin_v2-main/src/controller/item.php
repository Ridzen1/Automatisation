<?php

declare(strict_types=1);

// Attention à bien mettre la majuscule si tu as mis Racoin\Controller pour AddItem !
namespace Racoin\Controller;

use Racoin\Model\Annonce;
use Racoin\Model\Annonceur;
use Racoin\Model\Departement;
use Racoin\Model\Photo;
use Racoin\Model\Categorie;
use Twig\Environment;

class Item
{
    // Plus besoin de __construct ni de #[AllowDynamicProperties]

    public function afficherItem(Environment $twig, array $menu, string $chemin, string|int $n, array $cat): void
    {
        $annonce = Annonce::find($n);

        if (!$annonce) {
            echo "404";
            return;
        }

        $categorie = Categorie::find($annonce->id_categorie);

        $menu = [
            ['href' => $chemin, 'text' => 'Accueil'],
            ['href' => $chemin . "/cat/" . $n, 'text' => $categorie?->nom_categorie],
            ['href' => $chemin . "/item/" . $n, 'text' => $annonce->titre]
        ];

        // Au lieu d'utiliser $this->..., on utilise de simples variables locales
        $annonceur = Annonceur::find($annonce->id_annonceur);
        $departement = Departement::find($annonce->id_departement);
        $photo = Photo::where('id_annonce', '=', $n)->get();

        $template = $twig->load("item.html.twig");

        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $annonce,
            "annonceur" => $annonceur,
            "dep" => $departement?->nom_departement,
            "photo" => $photo,
            "categories" => $cat
        ]);
    }

    public function supprimerItemGet(Environment $twig, array $menu, string $chemin, string|int $n): void
    {
        $annonce = Annonce::find($n);

        if (!$annonce) {
            echo "404";
            return;
        }

        $template = $twig->load("delGet.html.twig");

        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $annonce
        ]);
    }

    // On ajoute $allPostVars pour éviter d'utiliser $_POST
    public function supprimerItemPost(Environment $twig, array $menu, string $chemin, string|int $n, array $cat, array $allPostVars): void
    {
        $annonce = Annonce::find($n);
        $reponse = false;

        $pass = $allPostVars["pass"] ?? '';

        if ($annonce && password_verify($pass, $annonce->mdp)) {
            $reponse = true;
            Photo::where('id_annonce', '=', $n)->delete();
            $annonce->delete();
        }

        $template = $twig->load("delPost.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $annonce,
            "pass" => $reponse,
            "categories" => $cat
        ]);
    }

    public function modifyGet(Environment $twig, array $menu, string $chemin, string|int $id): void
    {
        $annonce = Annonce::find($id);

        if (!$annonce) {
            echo "404";
            return;
        }

        $template = $twig->load("modifyGet.html.twig");

        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $annonce
        ]);
    }

    // On ajoute $allPostVars ici aussi
    public function modifyPost(Environment $twig, array $menu, string $chemin, string|int $n, array $cat, array $dpt, array $allPostVars): void
    {
        $annonce = Annonce::find($n);

        if (!$annonce) {
            echo "404";
            return;
        }

        $annonceur = Annonceur::find($annonce->id_annonceur);
        $categItem = Categorie::find($annonce->id_categorie)?->nom_categorie;
        $dptItem = Departement::find($annonce->id_departement)?->nom_departement;

        $reponse = false;
        $pass = $allPostVars["pass"] ?? '';

        if (password_verify($pass, $annonce->mdp)) {
            $reponse = true;
        }

        $template = $twig->load("modifyPost.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $annonce,
            "annonceur" => $annonceur,
            "pass" => $reponse,
            "categories" => $cat,
            "departements" => $dpt,
            "dptItem" => $dptItem,
            "categItem" => $categItem
        ]);
    }

    public function edit(Environment $twig, array $menu, string $chemin, array $allPostVars, string|int $id): void
    {
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

        $errors = [];

        if ($nom === '')
            $errors[] = 'Veuillez entrer votre nom';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors[] = 'Veuillez entrer une adresse mail correcte';
        if ($phone === '' || !is_numeric($phone))
            $errors[] = 'Veuillez entrer votre numéro de téléphone';
        if ($ville === '')
            $errors[] = 'Veuillez entrer votre ville';
        if (!is_numeric($departement))
            $errors[] = 'Veuillez choisir un département';
        if (!is_numeric($categorie))
            $errors[] = 'Veuillez choisir une catégorie';
        if ($title === '')
            $errors[] = 'Veuillez entrer un titre';
        if ($description === '')
            $errors[] = 'Veuillez entrer une description';
        if ($price === '' || !is_numeric($price))
            $errors[] = 'Veuillez entrer un prix';

        if (!empty($errors)) {
            $template = $twig->load("add-error.html.twig");
            echo $template->render([
                "breadcrumb" => $menu,
                "chemin" => $chemin,
                "errors" => $errors
            ]);
            return;
        }

        $annonce = Annonce::find($id);

        if ($annonce) {
            $annonceur = Annonceur::find($annonce->id_annonceur);

            if ($annonceur) {
                $annonceur->email = htmlspecialchars($email);
                $annonceur->nom_annonceur = htmlspecialchars($nom);
                $annonceur->telephone = htmlspecialchars($phone);
                $annonceur->save();
            }

            $annonce->ville = htmlspecialchars($ville);
            $annonce->id_departement = $departement;
            $annonce->prix = htmlspecialchars($price);
            $annonce->mdp = password_hash($password, PASSWORD_DEFAULT);
            $annonce->titre = htmlspecialchars($title);
            $annonce->description = htmlspecialchars($description);
            $annonce->id_categorie = $categorie;
            $annonce->date = date('Y-m-d');

            $annonce->save();
        }

        $template = $twig->load("modif-confirm.html.twig");
        echo $template->render(["breadcrumb" => $menu, "chemin" => $chemin]);
    }
}