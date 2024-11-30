<?php

namespace App\Entity;

use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:user']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:user']
        ),
        new Post(
            denormalizationContext: ['groups' => 'post:collection:user'],
            normalizationContext: ['groups' => 'get:item:user']
        ),
        new Patch(
            denormalizationContext: ['groups' => 'patch:item:user'],
            normalizationContext: ['groups' => 'get:item:user']
        ),
        new Delete(),
    ],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:user', 'get:collection:user', 'get:item:department'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotNull]
    #[Assert\Email]
    #[Groups([
        'get:item:user',
        'get:collection:user',
        'post:collection:user',
        'patch:item:user',
        'get:item:department'
    ])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups([
        'post:collection:user',
        'patch:item:user'
    ])]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 2)]
    #[Groups([
        'get:item:user',
        'get:collection:user',
        'post:collection:user',
        'patch:item:user',
        'get:item:department'
    ])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 2)]
    #[Groups([
        'get:item:user',
        'get:collection:user',
        'post:collection:user',
        'patch:item:user',
        'get:item:department'
    ])]
    private ?string $lastName = null;

    #[ORM\ManyToOne(targetEntity: StudentGroup::class, inversedBy: 'students')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', onDelete: 'restrict')]
    #[Groups([
        'get:item:user',
        'get:collection:user',
        'post:collection:user',
        'patch:item:user'
    ])]
    private ?StudentGroup $studentGroup = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'teachers')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id', onDelete: 'restrict')]
    #[Groups([
        'get:item:user',
        'get:collection:user',
        'post:collection:user',
        'patch:item:user'
    ])]
    private ?Department $department = null;

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Course::class)]
    #[Groups(['get:item:user'])]
    private ?Collection $taughtCourses;

    #[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'students')]
    #[ORM\JoinTable(name: 'courses_students')]
    #[Groups(['get:item:user'])]
    private Collection $enrolledCourses;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Submission::class)]
    #[Groups(['get:item:user'])]
    private ?Collection $submissions;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: ExamResult::class)]
    #[Groups(['get:item:user'])]
    private ?Collection $examResults;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:user',
        'get:collection:user',
        'post:collection:user',
        'patch:item:user'
    ])]
    private array $roles = [];

    #[Assert\Callback]
    public function validateGroupForStudents(ExecutionContextInterface $context): void
    {
        if (in_array('ROLE_STUDENT', $this->roles, true) && $this->studentGroup === null) {
            $context->buildViolation('The group field is required for students.')
                ->atPath('student_group')
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
     * getStudentGroup
     *
     * @return StudentGroup
     */
    public function getStudentGroup(): ?StudentGroup
    {
        return $this->studentGroup;
    }

    /**
     * setStudentGroup
     *
     * @param  StudentGroup $studentGroup
     * @return static
     */
    public function setStudentGroup(StudentGroup $studentGroup): static
    {
        $this->studentGroup = $studentGroup;

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
        return $this->taughtCourses;
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
        if ($this->enrolledCourses->contains($course)) {
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
        return $this->enrolledCourses;
    }

    /**
     * getSubmissions
     *
     * @return mixed
     */
    public function getSubmissions(): mixed
    {
        return $this->submissions;
    }

    /**
     * getExamResults
     *
     * @return mixed
     */
    public function getExamResults(): mixed
    {
        return $this->examResults;
    }
}
