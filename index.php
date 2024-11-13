<?php

// Classe Route qui représente une route avec un chemin et une fonction de rappel
class Route {
    public string $chemin;
    public $action;

    public function definirChemin($chemin) {
        $this->chemin = $chemin;
    }

    public function definirAction($action) {
        $this->action = $action;
    }

    public function correspond($url) {
        return $this->chemin === $url;
    }

    public function executer() {
        if (is_callable($this->action)) {
            call_user_func($this->action);
        }
    }
}

// Classe Routeur pour gérer les routes
class Routeur {
    public array $routes = [];

    public function ajouterRoute($chemin, $action) {
        $route = new Route();
        $route->definirChemin($chemin);
        $route->definirAction($action);
        $this->routes[] = $route;
    }

    public function gererRequete($url) {
        foreach ($this->routes as $route) {
            if ($route->correspond($url)) {
                $route->executer();
                return;
            }
        }
        // Si aucune route ne correspond
        echo "404 - Page non trouvée";
    }
}

// Initialiser le routeur
$routeur = new Routeur();

// Définir des fonctions de rappel pour chaque route
function afficherFilms() {
    echo "Page des films";
}

function afficherContact() {
    echo "Page de contact";
}

// Ajouter des routes
$routeur->ajouterRoute('/films', 'afficherFilms');
$routeur->ajouterRoute('/contact', 'afficherContact');

// Récupérer l'URL demandée
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Gérer la requête
$routeur->gererRequete($url);
?>
