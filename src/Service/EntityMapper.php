<?php

declare(strict_types=1);

namespace App\Service;

class EntityMapper
{
    
    public function mapToEntity(array $data, string $entityClass)
    {
        $entity = new $entityClass();

        foreach ($data as $key => $value) {
            $setterKey = str_replace('_', '', ucwords($key, '_'));
            $setter = 'set' . $setterKey;

            if (str_contains($key, '_at') && $value !== null) {
                try {
                    $value = new \DateTime($value);
                } catch (\Exception $e) {
                    throw new \InvalidArgumentException("Invalid date format for key '$key': $value");
                }
            }

            if ($key === 'year' && $value !== null) {
                $value = (int) $value; 
            }

            if (method_exists($entity, $setter)) {
                $entity->$setter($value);
            }
        }

        return $entity;
    }

    
    public function mapToEntities(array $rows, string $entityClass): array
    {
        $entities = [];

        foreach ($rows as $row) {
            $entities[] = $this->mapToEntity($row, $entityClass);
        }

        return $entities;
    }
}
