<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="user", indexes={
 *     @ORM\Index(columns={"last_name"}),
 *     @ORM\Index(columns={"email"}),
 *     @ORM\Index(columns={"position"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"user"})
     */
    private $id;

    /**
     * @ORM\Column(name="first_name", type="string", length=255)
     * @Serializer\Groups({"user"})
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=255)
     * @Serializer\Groups({"user"})
     */
    private $lastName;

    /**
     * @ORM\Column(name="middle_name", type="string", length=255, nullable=true)
     * @Serializer\Groups({"user"})
     */
    private $middleName;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Serializer\Groups({"user"})
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Groups({"user"})
     */
    private $email;

    // Getter and Setter for `name`
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $name): self
    {
        $this->firstName = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $name): self
    {
        $this->lastName = $name;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $name): self
    {
        $this->middleName = $name;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
