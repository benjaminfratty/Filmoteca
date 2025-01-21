<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\DatabaseConnection;
use App\Service\EntityMapper;
use App\Entity\Film;

class FilmRepository
{
    private \PDO $db; 
    private EntityMapper $entityMapperService; 

    public function __construct()
    {
        $this->db = DatabaseConnection::getConnection();
        $this->entityMapperService = new EntityMapper();
    }

    public function findAll(): array
{
    $query = 'SELECT * FROM film';
    $stmt = $this->db->query($query);
    $films = $stmt->fetchAll();

    return $this->entityMapperService->mapToEntities($films, Film::class);
}




    public function find(int $id): Film
    {
        $query = 'SELECT * FROM film WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);

        $film = $stmt->fetch();

        return $this->entityMapperService->mapToEntity($film, Film::class);
    }

    public function save(Film $film): void
{
    $query = 'INSERT INTO film (title, year, type, synopsis, director, created_at, updated_at)
              VALUES (:title, :year, :type, :synopsis, :director, :createdAt, :updatedAt)';

    $stmt = $this->db->prepare($query);
    $stmt->execute([
        'title' => $film->getTitle(),
        'year' => $film->getYear(),
        'type' => $film->getType(),
        'synopsis' => $film->getSynopsis(),
        'director' => $film->getDirector(),
        'createdAt' => $film->getCreatedAt()->format('Y-m-d H:i:s'),
        'updatedAt' => $film->getUpdatedAt()->format('Y-m-d H:i:s'),
    ]);
}


public function softDelete(int $id): void
{
    $query = 'UPDATE film SET deleted_at = :deletedAt WHERE id = :id';
    $stmt = $this->db->prepare($query);
    $stmt->execute([
        'deletedAt' => (new \DateTime())->format('Y-m-d H:i:s'),
        'id' => $id,
    ]);
}

public function update(Film $film): void
{
    $query = 'UPDATE film SET 
                title = :title, 
                year = :year, 
                type = :type, 
                synopsis = :synopsis, 
                director = :director, 
                updated_at = :updatedAt
              WHERE id = :id';

    $stmt = $this->db->prepare($query);
    $stmt->execute([
        'title' => $film->getTitle(),
        'year' => $film->getYear(),
        'type' => $film->getType(),
        'synopsis' => $film->getSynopsis(),
        'director' => $film->getDirector(),
        'updatedAt' => $film->getUpdatedAt()->format('Y-m-d H:i:s'),
        'id' => $film->getId(),
    ]);
}



}
