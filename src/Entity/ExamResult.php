<?php

namespace App\Entity;

use App\Repository\ExamResultRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExamResultRepository::class)]
class ExamResult implements JsonSerializable
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
    #[Assert\PositiveOrZero]
    private ?int $obtainedGrade = null;

    #[ORM\ManyToOne(targetEntity: Exam::class, inversedBy: 'examResults')]
    #[ORM\JoinColumn(name: 'exam_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    private ?Exam $exam = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'examResults')]
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
     * @param  string $answer
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
     * getExam
     *
     * @return Exam
     */
    public function getExam(): ?Exam
    {
        return $this->exam;
    }

    /**
     * setExam
     *
     * @return  self
     */
    public function setExam(Exam $exam): static
    {
        $this->exam = $exam;

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
     * setStudent
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
            "exam" => [
                "id" => $this->exam?->getId(),
                "title" => $this->exam?->getTitle(),
                "startDate" => $this->exam?->getStartDate(),
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
