<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\TemplateRenderer;
use App\Entity\Film;
use App\Repository\FilmRepository;

class FilmController
{
    private TemplateRenderer $renderer;

    public function __construct()
    {
        $this->renderer = new TemplateRenderer();
    }

    public function list(array $queryParams)
    {
        $filmRepository = new FilmRepository();
        $films = $filmRepository->findAll();

        /* $filmEntities = [];
        foreach ($films as $film) {
            $filmEntity = new Film();
            $filmEntity->setId($film['id']);
            $filmEntity->setTitle($film['title']);
            $filmEntity->setYear($film['year']);
            $filmEntity->setType($film['type']);
            $filmEntity->setSynopsis($film['synopsis']);
            $filmEntity->setDirector($film['director']);
            $filmEntity->setCreatedAt(new \DateTime($film['created_at']));
            $filmEntity->setUpdatedAt(new \DateTime($film['updated_at']));

            $filmEntities[] = $filmEntity;
        } */

        //dd($films);

        echo $this->renderer->render('film/list.html.twig', [
            'films' => $films,
        ]);

        // header('Content-Type: application/json');
        // echo json_encode($films);
    }

    public function create()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $filmRepository = new FilmRepository();
        $film = new Film();

        $film->setTitle($_POST['title']);
        $film->setYear($_POST['year']);
        $film->setType($_POST['type']);
        $film->setDirector($_POST['director']);
        $film->setSynopsis($_POST['synopsis']);
        $film->setCreatedAt(new \DateTime());
        $film->setUpdatedAt(new \DateTime());

        $filmRepository->save($film);

        header('Location: /films');
        exit;
    }

    echo $this->renderer->render('film/create.html.twig');
}


public function read(array $queryParams)
{
    if (!isset($queryParams['id'])) {
        echo "L'identifiant du film est requis.";
        return;
    }

    $filmRepository = new FilmRepository();
    $film = $filmRepository->find((int)$queryParams['id']);

    if (!$film) {
        echo "Film introuvable.";
        return;
    }

    echo $this->renderer->render('film/read.html.twig', [
        'film' => $film,
    ]);
}


public function update(array $queryParams)
{
    $filmRepository = new FilmRepository();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($queryParams['id'])) {
            echo "L'identifiant du film est requis.";
            return;
        }

        $filmId = (int)$queryParams['id'];
        $film = $filmRepository->find($filmId);

        if (!$film) {
            echo "Film introuvable.";
            return;
        }

        $film->setTitle($_POST['title']);
        $film->setYear((int)$_POST['year']);
        $film->setType($_POST['type']);
        $film->setDirector($_POST['director']);
        $film->setSynopsis($_POST['synopsis']);
        $film->setUpdatedAt(new \DateTime());

        $filmRepository->update($film);

        header('Location: /films');
        exit;
    } else {
        if (!isset($queryParams['id'])) {
            echo "L'identifiant du film est requis.";
            return;
        }

        $filmId = (int)$queryParams['id'];
        $film = $filmRepository->find($filmId);

        if (!$film) {
            echo "Film introuvable.";
            return;
        }

        echo $this->renderer->render('film/update.html.twig', [
            'film' => $film,
        ]);
    }
}


    public function delete(array $queryParams)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($queryParams['id'])) {
            echo "L'identifiant du film est requis.";
            return;
        }

        $filmRepository = new FilmRepository();
        $filmId = (int)$queryParams['id'];

        $filmRepository->softDelete($filmId);

        header('Location: /films');
        exit;
    } else {
        echo "Méthode HTTP non autorisée.";
    }
}


}
