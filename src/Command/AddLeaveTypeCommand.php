<?php

namespace App\Command;

use App\Entity\LeaveType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddLeaveTypeCommand extends Command
{
    protected static $defaultName = "system:leave-type:add";

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct();

        $this->entityManager = $em;
    }

    public function configure() {
        $this
            ->setName(self::$defaultName)
            ->setDescription("Add leave type(s) to DB")
            ->addArgument("types", InputArgument::REQUIRED, "Provide required names of types separated by comma")
            ->setHelp(
                "This command will add provided leave types to DB.".PHP_EOL.PHP_EOL.
                "Types should be provided as a string and values should be divided by comma. ".PHP_EOL.
                "<comment>bin/console system:leave-type:add \"personal,sick\"</comment>"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $typesInput = $input->getArgument("types");
        $types = array_filter(explode(",", $typesInput));

        if (empty($types)) {
            $output->writeln("<comment>No leave types provided.</comment>");

            return 0;
        }

        $types = $this->sanitiseTypes($types, $output);

        if (!empty($types)) {
            $this->addLeaveTypes($types);
        }

        $output->writeln(count($types) . " types added.");

        return 1;
    }

    private function sanitiseTypes(array $types, OutputInterface $output): array {
        $existingTypes = $this->entityManager->getRepository(LeaveType::class)
            ->findByType($types);

        if (!empty($existingTypes)) {
            $names = array_map(fn ($el) => $el->getType(), $existingTypes);

            $output->writeln("<error>Leave type(s) " . implode(",", $names) ." already exist.</error>");

            return array_diff($types, $names);
        }

        return $types;
    }

    private function addLeaveTypes(array $types) {
        foreach ($types as $type) {
            $leaveType = (new LeaveType())
                ->setType($type);
            $this->entityManager->persist($leaveType);
        }

        $this->entityManager->flush();
    }
}
