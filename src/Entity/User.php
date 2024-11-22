<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JsonSerializable
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

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'students')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', onDelete: 'restrict')]
    private ?Group $group = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'teachers')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id', onDelete: 'restrict')]
    private ?Department $department = null;

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Course::class)]
    private ?Collection $taughtCourses;

    #[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'students')]
    #[ORM\JoinTable(name: 'courses_students')]
    private Collection $enrolledCourses;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Submission::class)]
    private ?Collection $submissions;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: ExamResult::class)]
    private ?Collection $examResults;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private array $roles = [];

    #[Assert\Callback]
    public function validateGroupForStudents(ExecutionContextInterface $context): void
    {
        if (in_array('ROLE_STUDENT', $this->roles, true) && $this->group === null) {
            $context->buildViolation('The group field is required for students.')
                ->atPath('group')
                ->addViolation();
        }
    }

    #[Assert\Callback]
    public function validateDepartmentForTeachers(ExecutionContextInterface $context): void
    {
        if (in_array('ROLE_TEACHER', $this->roles, true) && $this->department === null) {
            $context->buildViolation('The department field is required for teachers.')
                ->atPath('department')
                ->addViolation();
        }
    }

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->taughtCourses = new ArrayCollection();
        $this->enrolledCourses = new ArrayCollection();
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
     * @param  mixed $email
     * @return static
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

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
     * @param  mixed $firstName
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
     * @param  mixed $lastName
     * @return static
     */
    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials()
    {
        // 
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * setPassword
     *
     * @param  mixed $password
     * @return static
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

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
     * getTaughtCourses
     *
     * @return mixed
     */
    public function getTaughtCourses(): mixed
    {
        return array_map(function ($course) {
            return [
                'id' => $course?->getId(),
                'name' => $course?->getName(),
                'credits' => $course?->getCredits(),
            ];
        }, iterator_to_array($this->taughtCourses));
    }

    /**
     * addEnrolledCourse
     *
     * @param  Course $course
     * @return self
     */
    public function addEnrolledCourse(Course $course): self
    {
        if (in_array('ROLE_STUDENT', $this->getRoles(), true) && !$this->enrolledCourses->contains($course)) {
            $this->enrolledCourses[] = $course;
            $course->addStudent($this);
        }

        return $this;
    }

    /**
     * removeEnrolledCourse
     *
     * @param  Course $course
     * @return self
     */
    public function removeEnrolledCourse(Course $course): self
    {
        if (in_array('ROLE_STUDENT', $this->getRoles(), true) && $this->enrolledCourses->contains($course)) {
            if (in_array($this, $course->getStudents())) {
                $course->removeStudent($this);
            }
            $this->enrolledCourses->removeElement($course);
        }

        return $this;
    }

    /**
     * getEnrolledCourses
     *
     * @return mixed
     */
    public function getEnrolledCourses(): mixed
    {
        return array_map(function ($course) {
            return [
                'id' => $course?->getId(),
                'name' => $course?->getName(),
                'credits' => $course?->getCredits(),
            ];
        }, iterator_to_array($this->enrolledCourses));
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
            'roles' => $this->getRoles(),
            'group' => [
                'id' => $this->group?->getId(),
                'name' => $this->group?->getName(),
                'faculty' => $this->group?->getMajor(),
                'year' => $this->group?->getYear(),
            ],
            'department' => $this->department,
            'enrolledCourses' => $this->getEnrolledCourses(),
            'taughtCourses' => $this->getTaughtCourses()
        ];
    }
}
