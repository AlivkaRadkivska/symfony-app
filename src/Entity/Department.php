<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use App\Entity\Teacher;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $faculty = null;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: Teacher::class)]
    private ?Collection $teachers;

    public function __construct()
    {
        $this->teachers = new ArrayCollection();
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param string $name
     * @return  self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of faculty
     */
    public function getFaculty()
    {
        return $this->faculty;
    }

    /**
     * Set the value of faculty
     *
     * @param string $faculty
     * @return  self
     */
    public function setFaculty(string $faculty): self
    {
        $this->faculty = $faculty;

        return $this;
    }

    /**
     * Get the value of teachers
     * 
     * @return mixed
     */
    public function getTeachers(): mixed
    {
        return array_map(function ($teacher) {
            return [
                'email' => $teacher?->getEmail(),
                'firstName' => $teacher?->getFirstName(),
                'secondName' => $teacher?->getLastName(),
                'position' => $teacher?->getPosition(),
            ];
        }, iterator_to_array($this->teachers));
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
            'name' => $this->name,
            'faculty' => $this->faculty,
            'teachers' => $this->getTeachers(),
        ];
    }
}
