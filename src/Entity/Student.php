<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'students')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', onDelete: 'restrict')]
    private ?Group $group = null;

    #[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'students')]
    #[ORM\JoinTable(name: 'students_courses')]
    private Collection $courses;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Submission::class)]
    private ?Collection $submissions;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: ExamResult::class)]
    private ?Collection $examResults;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->submissions = new ArrayCollection();
        $this->examResults = new ArrayCollection();
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
     * getEmail
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * setEmail
     *
     * @param  string $email
     * @return static
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * getPassword
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * setPassword
     *
     * @param  string $password
     * @return static
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * getFirstName
     *
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * setFirstName
     *
     * @param  string $firstName
     * @return static
     */
    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * getLastName
     *
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * setLastName
     *
     * @param  string $lastName
     * @return static
     */
    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * getGroup
     *
     * @return Group
     */
    public function getGroup(): ?Group
    {
        return $this->group;
    }

    /**
     * setGroup
     *
     * @param  Group $group
     * @return static
     */
    public function setGroup(Group $group): static
    {
        $this->group = $group;

        return $this;
    }

    /**
     * getCourses
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
     * addCourse
     *
     * @param  Course $course
     * @return self
     */
    public function addCourse(Course $course): self
    {
        if (!$this->courses->contains($course)) {
            $this->courses[] = $course;
            $course->addStudent($this);
        }

        return $this;
    }

    /**
     * removeCourse
     *
     * @param  Course $course
     * @return self
     */
    public function removeCourse(Course $course): self
    {
        if ($this->courses->contains($course)) {
            if (in_array($this, $course->getStudents())) {
                $course->removeStudent($this);
            }
            $this->courses->removeElement($course);
        }

        return $this;
    }

    /**
     * getSubmissions
     *
     * @return mixed
     */
    public function getSubmissions(): mixed
    {
        return array_map(function ($submission) {
            return [
                'id' => $submission?->getId(),
                'answer' => $submission?->getAnswer(),
                'dueDate' => $submission?->getDueDate(),
                'task' => [
                    "id" => $submission?->getTask()->getId(),
                    "title" => $submission?->getTask()->getTitle(),
                    "dueDate" => $submission?->getTask()->getDueDate(),
                ],
                'obtainedGrade' => $submission?->getObtainedGrade(),
            ];
        }, iterator_to_array($this->submissions));
    }

    /**
     * getExamResults
     *
     * @return mixed
     */
    public function getExamResults(): mixed
    {
        return array_map(function ($examResult) {
            return [
                'id' => $examResult?->getId(),
                'answer' => $examResult?->getAnswer(),
                'startDate' => $examResult?->getStartDate(),
                'exam' => [
                    "id" => $examResult?->getExam()->getId(),
                    "title" => $examResult?->getExam()->getTitle(),
                    "startDate" => $examResult?->getExam()->getStartDate(),
                ],
                'obtainedGrade' => $examResult?->getObtainedGrade(),
            ];
        }, iterator_to_array($this->examResults));
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
            'group' => [
                'id' => $this->group?->getId(),
                'name' => $this->group?->getName(),
                'faculty' => $this->group?->getMajor(),
                'year' => $this->group?->getYear(),
            ],
            'courses' => $this->getCourses()
        ];
    }
}
