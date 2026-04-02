<?php
require 'vendor/autoload.php';

use Racoin\Db\connection;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use Racoin\Model\Annonce;
use Racoin\Model\Categorie;
use Racoin\Model\Annonceur;
use Racoin\Model\Departement;

$logger = new Logger('racoin_logs');
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/app.log', Logger::DEBUG));

connection::createConn();

$app = new App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
]);

$app->add(function (Request $request, Response $response, $next) use ($logger) {
    $logger->info("Requête HTTP : " . $request->getMethod() . " " . $request->getUri()->getPath());
    return $next($request, $response);
});

$container = $app->getContainer();
$container['errorHandler'] = function ($c) use ($logger) {
    return function ($request, $response, $exception) use ($logger) {
        $logger->error("Exception levée : " . $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write("Une erreur interne est survenue. Consultez le fichier logs/app.log pour plus de détails.");
    };
};

$app->add(function (Request $request, Response $response, $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && str_ends_with($path, '/')) {
        $uri = $uri->withPath(substr($path, 0, -1));
        if ($request->getMethod() == 'GET') {
            return $response->withRedirect((string) $uri, 301);
        } else {
            return $next($request->withUri($uri), $response);
        }
    }
    return $next($request, $response);
});

$loader = new FilesystemLoader(__DIR__ . '/template');
$twig = new Environment($loader);

if (!isset($_SESSION)) {
    session_start();
    $_SESSION['formStarted'] = true;
}

if (!isset($_SESSION['token'])) {
    $token = md5(uniqid((string) rand(), TRUE));
    $_SESSION['token'] = $token;
    $_SESSION['token_time'] = time();
} else {
    $token = $_SESSION['token'];
}

$menu = [
    [
        'href' => './index.php',
        'text' => 'Accueil'
    ]
];

$chemin = dirname($_SERVER['SCRIPT_NAME']);

$cat = new \Racoin\Controller\getCategorie();
$dpt = new \Racoin\Controller\getDepartment();

$app->get('/', function () use ($twig, $menu, $chemin, $cat) {
    $index = new \Racoin\Controller\index();
    $index->displayAllAnnonce($twig, $menu, $chemin, $cat->getCategories());
});

$app->get('/exception', function ($request, $response) {
    throw new \Exception("Ceci est un test d'exception pour vérifier que Monolog enregistre bien les erreurs !");
});

$app->get('/item/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n = $arg['n'];
    $item = new \Racoin\Controller\item();
    $item->afficherItem($twig, $menu, $chemin, $n, $cat->getCategories());
});

$app->get('/add', function () use ($twig, $app, $menu, $chemin, $cat, $dpt) {
    $ajout = new \Racoin\Controller\addItem();
    $ajout->addItemView($twig, $menu, $chemin, $cat->getCategories(), $dpt->getAllDepartments());
});

$app->post('/add', function ($request) use ($twig, $app, $menu, $chemin) {
    $allPostVars = $request->getParsedBody();
    $ajout = new \Racoin\Controller\addItem();
    $ajout->addNewItem($twig, $menu, $chemin, $allPostVars);
});

$app->get('/item/{id}/edit', function ($request, $response, $arg) use ($twig, $menu, $chemin) {
    $id = $arg['id'];
    $item = new \Racoin\Controller\item();
    $item->modifyGet($twig, $menu, $chemin, $id);
});

$app->post('/item/{id}/edit', function ($request, $response, $arg) use ($twig, $app, $menu, $chemin, $cat, $dpt) {
    $id = $arg['id'];
    $allPostVars = $request->getParsedBody();
    $item = new \Racoin\Controller\item();
    $item->modifyPost($twig, $menu, $chemin, $id, $cat->getCategories(), $dpt->getAllDepartments(), $allPostVars);
});

$app->map(['GET', 'POST'], '/item/{id}/confirm', function ($request, $response, $arg) use ($twig, $app, $menu, $chemin) {
    $id = $arg['id'];
    $allPostVars = $request->getParsedBody();
    $item = new \Racoin\Controller\item();
    $item->edit($twig, $menu, $chemin, $allPostVars, $id);
});

$app->get('/search', function () use ($twig, $menu, $chemin, $cat) {
    $s = new \Racoin\Controller\Search();
    $s->show($twig, $menu, $chemin, $cat->getCategories());
});

$app->post('/search', function ($request, $response) use ($app, $twig, $menu, $chemin, $cat) {
    $array = $request->getParsedBody();
    $s = new \Racoin\Controller\Search();
    $s->research($array, $twig, $menu, $chemin, $cat->getCategories());
});

$app->get('/annonceur/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n = $arg['n'];
    $annonceur = new \Racoin\Controller\viewAnnonceur();
    $annonceur->afficherAnnonceur($twig, $menu, $chemin, $n, $cat->getCategories());
});

