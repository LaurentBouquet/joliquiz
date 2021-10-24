<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 * @ORM\Table(name="tbl_session")
 */
class Session
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $started_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $ended_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quiz", inversedBy="sessions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quiz;

    /**
     * @ORM\OneToMany(targetEntity=Workout::class, mappedBy="session", cascade={"remove"})
     */
    private $workouts;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sended_to_ed;

    public function __construct(Quiz $quiz, DateTime $started_at) {
        $this->setQuiz($quiz);
        $this->setStartedAt($started_at);
        $this->workouts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->started_at;
    }

    public function setStartedAt(\DateTimeInterface $started_at): self
    {
        $this->started_at = $started_at;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->ended_at;
    }

    public function setEndedAt(?\DateTimeInterface $ended_at): self
    {
        $this->ended_at = $ended_at;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * @return Collection|Workout[]
     */
    public function getWorkouts(): Collection
    {
        return $this->workouts;
    }

    public function addWorkout(Workout $workout): self
    {
        if (!$this->workouts->contains($workout)) {
            $this->workouts[] = $workout;
            $workout->setSession($this);
        }

        return $this;
    }

    public function removeWorkout(Workout $workout): self
    {
        if ($this->workouts->removeElement($workout)) {
            // set the owning side to null (unless already changed)
            if ($workout->getSession() === $this) {
                $workout->setSession(null);
            }
        }

        return $this;
    }

    public function getSendedToED(): ?bool
    {
        return $this->sended_to_ed;
    }

    public function setSendedToED(?bool $sended_to_ed): self
    {
        $this->sended_to_ed = $sended_to_ed;

        return $this;
    }

}
