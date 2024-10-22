<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $maxGrade = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', onDelete: 'cascade')]
    private ?Course $course = null;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Submission::class)]
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
     * @param  DateTimeInterface $dueDate
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
        return array_map(function ($submission) {
            return [
                'id' => $submission?->getId(),
                'answer' => $submission?->getAnswer(),
                'dueDate' => $submission?->getDueDate(),
                'student' => [
                    "id" => $submission?->getStudent()->getId(),
                    "firstName" => $submission?->getStudent()->getFirstName(),
                    "lastName" => $submission?->getStudent()->getLastName(),
                ],
                'obtainedGrade' => $submission?->getObtainedGrade(),
            ];
        }, iterator_to_array($this->submissions));
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
            "dueDate" => $this->dueDate->format("Y-m-d H:i"),
            "course" => [
                "id" => $this->course?->getId(),
                "name" => $this->course?->getName(),
            ],
        ];
    }
}
