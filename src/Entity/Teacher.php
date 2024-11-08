<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
class Teacher implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotNull]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 2)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 2)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?string $position = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'teachers')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id', onDelete: 'restrict')]
    #[Assert\NotNull]
    private ?Department $department = null;

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Course::class)]
    private ?Collection $courses;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }


    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param string $email
     * @return  self
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @param string $password
     * @return  self
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of firstName
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @param string $firstName
     * @return  self
     */
    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of lastName
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @param string $lastName
     * @return  self
     */
    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of position
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * Set the value of position
     *
     * @param string $position
     * @return  self
     */
    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the value of department
     */
    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    /**
     * Set the value of department
     *
     * @param   Department $department
     * @return  self
     */
    public function setDepartment(Department $department): static
    {
        $this->department = $department;

        return $this;
    }

    /**
     * getGroups
     *
     * @return mixed
     */
    public function getCourses(): mixed
    {
        return array_map(function ($course) {
            return [
                'id' => $course?->getId(),
                'name' => $course?->getName(),
                'description' => $course?->getDescription(),
                'credits' => $course?->getCredits(),
            ];
        }, iterator_to_array($this->courses));
    }

    /**
     * jsonSerialize
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'position' => $this->position,
            'department' => [
                'id' => $this->department?->getId(),
                'name' => $this->department?->getName(),
                'faculty' => $this->department?->getFaculty()
            ],
            'courses' => $this->getCourses()
        ];
    }
}
