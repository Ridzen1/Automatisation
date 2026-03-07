<?php
require 'vendor/autoload.php';

use db\connection;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use model\Annonce;
use model\Categorie;
use model\Annonceur;
use model\Departement;

connection::createConn();

if (!isset($_SESSION)) {
    session_start();
    $_SESSION['formStarted'] = true;
}

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = md5(uniqid(rand(), TRUE));
    $_SESSION['token_time'] = time();
}

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true); // Remplace le mode 'development'

$loader = new \Twig\Loader\FilesystemLoader('template');
$twig = new \Twig\Environment($loader);

$menu = array(
    array('href' => "./index.php", 'text' => 'Accueil')
);

$chemin = dirname($_SERVER['SCRIPT_NAME']);

$cat = new \controller\getCategorie();
$dpt = new \controller\getDepartment();

$app->get('/', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
    ob_start();
    $index = new \controller\index();
    $index->displayAllAnnonce($twig, $menu, $chemin, $cat->getCategories());
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->get('/item/{n}', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin, $cat) {
    ob_start();
    $item = new \controller\item();
    $item->afficherItem($twig, $menu, $chemin, $args['n'], $cat->getCategories());
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->get('/add', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat, $dpt) {
    ob_start();
    $ajout = new controller\addItem();
    $ajout->addItemView($twig, $menu, $chemin, $cat->getCategories(), $dpt->getAllDepartments());
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/add', function (Request $request, Response $response) use ($twig, $menu, $chemin) {
    ob_start();
    $allPostVars = (array)$request->getParsedBody();
    $ajout = new controller\addItem();
    $ajout->addNewItem($twig, $menu, $chemin, $allPostVars);
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->get('/item/{id}/edit', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin) {
    ob_start();
    $item = new \controller\item();
    $item->modifyGet($twig, $menu, $chemin, $args['id']);
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/item/{id}/edit', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin, $cat, $dpt) {
    ob_start();
    $allPostVars = (array)$request->getParsedBody();
    $item= new \controller\item();
    $item->modifyPost($twig, $menu, $chemin, $args['id'], $allPostVars, $cat->getCategories(), $dpt->getAllDepartments());
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->map(['GET', 'POST'], '/item/{id}/confirm', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin) {
    ob_start();
    $allPostVars = (array)$request->getParsedBody();
    $item = new \controller\item();
    $item->edit($twig, $menu, $chemin, $allPostVars, $args['id']);
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->get('/search', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
    ob_start();
    $s = new controller\Search();
    $s->show($twig, $menu, $chemin, $cat->getCategories());
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/search', function (Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
    ob_start();
    $array = (array)$request->getParsedBody();
    $s = new controller\Search();
    $s->research($array, $twig, $menu, $chemin, $cat->getCategories());
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->get('/annonceur/{n}', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin, $cat) {
    ob_start();
    $annonceur = new controller\viewAnnonceur();
    $annonceur->afficherAnnonceur($twig, $menu, $chemin, $args['n'], $cat->getCategories());
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->get('/del/{n}', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin) {
    ob_start();
    $item = new controller\item();
    $item->supprimerItemGet($twig, $menu, $chemin, $args['n']);
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/del/{n}', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin, $cat) {
    ob_start();
    $item = new controller\item();
    $item->supprimerItemPost($twig, $menu, $chemin, $args['n'], $cat->getCategories());
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->get('/cat/{n}', function (Request $request, Response $response, array $args) use ($twig, $menu, $chemin, $cat) {
    ob_start();
    $categorie = new controller\getCategorie();
    $categorie->displayCategorie($twig, $menu, $chemin, $cat->getCategories(), $args['n']);
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->get('/api', function (Request $request, Response $response) use ($twig, $menu, $chemin) {
    ob_start();
    $template = $twig->load("api.html.twig");
    $menu = array(
        array('href' => $chemin, 'text' => 'Acceuil'),
        array('href' => $chemin . '/api', 'text' => 'Api')
    );
    echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin));
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->group('/api', function (RouteCollectorProxy $group) use ($twig, $menu, $chemin, $cat) {

    $group->group('/annonce', function (RouteCollectorProxy $group) {
        $group->get('/{id}', function (Request $request, Response $response, array $args) {
            $id = $args['id'];
            $annonceList = ['id_annonce', 'id_categorie as categorie', 'id_annonceur as annonceur', 'id_departement as departement', 'prix', 'date', 'titre', 'description', 'ville'];
            $return = Annonce::select($annonceList)->find($id);

            if (isset($return)) {
                $return->categorie = Categorie::find($return->categorie);
                $return->annonceur = Annonceur::select('email', 'nom_annonceur', 'telephone')->find($return->annonceur);
                $return->departement = Departement::select('id_departement', 'nom_departement')->find($return->departement);
                $links = [];
                $links["self"]["href"] = "/api/annonce/" . $return->id_annonce;
                $return->links = $links;
                
                $response->getBody()->write($return->toJson());
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                throw new \Slim\Exception\HttpNotFoundException($request);
            }
        });
    });

    $group->group('/annonces', function (RouteCollectorProxy $group) {
        $group->get('/', function (Request $request, Response $response) {
            $annonceList = ['id_annonce', 'prix', 'titre', 'ville'];
            $a = Annonce::all($annonceList);
            $links = [];
            foreach ($a as $ann) {
                $links["self"]["href"] = "/api/annonce/" . $ann->id_annonce;
                $ann->links = $links;
            }
            $links["self"]["href"] = "/api/annonces/";
            $a->links = $links;
            
            $response->getBody()->write($a->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $group->group('/categorie', function (RouteCollectorProxy $group) {
        $group->get('/{id}', function (Request $request, Response $response, array $args) {
            $id = $args['id'];
            $a = Annonce::select('id_annonce', 'prix', 'titre', 'ville')
                ->where("id_categorie", "=", $id)
                ->get();
            $links = [];
            foreach ($a as $ann) {
                $links["self"]["href"] = "/api/annonce/" . $ann->id_annonce;
                $ann->links = $links;
            }
            $c = Categorie::find($id);
            $links["self"]["href"] = "/api/categorie/" . $id;
            $c->links = $links;
            $c->annonces = $a;
            
            $response->getBody()->write($c->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $group->group('/categories', function (RouteCollectorProxy $group) {
        $group->get('/', function (Request $request, Response $response) {
            $c = Categorie::get();
            $links = [];
            foreach ($c as $cat) {
                $links["self"]["href"] = "/api/categorie/" . $cat->id_categorie;
                $cat->links = $links;
            }
            $links["self"]["href"] = "/api/categories/";
            $c->links = $links;
            
            $response->getBody()->write($c->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    $group->get('/key', function(Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
        ob_start();
        $kg = new controller\KeyGenerator();
        $kg->show($twig, $menu, $chemin, $cat->getCategories());
        $response->getBody()->write(ob_get_clean());
        return $response;
    });

    $group->post('/key', function(Request $request, Response $response) use ($twig, $menu, $chemin, $cat) {
        ob_start();
        $parsedBody = (array)$request->getParsedBody();
        $nom = $parsedBody['nom'] ?? '';
        $kg = new controller\KeyGenerator();
        $kg->generateKey($twig, $menu, $chemin, $cat->getCategories(), $nom);
        $response->getBody()->write(ob_get_clean());
        return $response;
    });
});

$app->run();