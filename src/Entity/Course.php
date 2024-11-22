<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 3)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 10)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?string $credits = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'taughtCourses')]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id', onDelete: 'restrict')]
    #[Assert\NotNull]
    private ?User $teacher = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'enrolledCourses')]
    #[ORM\JoinTable(name: 'courses_students')]
    private Collection $students;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Task::class)]
    private ?Collection $tasks;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Exam::class)]
    private ?Collection $exams;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: ScheduleEvent::class)]
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
        return array_map(function ($student) {
            return [
                'id' => $student?->getId(),
                'email' => $student?->getEmail(),
                'firstName' => $student?->getFirstName(),
                'lastName' => $student?->getLastName(),
                'group' => $student?->getGroup()->getName(),
            ];
        }, iterator_to_array($this->students));
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
        return array_map(function ($task) {
            return [
                'id' => $task?->getId(),
                'name' => $task?->getTitle(),
                'dueDate' => $task?->getDueDate(),
            ];
        }, iterator_to_array($this->tasks));
    }

    /**
     * getExams
     *
     * @return mixed
     */
    public function getExams(): mixed
    {
        return array_map(function ($exam) {
            return [
                'id' => $exam?->getId(),
                'name' => $exam?->getTitle(),
                'startDate' => $exam?->getStartDate(),
            ];
        }, iterator_to_array($this->exams));
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
            'description' => $this->description,
            'credits' => $this->credits,
            'teacher' => [
                'id' => $this->teacher?->getId(),
                'email' => $this->teacher?->getEmail(),
                'firstName' => $this->teacher?->getFirstName(),
                'lastName' => $this->teacher?->getLastName(),
            ],
            'students' => $this->getStudents()
        ];
    }
}
