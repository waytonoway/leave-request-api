<?php
namespace App\Command;

ini_set("memory_limit", "1024M");

use App\Entity\LeaveRequest;
use App\Entity\LeaveType;

use App\Entity\User;
use Doctrine\DBAL\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Faker\Factory as FakerFactory;

class GenerateLeaveRequestsCommand extends Command
{
    protected static $defaultName = "system:leave-request:generate";


    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em) {
        parent::__construct();

        $this->entityManager = $em;
    }

    public function configure() {
        $this
            ->setName(self::$defaultName)
            ->setDescription("Generate fake leave requests")
            ->addArgument("amount", InputArgument::OPTIONAL, "Amount of request, 10000 by default", 10000);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $amount = $input->getArgument("amount");
        $faker = FakerFactory::create();

        $users = $this->entityManager->getRepository(User::class)->findAll();
        $leaveTypes = $this->entityManager->getRepository(LeaveType::class)->findAll();

        if (empty($users) || empty($leaveTypes)) {
            $output->writeln("<error>Ensure users and leave types exist in the database.</error>");

            return Command::FAILURE;
        }

        $output->writeln("<info>Starting to generate leave requests...</info>");
        $sql = "INSERT INTO leave_request (start_date, end_date, leave_type_id, reason, user_id) 
        VALUES (:start_date, :end_date, :leave_type_id, :reason, :user_id)";
        $stmt = $this->entityManager->getConnection()->prepare($sql);

        $batchSize = 100;
        for ($i = 0; $i < $amount; $i++) {
            $user = $faker->randomElement($users);
            $leaveType = $faker->randomElement($leaveTypes);

            $startDate = $faker->dateTimeBetween("-2 year", "-1 day");
            $endDate = (clone $startDate)->modify("+" . $faker->numberBetween(1, 21) . " days");

            $overlapping = $this->checkOverlapping($user, $startDate, $endDate);
            if (!empty($overlapping)) {
                $i--;

                continue;
            }

            $leaveRequest = (new LeaveRequest())
                ->setUser($user)
                ->setLeaveType($leaveType)
                ->setStartDate($startDate)
                ->setEndDate($endDate)
                ->setReason($faker->sentence(6, true));

            // due to possible memory issue on big amount of records switch to naive sql: method addRecords
            // $this->entityManager->persist($leaveRequest);

            $leaveRequests[] = $leaveRequest;
            if ($i > 0 && $i % $batchSize === 0) {
                $this->addRecords($stmt, $leaveRequests);

                $leaveRequests = [];
                $output->writeln("Inserted {$i} leave requests...");

                gc_collect_cycles();

            }
        }

        if (!empty($leaveRequests)) {
            $this->addRecords($stmt, $leaveRequests);
        }

        $output->writeln("<info>Successfully generated {$amount} leave requests!</info>");

        return Command::SUCCESS;
    }

    private function addRecords($stmt, array $leaveRequests) {
        foreach ($leaveRequests as $request) {
            $stmt->executeQuery([
                "start_date" => $request->getStartDate()->format("Y-m-d H:i:s"),
                "end_date" => $request->getEndDate()->format("Y-m-d H:i:s"),
                "leave_type_id" => $request->getLeaveType()->getId(),
                "reason" => $request->getReason(),
                "user_id" => $request->getUser()->getId(),
            ]);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    private function checkOverlapping(User $user, \DateTime $startDate, \DateTime $endDate) {
        $connection = $this->entityManager->getConnection();

        $sql = "
            SELECT *
            FROM leave_request lr
            WHERE lr.user_id = :user_id
              AND (
                lr.start_date BETWEEN :start_date AND :end_date
                OR lr.end_date BETWEEN :start_date AND :end_date
              )
            LIMIT 1
        ";

        $params = [
            "user_id" => $user->getId(),
            "start_date" => $startDate->format("Y-m-d H:i:s"),
            "end_date" => $endDate->format("Y-m-d H:i:s"),
        ];

        return $connection->fetchOne($sql, $params);
    }
}
