<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="leave_request", indexes={
 *        @ORM\Index(columns={"start_date"}),
 *        @ORM\Index(columns={"end_date"}),
 *        @ORM\Index(columns={"reason"}),
 *        @ORM\Index(columns={"user_id", "start_date", "end_date"}),
 *
 *   })
 * @ORM\Entity(repositoryClass="App\Repository\LeaveRequestRepository")
 */
class LeaveRequest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"leave_request"})
     */
    private $id;

    /**
     * @ORM\Column(name="start_date", type="datetime")
     * @Serializer\Groups({"leave_request"})
    */
    private $startDate;

    /**
     * @ORM\Column(name="end_date", type="datetime")
     * @Serializer\Groups({"leave_request"})
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LeaveType")
     * @ORM\JoinColumn(name="leave_type_id", nullable=false, referencedColumnName="id")
     * @Serializer\Groups({"leave_request"})
     */
    private $leaveType;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"leave_request"})
     */
    private $reason;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", nullable=false, referencedColumnName="id")
     * @Serializer\Groups({"leave_request"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLeaveType(): ?LeaveType
    {
        return $this->leaveType;
    }

    public function setLeaveType(?LeaveType $leaveType): self
    {
        $this->leaveType = $leaveType;

        return $this;
    }

    public function getNumberOfDays(): ?float
    {
        return $this->numberOfDays;
    }

    public function setNumberOfDays(float $numberOfDays): self
    {
        $this->numberOfDays = $numberOfDays;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
