<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CourseProgressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CourseProgressRepository::class)]
#[ApiResource(subresourceOperations: ['api_students_courseProgress_get_subresource'=>
    ['normalization_context'=>['groups'=>['cp_subresource']]]],
    normalizationContext: ['groups'=> 'courseProgress_read'])]
class CourseProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['student_read','courseProgress_read','cp_subresource'])]
    private $id;

    #[ORM\Column(type: 'float')]
    #[Groups(['student_read','courseProgress_read'])]
    private $progress;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'courseProgress')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['courseProgress_read'])]
    private $student;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'studentsCourseProgress')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['student_read','courseProgress_read'])]
    private $course;

    #[ORM\ManyToMany(targetEntity: Lesson::class, inversedBy: 'courseProgress')]
    #[Groups(['student_read','courseProgress_read'])]
    private $lessons;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProgress(): ?float
    {
        return $this->progress;
    }

    public function setProgress(float $progress): self
    {
        $this->progress = $progress;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        $this->lessons->removeElement($lesson);

        return $this;
    }
}
