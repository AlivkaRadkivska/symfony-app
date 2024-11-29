<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:department']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:department']
        ),
        new Post(
            denormalizationContext: ['groups' => 'post:collection:department'],
            normalizationContext: ['groups' => 'get:item:department']
        ),
        new Patch(
            denormalizationContext: ['groups' => 'patch:item:department'],
            normalizationContext: ['groups' => 'get:item:department']
        ),
        new Delete(),
    ],
)]
class Department implements JsonSerializable
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['get:item:department', 'get:collection:department'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull, Assert\Length(min: 3)]
    #[Groups([
        'get:item:department',
        'get:collection:department',
        'post:collection:department',
        'patch:item:department'
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull, Assert\Length(min: 3)]
    #[Groups([
        'get:item:department',
        'get:collection:department',
        'post:collection:department',
        'patch:item:department'
    ])]
    private ?string $faculty = null;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: User::class)]
    #[Assert\NotNull]
    #[Groups([
        'get:item:department',
    ])]
    private ?Collection $teachers;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: Group::class)]
    #[Assert\NotNull]
    #[Groups([
        'get:item:department',
    ])]
    private ?Collection $groups;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->teachers = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param string $name
     * @return  self
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of faculty
     */
    public function getFaculty(): ?string
    {
        return $this->faculty;
    }

    /**
     * Set the value of faculty
     *
     * @param string $faculty
     * @return  self
     */
    public function setFaculty(string $faculty): static
    {
        $this->faculty = $faculty;

        return $this;
    }

    /**
     * Get the value of teachers
     * 
     * @return mixed
     */
    public function getTeachers(): mixed
    {
        return array_map(function ($teacher) {
            return [
                'id' => $teacher?->getId(),
                'email' => $teacher?->getEmail(),
                'firstName' => $teacher?->getFirstName(),
                'secondName' => $teacher?->getLastName(),
            ];
        }, iterator_to_array($this->teachers));
    }

    /**
     * getGroups
     *
     * @return mixed
     */
    public function getGroups(): mixed
    {
        return array_map(function ($group) {
            return [
                'id' => $group?->getId(),
                'name' => $group?->getName(),
                'major' => $group?->getMajor(),
                'year' => $group?->getYear(),
            ];
        }, iterator_to_array($this->groups));
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
            'name' => $this->name,
            'faculty' => $this->faculty,
            'teachers' => $this->getTeachers(),
            'groups' => $this->getGroups(),
        ];
    }
}
