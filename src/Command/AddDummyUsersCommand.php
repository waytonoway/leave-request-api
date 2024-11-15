<?php

namespace App\Command;


use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddDummyUsersCommand extends Command
{
    protected static $defaultName = "system:dummy-users:add";

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct();

        $this->entityManager = $em;
    }

    public function configure() {
        $this
            ->setName(self::$defaultName)
            ->setDescription("Add dummy users to DB")
            ->addArgument("amount", InputArgument::OPTIONAL, "Provide the number of dummy users to add to DB", 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $amount = $input->getArgument("amount");

        for ($i = 0; $i < $amount; $i++) {
            $user = (new User())
                ->setFirstName($this->generateName())
                ->setLastName($this->generateName(false))
                ->setMiddleName($i % 3 === 0 ? $this->generateName() : null);

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        $output->writeln($amount . " users added.");

        return 1;
    }

    private function generateName(bool $isFirst = true): string {
        $firstNames = [
            "John", "Jane", "Michael", "Emily", "David", "Sarah", "James", "Sophia",
            "Matthew", "Isabella", "Ethan", "Ava", "Lucas", "Charlotte", "Liam"
        ];

        $lastNames = [
            "Doe", "Smith", "Johnson", "Davis", "Wilson", "Miller", "Moore", "Taylor",
            "Anderson", "Thomas", "Jackson", "White", "Harris", "Martin", "Lee"
        ];

        return $isFirst ? $firstNames[array_rand($firstNames)] : $lastNames[array_rand($lastNames)];
    }
}
