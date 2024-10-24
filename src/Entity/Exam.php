<?php

namespace App\Entity;

use App\Repository\ExamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ExamRepository::class)]
class Exam implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $duration = null;

    #[ORM\Column(length: 255)]
    private ?string $maxGrade = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'exams')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', onDelete: 'cascade')]
    private ?Course $course = null;

    #[ORM\OneToMany(mappedBy: 'exam', targetEntity: ExamResult::class)]
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
     * @return string
     */
    public function getMaxGrade(): ?string
    {
        return $this->maxGrade;
    }

    /**
     * setMaxGrade
     *
     * @param  string $maxGrade
     * @return static
     */
    public function setMaxGrade(string $maxGrade): static
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
        return array_map(function ($examResult) {
            return [
                'id' => $examResult?->getId(),
                'answer' => $examResult?->getAnswer(),
                'startDate' => $examResult?->getStartDate(),
                'student' => [
                    "id" => $examResult?->getStudent()->getId(),
                    "firstName" => $examResult?->getStudent()->getFirstName(),
                    "lastName" => $examResult?->getStudent()->getLastName(),
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
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "maxGrade" => $this->maxGrade,
            "type" => $this->type,
            "duration" => $this->duration,
            "startDate" => $this->startDate->format("Y-m-d H:i"),
            "course" => [
                "id" => $this->course?->getId(),
                "name" => $this->course?->getName(),
            ],
        ];
    }
}
