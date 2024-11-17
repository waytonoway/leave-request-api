<?php
namespace App\Entity;

use App\Repository\LeaveTypeRepository;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LeaveTypeRepository")
 * @ORM\Table(name="leave_type", indexes={
 *       @ORM\Index(columns={"type"})
 *  })
 */
class LeaveType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"leave_type"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Serializer\Groups({"leave_type"})
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}

