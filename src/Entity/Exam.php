<?php

namespace App\Entity;

use App\Repository\ExamRepository;
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

#[ORM\Entity(repositoryClass: ExamRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:exam']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:exam']
        ),
        new Post(
            denormalizationContext: ['groups' => 'post:collection:exam'],
            normalizationContext: ['groups' => 'get:item:exam']
        ),
        new Patch(
            denormalizationContext: ['groups' => 'patch:item:exam'],
            normalizationContext: ['groups' => 'get:item:exam']
        ),
        new Delete(),
    ],
)]
class Exam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:exam', 'get:collection:exam'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 3)]
    #[Groups([
        'get:item:exam',
        'get:collection:exam',
        'post:collection:exam',
        'patch:item:exam'
    ])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\Length(min: 10)]
    #[Groups([
        'get:item:exam',
        'get:collection:exam',
        'post:collection:exam',
        'patch:item:exam'
    ])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:exam',
        'get:collection:exam',
        'post:collection:exam',
        'patch:item:exam'
    ])]
    private ?string $duration = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups([
        'get:item:exam',
        'get:collection:exam',
        'post:collection:exam',
        'patch:item:exam'
    ])]
    private ?int $maxGrade = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:exam',
        'get:collection:exam',
        'post:collection:exam',
        'patch:item:exam'
    ])]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Assert\NotNull]
    #[Groups([
        'get:item:exam',
        'get:collection:exam',
        'post:collection:exam',
        'patch:item:exam'
    ])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'exams')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:exam',
        'get:collection:exam',
        'post:collection:exam',
        'patch:item:exam'
    ])]
    private ?Course $course = null;

    #[ORM\OneToMany(mappedBy: 'exam', targetEntity: ExamResult::class)]
    #[Groups(['get:item:exam'])]
    private ?Collection $examResults;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
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
     * getDuration
     *
     * @return string
     */
    public function getDuration(): ?string
    {
        return $this->duration;
    }

    /**
     * setDuration
     *
     * @param  string $duration
     * @return static
     */
    public function setDuration(string $duration): static
    {
        $this->duration = $duration;

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
     * getStartDate
     *
     * @return DateTimeInterface
     */
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * setStartDate
     *
     * @param  \DateTimeInterface $startDate
     * @return static
     */
    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

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
     * getExamResults
     *
     * @return mixed
     */
    public function getExamResults(): mixed
    {
        return $this->examResults;
    }
}
