<?php

namespace App\EventListener;

use App\Consts;
use App\Entity\Action;
use App\Entity\ActionSource;
use App\Entity\Category;
use App\Entity\Log;
use App\Entity\Power;
use App\Entity\Promise;
use App\Entity\PromiseSource;
use App\Entity\PromiseUpdate;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrineLogsListener
{
    private $promiseDataBefore = [];
    private $actionDataBefore = [];

    //region Public Methods

    //region Data Before Setters

    public function addPromiseDataBefore(Promise $object)
    {
        $this->promiseDataBefore[$object->getId()] = $this->stringifyPromise($object);
    }

    public function addActionDataBefore(Action $object)
    {
        $this->actionDataBefore[$object->getId()] = $this->stringifyAction($object);
    }

    //endregion

    //region Events

    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $objectManager = $args->getObjectManager();

        $log = new Log();

        switch (true) {
            case ($object instanceof Promise):
                $log
                    ->setDescription('Promise created')
                    ->setObjectType('promise')
                    ->setObjectId($object->getId())
                    ->setDataAfter($this->stringifyPromise($object));
                break;
            case ($object instanceof Action):
                $log
                    ->setDescription('Action created')
                    ->setObjectType('action')
                    ->setObjectId($object->getId())
                    ->setDataAfter($this->stringifyAction($object));
                break;
        }

        if ($log->getDescription()) {
            $objectManager->persist($log);
            $objectManager->flush($log);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $objectManager = $args->getObjectManager();

        $log = new Log();

        if ($object instanceof Promise) {
            if (empty($this->promiseDataBefore[$object->getId()])) {
                return;
            }

            $log
                ->setDescription('Promise updated')
                ->setObjectType('promise')
                ->setObjectId($object->getId())
                ->setDataBefore($this->promiseDataBefore[$object->getId()])
                ->setDataAfter($this->stringifyPromise($object));

            unset($this->promiseDataBefore[$object->getId()]);
        } else if ($object instanceof Action) {
            if (empty($this->actionDataBefore[$object->getId()])) {
                return;
            }

            $log
                ->setDescription('Action updated')
                ->setObjectType('action')
                ->setObjectId($object->getId())
                ->setDataBefore($this->actionDataBefore[$object->getId()])
                ->setDataAfter($this->stringifyAction($object));

            unset($this->actionDataBefore[$object->getId()]);
        }

        if ($log->getDescription() && $log->getDataBefore() !== $log->getDataAfter()) {
            $objectManager->persist($log);
            $objectManager->flush($log);
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
    }

    //endregion

    //endregion

    //region Private Methods

    //region Stringify

    private function stringify(array $data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            $result[] = "█ $key\n$value";
        }

        return join("\n\n", $result);
    }

    private function stringifyPromise(Promise $entity)
    {
        $data = [
            'Name' => $entity->getName(),
            'Status' => $entity->getStatus() ? $entity->getStatus()->getName() : '',
            'Published' => $entity->getPublished() ? '+' : '-',
            'Slug' => $entity->getSlug(),
            'Description' => $entity->getDescription(),
            'Date' => $entity->getMadeTime()->format(Consts::DATE_FORMAT_PHP),
            'Categories' => array_reduce(
                $entity->getCategories()->toArray(),
                function ($text, Category $category) {
                    return $text . (empty($text) ? '' : "\n") . $category->getName();
                },
                ''
            ),
            'Sources' => array_reduce(
                $entity->getSources()->toArray(),
                function ($text, PromiseSource $source) {
                    return $text . (empty($text) ? '' : "\n") . $source->getName() . ' ' . $source->getLink();
                },
                ''
            )
        ];

        return $this->stringify($data);
    }

    private function stringifyAction(Action $entity)
    {
        $data = [
            'Name' => $entity->getName(),
            'Published' => $entity->getPublished() ? '+' : '-',
            'Slug' => $entity->getSlug(),
            'Description' => $entity->getDescription(),
            'Date' => $entity->getOccurredTime()->format(Consts::DATE_FORMAT_PHP),
            'Sources' => array_reduce(
                $entity->getSources()->toArray(),
                function ($text, ActionSource $source) {
                    return (
                        $text
                        . (empty($text) ? '' : "\n")
                        . $source->getName()
                        . ' '
                        . $source->getLink()
                    );
                },
                ''
            ),
            'Powers' => array_reduce(
                $entity->getUsedPowers()->toArray(),
                function ($text, Power $power) {
                    return $text . (empty($text) ? '' : "\n") . $power->getName();
                },
                ''
            ),
            'Promises' => array_reduce(
                $entity->getPromiseUpdates()->toArray(),
                function ($text, PromiseUpdate $promiseUpdate) {
                    return (
                        $text
                        . (empty($text) ? '' : "\n")
                        . $promiseUpdate->getPromise()->getName()
                        . ' » '
                        . ($promiseUpdate->getStatus() ? $promiseUpdate->getStatus()->getName() : '·')
                    );
                },
                ''
            )
        ];

        return $this->stringify($data);
    }

    //endregion

    //endregion
}