$app->get('/del/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin) {
    $n = $arg['n'];
    $item = new \Racoin\Controller\item();
    $item->supprimerItemGet($twig, $menu, $chemin, $n);
});

$app->post('/del/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n = $arg['n'];
    $allPostVars = $request->getParsedBody();
    $item = new \Racoin\Controller\item();
    $item->supprimerItemPost($twig, $menu, $chemin, $n, $cat->getCategories(), $allPostVars);
});

$app->get('/cat/{n}', function ($request, $response, $arg) use ($twig, $menu, $chemin, $cat) {
    $n = $arg['n'];
    $categorie = new \Racoin\Controller\getCategorie();
    $categorie->displayCategorie($twig, $menu, $chemin, $cat->getCategories(), $n);
});

$app->get('/api(/)', function () use ($twig, $menu, $chemin, $cat) {
    $template = $twig->load('api.html.twig');
    $menu = array(
        array(
            'href' => $chemin,
            'text' => 'Accueil'
        ),
        array(
            'href' => $chemin . '/api',
            'text' => 'Api'
        )
    );
    echo $template->render(array('breadcrumb' => $menu, 'chemin' => $chemin));
});

$app->group('/api', function () use ($app, $twig, $menu, $chemin, $cat) {

    $app->group('/annonce', function () use ($app) {
        $app->get('/{id}', function ($request, $response, $arg) use ($app) {
            $id = $arg['id'];
            $annonceList = ['id_annonce', 'id_categorie as categorie', 'id_annonceur as annonceur', 'id_departement as departement', 'prix', 'date', 'titre', 'description', 'ville'];
            $return = Annonce::select($annonceList)->find($id);

            if (isset($return)) {
                $response->headers->set('Content-Type', 'application/json');
                $return->categorie = Categorie::find($return->categorie);
                $return->annonceur = Annonceur::select('email', 'nom_annonceur', 'telephone')
                    ->find($return->annonceur);
                $return->departement = Departement::select('id_departement', 'nom_departement')->find($return->departement);
                $links = [];
                $links['self']['href'] = '/api/annonce/' . $return->id_annonce;
                $return->links = $links;
                echo $return->toJson();
            } else {
                $app->notFound();
            }
        });
    });

    $app->group('/annonces(/)', function () use ($app) {
        $app->get('/', function ($request, $response) use ($app) {
            $annonceList = ['id_annonce', 'prix', 'titre', 'ville'];
            $response->headers->set('Content-Type', 'application/json');
            $a = Annonce::all($annonceList);
            $links = [];
            foreach ($a as $ann) {
                $links['self']['href'] = '/api/annonce/' . $ann->id_annonce;
                $ann->links = $links;
            }
            $links['self']['href'] = '/api/annonces/';
            $a->links = $links;
            echo $a->toJson();
        });
    });

    $app->group('/categorie', function () use ($app) {
        $app->get('/{id}', function ($request, $response, $arg) use ($app) {
            $id = $arg['id'];
            $response->headers->set('Content-Type', 'application/json');
            $a = Annonce::select('id_annonce', 'prix', 'titre', 'ville')
                ->where('id_categorie', '=', $id)
                ->get();
            $links = [];

            foreach ($a as $ann) {
                $links['self']['href'] = '/api/annonce/' . $ann->id_annonce;
                $ann->links = $links;
            }

            $c = Categorie::find($id);
            if ($c) {
                $links['self']['href'] = '/api/categorie/' . $id;
                $c->links = $links;
                $c->annonces = $a;
                echo $c->toJson();
            } else {
                echo json_encode(['error' => 'Category not found']);
            }
        });
    });

    $app->group('/categories(/)', function () use ($app) {
        $app->get('/', function ($request, $response, $arg) use ($app) {
            $response->headers->set('Content-Type', 'application/json');
            $c = Categorie::get();
            $links = [];
            foreach ($c as $cat) {
                $links['self']['href'] = '/api/categorie/' . $cat->id_categorie;
                $cat->links = $links;
            }
            $links['self']['href'] = '/api/categories/';
            $c->links = $links;
            echo $c->toJson();
        });
    });

    $app->get('/key', function () use ($app, $twig, $menu, $chemin, $cat) {
        $kg = new \Racoin\Controller\KeyGenerator();
        $kg->show($twig, $menu, $chemin, $cat->getCategories());
    });

    $app->post('/key', function ($request) use ($app, $twig, $menu, $chemin, $cat) {
        $parsedBody = $request->getParsedBody();
        $nom = $parsedBody['nom'] ?? '';

        $kg = new \Racoin\Controller\KeyGenerator();
        $kg->generateKey($twig, $menu, $chemin, $cat->getCategories(), $nom);
    });
});

$app->run();