<?php

namespace App\Entity;

use App\Repository\ScheduleEventRepository;
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

#[ORM\Entity(repositoryClass: ScheduleEventRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'get:item:schedule-event']
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'get:collection:schedule-event']
        ),
        new Post(
            denormalizationContext: ['groups' => 'post:collection:schedule-event'],
            normalizationContext: ['groups' => 'get:item:schedule-event']
        ),
        new Patch(
            denormalizationContext: ['groups' => 'patch:item:schedule-event'],
            normalizationContext: ['groups' => 'get:item:schedule-event']
        ),
        new Delete(),
    ],
)]
class ScheduleEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:item:schedule-event', 'get:collection:schedule-event'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Assert\NotNull]
    #[Groups([
        'get:item:schedule-event',
        'get:collection:schedule-event',
        'post:collection:schedule-event',
        'patch:item:schedule-event'
    ])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Context(normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Assert\NotNull]
    #[Groups([
        'get:item:schedule-event',
        'get:collection:schedule-event',
        'post:collection:schedule-event',
        'patch:item:schedule-event'
    ])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups([
        'get:item:schedule-event',
        'get:collection:schedule-event',
        'post:collection:schedule-event',
        'patch:item:schedule-event'
    ])]
    private ?string $meetingLink = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'scheduleEvents')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:schedule-event',
        'get:collection:schedule-event',
        'post:collection:schedule-event',
        'patch:item:schedule-event'
    ])]
    private ?Course $course = null;

    #[ORM\ManyToOne(targetEntity: StudentGroup::class, inversedBy: 'scheduleEvents')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    #[Groups([
        'get:item:schedule-event',
        'get:collection:schedule-event',
        'post:collection:schedule-event',
        'patch:item:schedule-event'
    ])]
    private ?StudentGroup $studentGroup = null;

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
     * getEndDate
     *
     * @return DateTimeInterface
     */
    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * setEndDate
     *
     * @param  \DateTimeInterface $endDate
     * @return static
     */
    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * getMeetingLink
     *
     * @return string
     */
    public function getMeetingLink(): ?string
    {
        return $this->meetingLink;
    }

    /**
     * setMeetingLink
     *
     * @param  mixed $meetingLink
     * @return static
     */
    public function setMeetingLink(string $meetingLink): static
    {
        $this->meetingLink = $meetingLink;

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
     * @param  mixed $course
     * @return static
     */
    public function setCourse(Course $course): static
    {
        $this->course = $course;

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
     * @param  mixed $studentGroup
     * @return static
     */
    public function setStudentGroup(StudentGroup $studentGroup): static
    {
        $this->studentGroup = $studentGroup;

        return $this;
    }
}
