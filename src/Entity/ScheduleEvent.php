<?php

namespace App\Entity;

use App\Repository\ScheduleEventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScheduleEventRepository::class)]
class ScheduleEvent implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    #[Assert\DateTime]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    #[Assert\DateTime]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?string $meetingLink = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'scheduleEvents')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    private ?Course $course = null;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'scheduleEvents')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', onDelete: 'cascade')]
    #[Assert\NotNull]
    private ?Group $group = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getMeetingLink(): ?string
    {
        return $this->meetingLink;
    }

    public function setMeetingLink(string $meetingLink): static
    {
        $this->meetingLink = $meetingLink;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): static
    {
        $this->group = $group;

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
            'id' => $this->id,
            'meetingLink' => $this->meetingLink,
            'startDate' => $this->startDate->format('Y-m-d H:m'),
            'endDate' => $this->startDate->format('Y-m-d H:m'),
            'course' => [
                'id' => $this->course?->getId(),
                'name' => $this->course?->getName(),
            ],
            'group' => [
                'id' => $this->group?->getId(),
                'name' => $this->group?->getName(),
            ],
        ];
    }
}
