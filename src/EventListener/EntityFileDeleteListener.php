<?php

namespace App\EventListener;

use App\Consts;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityFileDeleteListener
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $this->removeFiles($args->getEntity());
    }

    private function removeFiles($entity)
    {
        $entityClass = get_class($entity);
        $fileField = Consts::ENTITY_FILE_FIELDS[ $entityClass ] ?? null;
        $uploadDir = Consts::ENTITY_UPLOAD_DIRS[ $entityClass ] ?? null;

        if (!$fileField || !$uploadDir) {
            return;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $fileName = $propertyAccessor->getValue($entity, $fileField);

        if (!$fileName) {
            return;
        }

        unlink("{$this->projectDir}{$uploadDir}{$fileName}");
    }
}
