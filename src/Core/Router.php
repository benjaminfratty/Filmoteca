<?php

declare(strict_types=1);

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Router
{
    private Environment $twig;

    public function __construct()
    {
        // Initialisation de Twig
        $loader = new FilesystemLoader('/var/www/filmoteca/src/views');
        $this->twig = new Environment($loader);
    }

    public function route(): void
    {
        // Récupère l'URL demandée (sans le domaine et la racine)
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // Découpe l'URI pour obtenir la route et l'action
        $parts = explode('/', $uri); // Exemple : ['films', 'create']

        $route = $parts[0] ?? null;   // 'films'
        $action = $parts[1] ?? 'list'; // Action par défaut : 'list'

        // Définit les routes et leurs contrôleurs associés
        $routes = [
            'films' => 'FilmController',
            'contact' => 'ContactController',
        ];

        if (array_key_exists($route, $routes)) {
            // Crée dynamiquement le contrôleur
            $controllerName = 'App\\Controller\\' . $routes[$route];

            if (!class_exists($controllerName)) {
                $this->renderError(404, "Controller '$controllerName' not found");
                return;
            }

            // Instancie le contrôleur avec les dépendances nécessaires
            $controller = $this->instantiateController($controllerName);

            // Vérifie si la méthode existe dans le contrôleur
            if (method_exists($controller, $action)) {
                $queryParams = $_GET; // Récupère les paramètres éventuels
                $controller->$action($queryParams); // Appelle la méthode correspondant à l'action
            } else {
                $this->renderError(404, "Action '$action' not found in $controllerName");
            }
        } else {
            // Page non trouvée
            $this->renderError(404, "Route '$route' not found");
        }
    }

    private function instantiateController(string $controllerName)
    {
        // Instancie les dépendances nécessaires pour les contrôleurs
        // Si un contrôleur requiert d'autres dépendances, ajoutez-les ici
        if ($controllerName === 'App\\Controller\\FilmController') {
            return new $controllerName($this->twig);
        }

        // Pour les contrôleurs sans dépendances spécifiques
        return new $controllerName();
    }

    private function renderError(int $code, string $message): void
    {
        http_response_code($code);
        echo $this->twig->render('error.html.twig', ['code' => $code, 'message' => $message]);
    }
}
