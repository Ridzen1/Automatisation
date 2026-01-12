<?php

namespace App\Console;

use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Capsule\Manager as DBManager;

class PopulateDatabaseCommand extends Command
{
    private App $app;
    private Generator $faker;
    private DBManager $db;

    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('db:populate');
        $this->setDescription('Populate database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Populate database...');

        $this->db = $this->app->getContainer()->get('db');
        $this->faker = Factory::create('fr_FR');

        $this->db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=0");
        $this->db->getConnection()->statement("TRUNCATE `employees`");
        $this->db->getConnection()->statement("TRUNCATE `offices`");
        $this->db->getConnection()->statement("TRUNCATE `companies`");
        $this->db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=1");

        $this->insertCompany(1);
        $this->insertCompany(2);

        $this->insertOffice(1, 1);
        $this->insertOffice(2, 1);
        $this->insertOffice(3, 2);
        $this->insertOffice(4, 2);

        $this->insertEmployee(1, 1);
        $this->insertEmployee(2, 2);
        $this->insertEmployee(3, 3);
        $this->insertEmployee(4, 4);
        $this->insertEmployee(5, 1);
        $this->insertEmployee(6, 2);
        $this->insertEmployee(7, 3);
        $this->insertEmployee(8, 4);

        $this->db->getConnection()->statement("update companies set head_office_id = 1 where id = 1;");
        $this->db->getConnection()->statement("update companies set head_office_id = 3 where id = 2;");

        $output->writeln('Database created successfully!');
        return 0;
    }

    private function insertCompany(int $id): void
    {
        $sql = "INSERT INTO `companies` VALUES (" .
            $id . ", " .
            $this->q($this->faker->company) . ", " .
            $this->q($this->faker->phoneNumber) . ", " .
            $this->q($this->faker->companyEmail) . ", " .
            $this->q($this->faker->url) . ", " .
            $this->q($this->faker->imageUrl(640, 480, 'business')) . ", " .
            "now(), now(), null)";

        $this->db->getConnection()->statement($sql);
    }

    private function insertOffice(int $id, int $companyId): void
    {
        $sql = "INSERT INTO `offices` VALUES (" .
            $id . ", " .
            $this->q('Bureau de ' . $this->faker->city) . ", " .
            $this->q($this->faker->streetAddress) . ", " .
            $this->q($this->faker->city) . ", " .
            $this->q($this->faker->postcode) . ", " .
            $this->q($this->faker->country) . ", " .
            $this->q($this->faker->email) . ", " .
            $this->q($this->faker->phoneNumber) . ", " .
            $companyId . ", " .
            "now(), now())";

        $this->db->getConnection()->statement($sql);
    }

    private function insertEmployee(int $id, int $officeId): void
    {
        $sql = "INSERT INTO `employees` VALUES (" .
            $id . ", " .
            $this->q($this->faker->firstName) . ", " .
            $this->q($this->faker->lastName) . ", " .
            $officeId . ", " .
            $this->q($this->faker->email) . ", " .
            $this->q($this->faker->phoneNumber) . ", " .
            $this->q($this->faker->jobTitle) . ", " .
            "now(), now())";

        $this->db->getConnection()->statement($sql);
    }

    private function q($value): string
    {
        return $value === null ? 'NULL' : $this->db->getConnection()->getPdo()->quote($value);
    }
}