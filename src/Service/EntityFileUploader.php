<?php

namespace App\Service;

use App\Consts;
use App\Entity\BlogPost;
use App\Entity\Party;
use App\Entity\Politician;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityFileUploader
{
    private ObjectManager $objectManager;
    private string $projectDir;

    public function __construct(ObjectManager $objectManager, string $projectDir)
    {
        $this->objectManager = $objectManager;
        $this->projectDir = $projectDir;
    }

    public function uploadAndUpdate($entity, UploadedFile $file): void
    {
        $entityClass = get_class($entity);
        $fileField = Consts::ENTITY_FILE_FIELDS[ $entityClass ] ?? null;
        $uploadDir = Consts::ENTITY_UPLOAD_DIRS[ $entityClass ] ?? null;

        if (!$fileField || !$uploadDir) {
            throw new \Exception("Unsupported entity {$entityClass}");
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        if ($currentFileName = $propertyAccessor->getValue($entity, $fileField)) {
            unlink("{$this->projectDir}{$uploadDir}{$currentFileName}");
        }

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move("{$this->projectDir}{$uploadDir}", $fileName);

        {
            $propertyAccessor->setValue($entity, $fileField, $fileName);
            $this->objectManager->flush();
        }
    }
}