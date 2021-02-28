<?php

namespace App\Repository;

use App\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class SettingRepository extends ServiceEntityRepository
{
    // todo: move to env (and delete Setting)
    const PRESIDENT_INSTITUTION_TITLE_ID = 'president_institution_title_id';
    const CURRENT_ELECTION_ID = 'current_election_id';

    private static $whitelist = [
        self::PRESIDENT_INSTITUTION_TITLE_ID => [
            'type' => 'App:InstitutionTitle',
            'name' => 'Funcția de președinte',
            'default' => false,
        ],
        self::CURRENT_ELECTION_ID => [
            'type' => 'App:Election',
            'name' => 'Alegerile curente',
            'default' => false,
        ],
    ];

    public static function getWhiteList() : array
    {
        return self::$whitelist;
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    public function getConfig($id)
    {
        return self::$whitelist[$id] ?? null;
    }

    public function get($id, $throw = true)
    {
        /** @var Setting $setting */
        $setting = parent::find($id);

        if (!isset(self::$whitelist[$id])) {
            throw new \RuntimeException("Unknown setting: " . $id);
        }

        $config = self::$whitelist[$id];

        if (!$setting) {
            if (is_null($config['default'])) {
                if ($throw) {
                    throw new \RuntimeException("Undefined setting: " . $id);
                } else {
                    return null;
                }
            } else {
                return $config['default'];
            }
        }

        return $this->applyType($setting->getValue(), $config, $throw);
    }

    public function getAdminList(Request $request)
    {
        $list = self::$whitelist;

        foreach ($this->findAll() as $setting) { /** @var Setting $setting */
            $id = $setting->getId();
            if (isset($list[$id])) {
                $list[$id]['value'] = $this->applyType($setting->getValue(), $list[$id], false);
            }
        }

        foreach ($list as $id => $setting) {
            if (!array_key_exists('value', $setting)) {
                $list[$id]['value'] = $this->applyType(null, $setting, false);
            }
        }

        return $list;
    }

    private function applyType($value, array $config, $throw = true)
    {
        switch ($config['type']) {
            case 'string':
                return $value;
            case 'App:InstitutionTitle':
            case 'App:Election':
                $entity = $value
                    ? $this->getEntityManager()->getRepository($config['type'])->find($value)
                    : $value;

                if ($entity) {
                    return $entity;
                } elseif (is_null($config['default'])) {
                    if ($throw) {
                        throw new \RuntimeException("Setting entity not found. Type: ". $config['type']);
                    } else {
                        return null;
                    }
                } else {
                    return $config['default'];
                }
            default:
                throw new \RuntimeException("Unknown setting type: " . $config['type']);
        }
    }
}
