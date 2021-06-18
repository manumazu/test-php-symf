<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DatabaseTest extends KernelTestCase
{
    private $databaseConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->databaseConnection = parent::getContainer()->get('database_connection');
    }

    public function testDatabaseIsUp(): void
    {
        self::assertEquals(1, $this->databaseConnection->query('SELECT 1;')->fetchColumn(0), 'Connection with data could not be established');
    }
}