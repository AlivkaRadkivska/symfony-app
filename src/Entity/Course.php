<?php

namespace App\Entity;

use App\Repository\CourseRepository;
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

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:course']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:course']
        ),
        new Post(
            denormalizationContext: ['groups' => 'post:collection:course'],
            normalizationContext: ['groups' => 'get:item:course']
        ),
        new Patch(
            denormalizationContext: ['groups' => 'patch:item:course'],
            normalizationContext: ['groups' => 'get:item:course']
        ),
        new Delete(),
    ],
)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:course', 'get:collection:course'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 3)]
    #[Groups([
        'get:item:course',
        'get:collection:course',
        'post:collection:course',
        'patch:item:course'
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 10)]
    #[Groups([
        'get:item:course',
        'get:collection:course',
        'post:collection:course',
        'patch:item:course'
    ])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:course',
        'get:collection:course',
        'post:collection:course',
        'patch:item:course'
    ])]
    private ?string $credits = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'taughtCourses')]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id', onDelete: 'restrict')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:course',
        'get:collection:course',
        'post:collection:course',
        'patch:item:course'
    ])]
    private ?User $teacher = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'enrolledCourses')]
    #[ORM\JoinTable(name: 'courses_students')]
    #[Groups([
        'get:item:course',
    ])]
    private Collection $students;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Task::class)]
    #[Groups([
        'get:item:course',
    ])]
    private ?Collection $tasks;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Exam::class)]
    #[Groups([
        'get:item:course',
    ])]
    private ?Collection $exams;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: ScheduleEvent::class)]
    #[Groups([
        'get:item:course',
    ])]
    private ?Collection $scheduleEvents;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->exams = new ArrayCollection();
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
     * getDescription
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * setDescription
     *
     * @param  string $description
     * @return static
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * getCredits
     *
     * @return string
     */
    public function getCredits(): ?string
    {
        return $this->credits;
    }

    /**
     * setCredits
     *
     * @param  string $credits
     * @return static
     */
    public function setCredits(string $credits): static
    {
        $this->credits = $credits;

        return $this;
    }

    /**
     * getTeacher
     *
     * @return Teacher
     */
    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    /**
     * setTeacher
     *
     * @param  User $teacher
     * @return static
     */
    public function setTeacher(User $teacher): self
    {
        if (in_array('ROLE_TEACHER', $teacher->getRoles(), true)) {
            throw new \InvalidArgumentException('Assigned user must have the role of "teacher".');
        }
        $this->teacher = $teacher;
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
     * addStudent
     *
     * @param  User $student
     * @return self
     */
    public function addStudent(User $student): self
    {
        if (in_array('ROLE_STUDENT', $student->getRoles(), true) && !$this->students->contains($student)) {
            $this->students[] = $student;
            $student->addEnrolledCourse($this);
        }

        return $this;
    }

    /**
     * removeStudent
     *
     * @param  User $student
     * @return self
     */
    public function removeStudent(User $student): self
    {
        if ($this->students->contains($student)) {
            $student->removeEnrolledCourse($this);
            $this->students->removeElement($student);
        }

        return $this;
    }

    /**
     * getTasks
     *
     * @return mixed
     */
    public function getTasks(): mixed
    {
        return $this->tasks;
    }

    /**
     * getExams
     *
     * @return mixed
     */
    public function getExams(): mixed
    {
        return $this->exams;
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
