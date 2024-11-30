<?php

namespace App\Entity;

use App\Repository\ExamResultRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExamResultRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:exam-result']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:exam-result']
        ),
        new Post(
            denormalizationContext: ['groups' => 'post:collection:exam-result'],
            normalizationContext: ['groups' => 'get:item:exam-result']
        ),
        new Patch(
            denormalizationContext: ['groups' => 'patch:item:exam-result'],
            normalizationContext: ['groups' => 'get:item:exam-result']
        ),
        new Delete(),
    ],
)]
class ExamResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:exam-result', 'get:collection:exam-result'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:exam-result',
        'get:collection:exam-result',
        'post:collection:exam-result',
        'patch:item:exam-result'
    ])]
    private ?string $answer = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    #[Groups([
        'get:item:exam-result',
        'get:collection:exam-result',
        'post:collection:exam-result',
        'patch:item:exam-result'
    ])]
    private ?int $obtainedGrade = null;

    #[ORM\ManyToOne(targetEntity: Exam::class, inversedBy: 'examResults')]
    #[ORM\JoinColumn(name: 'exam_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:exam-result',
        'get:collection:exam-result',
        'post:collection:exam-result',
        'patch:item:exam-result'
    ])]
    private ?Exam $exam = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'examResults')]
    #[ORM\JoinColumn(name: 'student_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:exam-result',
        'get:collection:exam-result',
        'post:collection:exam-result',
        'patch:item:exam-result'
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
}
