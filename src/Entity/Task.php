<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:task']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:task']
        ),
        new Post(
            denormalizationContext: ['groups' => 'post:collection:task'],
            normalizationContext: ['groups' => 'get:item:task']
        ),
        new Patch(
            denormalizationContext: ['groups' => 'patch:item:task'],
            normalizationContext: ['groups' => 'get:item:task']
        ),
        new Delete(),
    ],
)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:task', 'get:collection:task'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 5)]
    #[Groups([
        'get:item:task',
        'get:collection:task',
        'post:collection:task',
        'patch:item:task'
    ])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 10)]
    #[Groups([
        'get:item:task',
        'get:collection:task',
        'post:collection:task',
        'patch:item:task'
    ])]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive()]
    #[Groups([
        'get:item:task',
        'get:collection:task',
        'post:collection:task',
        'patch:item:task'
    ])]
    private ?int $maxGrade = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:task',
        'get:collection:task',
        'post:collection:task',
        'patch:item:task'
    ])]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Assert\NotNull]
    #[Groups([
        'get:item:task',
        'get:collection:task',
        'post:collection:task',
        'patch:item:task'
    ])]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:task',
        'get:collection:task',
        'post:collection:task',
        'patch:item:task'
    ])]
    private ?Course $course = null;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Submission::class)]
    #[Groups(['get:item:task'])]
    private ?Collection $submissions;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->submissions = new ArrayCollection();
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
     * getTitle
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * setTitle
     *
     * @param  string $title
     * @return static
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

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
     * getMaxGrade
     *
     * @return int
     */
    public function getMaxGrade(): ?int
    {
        return $this->maxGrade;
    }

    /**
     * setMaxGrade
     *
     * @param  int $maxGrade
     * @return static
     */
    public function setMaxGrade(int $maxGrade): static
    {
        $this->maxGrade = $maxGrade;

        return $this;
    }

    /**
     * getType
     *
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * setType
     *
     * @param  string $type
     * @return static
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * getDueDate
     *
     * @return DateTimeInterface
     */
    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    /**
     * setDueDate
     *
     * @param  \DateTimeInterface $dueDate
     * @return static
     */
    public function setDueDate(\DateTimeInterface $dueDate): static
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * getCourse
     *
     * @return Course
     */
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    /**
     * setCourse
     *
     * @param  Course $course
     * @return static
     */
    public function setCourse(Course $course): static
    {
        $this->course = $course;

        return $this;
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
}
