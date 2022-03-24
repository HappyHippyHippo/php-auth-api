<?php

namespace App\Tests;

use Doctrine\Bundle\FixturesBundle\Fixture as BaseFixture;
use Doctrine\Persistence\ObjectManager;
use Hippy\Model\Model;

class Fixtures extends BaseFixture
{
    /** @var array<int, mixed> */
    protected array $records;

    public function __construct()
    {
        $this->clear();
    }

    public function clear(): void
    {
        $this->records = [];
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->records);
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach ($this->records as $record) {
            $manager->persist($record);
        }

        $manager->flush();
    }

    /**
     * @param Model $record
     * @return Model
     */
    public function add(Model $record): Model
    {
        $this->records[] = $record;
        return $record;
    }
}
