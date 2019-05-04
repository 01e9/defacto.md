<?php

namespace App\EventListener;

use App\Entity\BlogPost;
use App\Entity\Party;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Service\FileUploader;
use App\Entity\Politician;

class EntityFileFieldsListener
{
    private $uploader;

    private $entities = [
        Politician::class => [
            [
                'name' => 'photo',
                'dir' => '/politicians',
            ]
        ],
        Party::class => [
            [
                'name' => 'logo',
                'dir' => '/parties',
            ]
        ],
        BlogPost::class => [
            [
                'name' => 'image',
                'dir' => '/blog',
            ]
        ],
    ];

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->uploadFiles($args->getEntity());
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->removeReplaced($args->getEntity(), $args->getEntityChangeSet());
        $this->uploadFiles($args->getEntity());
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->loadFiles($args->getEntity());
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $this->loadFiles($args->getEntity());
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $this->removeFiles($args->getEntity());
    }

    private function uploadFiles($entity)
    {
        foreach ($this->entities as $entityClass => $fields) {
            if (!$entity instanceof $entityClass) {
                continue;
            }

            foreach ($fields as $field) {
                $getter = 'get' . ucfirst($field['name']);
                $setter = 'set' . ucfirst($field['name']);
                $file = $entity->{$getter}();

                if ($file instanceof UploadedFile) {
                    $entity->{$setter}(
                        $this->uploader->upload($field['dir'], $file)
                    );
                } elseif ($file instanceof File) {
                    $entity->{$setter}(
                        $file->getFilename()
                    );
                }
            }
        }
    }

    private function loadFiles($entity)
    {
        foreach ($this->entities as $entityClass => $fields) {
            if (!$entity instanceof $entityClass) {
                continue;
            }

            foreach ($fields as $field) {
                $getter = 'get' . ucfirst($field['name']);
                $setter = 'set' . ucfirst($field['name']);
                $file = $entity->{$getter}();

                if (is_string($file)) {
                    $entity->{$setter}(
                        new File($this->uploader->getTargetDir() . $field['dir'] . '/'. $file, false)
                    );
                }
            }
        }
    }

    private function removeFiles($entity)
    {
        foreach ($this->entities as $entityClass => $fields) {
            if (!$entity instanceof $entityClass) {
                continue;
            }

            foreach ($fields as $field) {
                $getter = 'get' . ucfirst($field['name']);
                $file = $entity->{$getter}();

                if ($file instanceof File) {
                    unlink($file->getRealPath());
                }
            }
        }
    }

    private function removeReplaced($entity, array $changeSet)
    {
        foreach ($this->entities as $entityClass => $fields) {
            if (!$entity instanceof $entityClass) {
                continue;
            }

            foreach ($fields as $field) {
                if (!isset($changeSet[$field['name']])) {
                    continue;
                }

                list($old, $new) = $changeSet[$field['name']];

                if (is_string($old) && $new instanceof File && $old !== $new->getFilename()) {
                    $filePath = $this->uploader->getTargetDir() . $field['dir'] . '/'. $old;

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }
    }
}