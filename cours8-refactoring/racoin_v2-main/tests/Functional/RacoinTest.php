<?php

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Tests fonctionnels pour le refactoring
 * 
 * Vérifie que les fonctionnalités principales fonctionnent correctement :
 * - Page d'accueil
 * - Consultation d'une annonce
 * - Ajout d'une annonce
 */
class RacoinTest extends TestCase
{
    private $app;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Charger l'application
        require_once __DIR__ . '/../../vendor/autoload.php';
        
        // Initialiser la connexion à la base de données
        \db\connection::createConn();
        
        // Créer l'application Slim
        $this->app = new App([
            'settings' => [
                'displayErrorDetails' => true,
            ],
        ]);

        // Initialisation de Twig
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../template');
        $twig = new \Twig\Environment($loader);

        $menu = [
            ['href' => './index.php', 'text' => 'Accueil']
        ];

        $chemin = '';
        $cat = new \controller\getCategorie();
        $dpt = new \controller\getDepartment();

        // Route de la page d'accueil
        $this->app->get('/', function () use ($twig, $menu, $chemin, $cat) {
            $index = new \controller\index();
            $index->displayAllAnnonce($twig, $menu, $chemin, $cat->getCategories());
        });

        // Route pour consulter une annonce
        $this->app->get('/item/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
            $n = $arg['n'];
            $item = new \controller\item();
            $item->afficherItem($twig, $menu, $chemin, $n, $cat->getCategories());
        });

        // Route pour afficher le formulaire d'ajout
        $this->app->get('/add', function () use ($twig, $menu, $chemin, $cat, $dpt) {
            $ajout = new \controller\addItem();
            $ajout->addItemView($twig, $menu, $chemin, $cat->getCategories(), $dpt->getAllDepartments());
        });

        // Route pour ajouter une annonce
        $this->app->post('/add', function ($request) use ($twig, $menu, $chemin) {
            $allPostVars = $request->getParsedBody();
            $ajout = new \controller\addItem();
            $ajout->addNewItem($twig, $menu, $chemin, $allPostVars);
        });
    }

    /**
     * Teste que la page d'accueil retourne bien des données (HTTP 200)
     */
    public function testHomePageReturnsData(): void
    {
        // Créer une requête GET sur /
        $environment = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
        ]);

        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // Capturer la sortie (les controllers utilisent echo)
        ob_start();
        $response = $this->app->process($request, $response);
        $body = ob_get_clean();

        // Vérifications
        $this->assertEquals(200, $response->getStatusCode(), 
            'La page d\'accueil devrait retourner un code 200');
        
        // Vérifier que la réponse contient du contenu
        $this->assertNotEmpty($body, 
            'La page d\'accueil devrait retourner du contenu');
        
        // Vérifier que le contenu contient "Acceuil" (breadcrumb)
        $this->assertStringContainsString('Acceuil', $body, 
            'La page devrait contenir le breadcrumb "Acceuil"');
        
        echo "\n✅ Test réussi : La page d'accueil retourne bien des données\n";
        echo "Taille du contenu : " . strlen($body) . " caractères\n";
    }

    /**
     * Teste la consultation d'une annonce existante
     */
    public function testConsulterAnnonce(): void
    {
        // Créer une annonce de test en base de données
        $annonceur = new \model\Annonceur();
        $annonceur->nom_annonceur = 'Test Annonceur';
        $annonceur->email = 'test@example.com';
        $annonceur->telephone = '0612345678';
        $annonceur->save();

        $annonce = new \model\Annonce();
        $annonce->titre = 'Test Annonce Consultation';
        $annonce->description = 'Description de test pour consultation';
        $annonce->prix = 100;
        $annonce->ville = 'Paris';
        $annonce->id_departement = 75;
        $annonce->id_categorie = 1;
        $annonce->id_annonceur = $annonceur->id_annonceur;
        $annonce->mdp = password_hash('testpass', PASSWORD_DEFAULT);
        $annonce->date = date('Y-m-d');
        $annonce->save();

        $annonceId = $annonce->id_annonce;

        // Créer une requête GET sur /item/{id}
        $environment = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/item/' . $annonceId,
        ]);

        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // Capturer la sortie
        ob_start();
        $response = $this->app->process($request, $response);
        $body = ob_get_clean();

        // Vérifications
        $this->assertEquals(200, $response->getStatusCode(), 
            'La page de l\'annonce devrait retourner un code 200');
        
        $this->assertNotEmpty($body, 
            'La page de l\'annonce devrait retourner du contenu');
        
        $this->assertStringContainsString('Test Annonce Consultation', $body, 
            'La page devrait contenir le titre de l\'annonce');
        
        $this->assertStringContainsString('Description de test pour consultation', $body, 
            'La page devrait contenir la description de l\'annonce');
        
        $this->assertStringContainsString('100', $body, 
            'La page devrait contenir le prix de l\'annonce');

        echo "\n✅ Test réussi : La consultation d'une annonce fonctionne\n";
        echo "Annonce consultée : ID {$annonceId}\n";

        // Nettoyage
        $annonce->delete();
        $annonceur->delete();
    }

    /**
     * Teste l'ajout d'une nouvelle annonce avec des données valides
     */
    public function testAjouterAnnonce(): void
    {
        // Données du formulaire
        $formData = [
            'nom' => 'Nouveau Vendeur Test',
            'email' => 'vendeur.test@example.com',
            'phone' => '0612345678',
            'ville' => 'Lyon',
            'departement' => '69',
            'categorie' => '1',
            'title' => 'Nouvelle Annonce Test',
            'description' => 'Description complète de la nouvelle annonce de test',
            'price' => '250',
            'psw' => 'password123',
            'confirm-psw' => 'password123'
        ];

        // IMPORTANT : Le controller addItem utilise $_POST directement
        // Il faut donc mettre les données dans $_POST pour le test
        $_POST = $formData;

        // Créer une requête POST sur /add
        $environment = Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/add',
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
        ]);

        $request = Request::createFromEnvironment($environment);
        $request = $request->withParsedBody($formData);
        $response = new Response();

        // Capturer la sortie
        ob_start();
        $response = $this->app->process($request, $response);
        $body = ob_get_clean();
        
        // Nettoyer $_POST après le test
        $_POST = [];

        // Vérifications
        $this->assertEquals(200, $response->getStatusCode(), 
            'L\'ajout d\'annonce devrait retourner un code 200');
        
        $this->assertNotEmpty($body, 
            'La page de confirmation devrait retourner du contenu');
        
        // Vérifier qu'il n'y a pas de message d'erreur
        $this->assertStringNotContainsString('Veuillez entrer', $body, 
            'La page ne devrait pas contenir de message d\'erreur de validation');

        // Vérifier que l'annonce a été créée en base
        $annonce = \model\Annonce::where('titre', 'Nouvelle Annonce Test')->first();
        $this->assertNotNull($annonce, 
            'L\'annonce devrait être créée en base de données');
        
        if ($annonce) {
            $this->assertEquals('Nouvelle Annonce Test', $annonce->titre);
            $this->assertEquals(250, $annonce->prix);
            $this->assertEquals('Lyon', $annonce->ville);
            
            echo "\n✅ Test réussi : L'ajout d'une annonce fonctionne\n";
            echo "Annonce créée : ID {$annonce->id_annonce}\n";

            // Récupérer l'annonceur pour le nettoyage
            $annonceur = \model\Annonceur::find($annonce->id_annonceur);
            
            // Nettoyage
            $annonce->delete();
            if ($annonceur) {
                $annonceur->delete();
            }
        }
    }
}
