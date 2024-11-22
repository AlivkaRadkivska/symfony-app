<?php

namespace App\Entity;

use App\Repository\SubmissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubmissionRepository::class)]
class Submission implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?string $answer = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    private ?int $obtainedGrade = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    #[Assert\DateTime]
    private ?\DateTimeInterface $doneDate = null;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'submissions')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    private ?Task $task = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'submissions')]
    #[ORM\JoinColumn(name: 'student_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
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

    /**
     * jsonSerialize
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->id,
            "answer" => $this->answer,
            "obtainedGrade" => $this->obtainedGrade,
            "doneDate" => $this->doneDate->format('Y:m:d H:m'),
            "task" => [
                "id" => $this->task?->getId(),
                "title" => $this->task?->getTitle(),
                "dueDate" => $this->task?->getDueDate(),
            ],
            "student" => [
                "id" => $this->student?->getId(),
                "firstName" => $this->student?->getFirstName(),
                "lastName" => $this->student?->getLastName(),
                "email" => $this->student?->getEmail(),
            ],
        ];
    }
}
