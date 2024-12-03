<?php

namespace App\Entity;

use App\Repository\SubmissionRepository;
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
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubmissionRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:submission']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:submission']
        ),
        new Post(
            denormalizationContext: ['groups' => 'post:collection:submission'],
            normalizationContext: ['groups' => 'get:item:submission']
        ),
        new Patch(
            denormalizationContext: ['groups' => 'patch:item:submission'],
            normalizationContext: ['groups' => 'get:item:submission']
        ),
        new Delete(),
    ],
)]
class Submission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:submission', 'get:collection:submission', 'get:item:task'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:submission',
        'get:collection:submission',
        'post:collection:submission',
        'patch:item:submission'
    ])]
    private ?string $answer = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Groups([
        'get:item:submission',
        'get:collection:submission',
        'post:collection:submission',
        'patch:item:submission',
        'get:item:task'
    ])]
    private ?int $obtainedGrade = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Assert\NotNull]
    #[Groups([
        'get:item:submission',
        'get:collection:submission',
        'post:collection:submission',
        'patch:item:submission'
    ])]
    private ?\DateTimeInterface $doneDate = null;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'submissions')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:submission',
        'get:collection:submission',
        'post:collection:submission',
        'patch:item:submission'
    ])]
    private ?Task $task = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'submissions')]
    #[ORM\JoinColumn(name: 'student_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:submission',
        'get:collection:submission',
        'post:collection:submission',
        'patch:item:submission',
        'get:item:task'
    ])]
    private ?User $student = null;

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
     * getAnswer
     *
     * @return string
     */
    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    /**
     * setAnswer
     *
     * @param  mixed $answer
     * @return static
     */
    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * getObtainedGrade
     *
     * @return int
     */
    public function getObtainedGrade(): ?int
    {
        return $this->obtainedGrade;
    }

    /**
     * setObtainedGrade
     *
     * @param  int $obtainedGrade
     * @return static
     */
    public function setObtainedGrade(int $obtainedGrade): static
    {
        $this->obtainedGrade = $obtainedGrade;

        return $this;
    }

    /**
     * getDoneDate
     *
     * @return \DateTimeInterface
     */
    public function getDoneDate(): ?\DateTimeInterface
    {
        return $this->doneDate;
    }

    /**
     * setDoneDate
     *
     * @param  \DateTimeInterface $doneDate
     * @return static
     */
    public function setDoneDate(\DateTimeInterface $doneDate): static
    {
        $this->doneDate = $doneDate;

        return $this;
    }

    /**
     * getTask
     *
     * @return Task
     */
    public function getTask(): ?Task
    {
        return $this->task;
    }

    /**
     * Set the value of task
     *
     * @return  self
     */
    public function setTask(Task $task): static
    {
        $this->task = $task;

        return $this;
    }

    /**
     * getStudent
     *
     * @return Student
     */
    public function getStudent(): ?User
    {
        return $this->student;
    }

    /**
     * Set the value of student
     *
     * @return  self
     */
    public function setStudent($student): static
    {
        $this->student = $student;

        return $this;
    }
}
