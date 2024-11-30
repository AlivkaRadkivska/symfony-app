<?php

namespace App\Entity;

use App\Repository\StudentGroupRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudentGroupRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:student-group']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:student-group']
        ),
        new Post(
            denormalizationContext: ['groups' => 'post:collection:student-group'],
            normalizationContext: ['groups' => 'get:item:student-group']
        ),
        new Patch(
            denormalizationContext: ['groups' => 'patch:item:student-group'],
            normalizationContext: ['groups' => 'get:item:student-group']
        ),
        new Delete(),
    ],
)]
class StudentGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'get:item:student-group',
        'get:collection:student-group',
        'get:item:department'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 1)]
    #[Groups([
        'get:item:student-group',
        'get:collection:student-group',
        'post:collection:student-group',
        'patch:item:student-group',
        'get:item:department'
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 1)]
    #[Groups([
        'get:item:student-group',
        'get:collection:student-group',
        'post:collection:student-group',
        'patch:item:student-group'
    ])]
    private ?string $major = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Groups([
        'get:item:student-group',
        'get:collection:student-group',
        'post:collection:student-group',
        'patch:item:student-group',
        'get:item:department'
    ])]
    private ?int $year = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'studentGroups')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id', onDelete: 'restrict')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:student-group',
        'get:collection:student-group',
        'post:collection:student-group',
        'patch:item:student-group'
    ])]
    private ?Department $department = null;

    #[ORM\OneToMany(mappedBy: 'studentGroup', targetEntity: User::class)]
    #[Groups(['get:item:student-group'])]
    private ?Collection $students;

    #[ORM\OneToMany(mappedBy: 'studentGroup', targetEntity: ScheduleEvent::class)]
    #[Groups(['get:item:student-group'])]
    private ?Collection $scheduleEvents;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->scheduleEvents = new ArrayCollection();
    }

    /**
     * getId
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * setName
     *
     * @param  string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * getMajor
     *
     * @return string
     */
    public function getMajor(): ?string
    {
        return $this->major;
    }

    /**
     * setMajor
     *
     * @param  string $major
     * @return static
     */
    public function setMajor(string $major): static
    {
        $this->major = $major;

        return $this;
    }

    /**
     * getYear
     *
     * @return int
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * setYear
     *
     * @param  int $year
     * @return static
     */
    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }


    /**
     * getDepartment
     *
     * @return Department
     */
    public function getDepartment(): ?Department
    {
        return $this->department;
    }


    /**
     * setDepartment
     *
     * @param  Department $department
     * @return static
     */
    public function setDepartment(Department $department): static
    {
        $this->department = $department;

        return $this;
    }

    /**
     * getStudents
     *
     * @return mixed
     */
    public function getStudents(): mixed
    {
        return $this->students;
    }

    /**
     * getScheduleEvents
     *
     * @return mixed
     */
    public function getScheduleEvents(): mixed
    {
        return $this->scheduleEvents;
    }
}
