<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 1)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 1)]
    private ?string $major = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    private ?int $year = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id', onDelete: 'restrict')]
    #[Assert\NotNull]
    private ?Department $department = null;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: User::class)]
    private ?Collection $students;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: ScheduleEvent::class)]
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
        return array_map(function ($student) {
            return [
                'id' => $student?->getId(),
                'email' => $student?->getEmail(),
                'firstName' => $student?->getFirstName(),
                'secondName' => $student?->getLastName(),
            ];
        }, iterator_to_array($this->students));
    }

    /**
     * getScheduleEvents
     *
     * @return mixed
     */
    public function getScheduleEvents(): mixed
    {
        return array_map(function ($scheduleEvent) {
            return [
                'id' => $scheduleEvent?->getId(),
                'meetingLink' => $scheduleEvent?->getMeetingLink(),
                'startDate' => $scheduleEvent?->getStartDate()->format('Y-m-d H:i'),
                'endDate' => $scheduleEvent?->getEndDate()->format('Y-m-d H:i'),
            ];
        }, iterator_to_array($this->scheduleEvents));
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
            'major' => $this->major,
            'year' => $this->year,
            'department' => [
                'id' => $this->department?->getId(),
                'name' => $this->department?->getName(),
                'faculty' => $this->department?->getFaculty()
            ],
            'students' => $this->getStudents(),
        ];
    }
}
