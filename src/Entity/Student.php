<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Un compte avec cette adresse email existe déjà.')]
#[UniqueEntity(fields: ['pseudo'], message: 'Un compte avec ce pseudo existe déjà.')]
#[ApiResource(collectionOperations: ['GET'=>['path'=>'/clicli']],
    subresourceOperations: ['courseProgress_get_subresource'=>['path'=>'/students/{id}/courseProgress']],
    attributes: ['pagination_enabled'=> true, 'order'=> ['id'=>'desc']],
    normalizationContext: ['groups'=> 'student_read'])]
#[ApiFilter(SearchFilter::class, properties: ['pseudo'=>'partial'])]
#[ApiFilter(OrderFilter::class)]

class Student extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['student_read'])]
    protected $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['student_read'])]
    private $pseudo;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['student_read'])]
    private $profilePicture;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: CourseProgress::class, orphanRemoval: true)]
    #[Groups(['student_read'])]
    #[ApiSubresource]
    private $courseProgress;

    #[Groups(['student_read'])]
    public function gettest():string
    {
        return $this->id + $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function __construct(array $roles = ['ROLE_STUDENT'])
    {
        parent::__construct($roles);
        $this->courseProgress = new ArrayCollection();
    }

    /**
     * @return Collection<int, CourseProgress>
     */
    public function getCourseProgress(): Collection
    {
        return $this->courseProgress;
    }

    public function addCourseProgress(CourseProgress $courseProgress): self
    {
        if (!$this->courseProgress->contains($courseProgress)) {
            $this->courseProgress[] = $courseProgress;
            $courseProgress->setStudent($this);
        }

        return $this;
    }

    public function removeCourseProgress(CourseProgress $courseProgress): self
    {
        if ($this->courseProgress->removeElement($courseProgress)) {
            // set the owning side to null (unless already changed)
            if ($courseProgress->getStudent() === $this) {
                $courseProgress->setStudent(null);
            }
        }

        return $this;
    }
}
