<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkoutRepository")
 * @ORM\Table(name="tbl_workout")
 */
class Workout
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="workouts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quiz", inversedBy="workouts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quiz;

    /**
     * @ORM\Column(type="datetime")
     */
    private $started_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $ended_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $number_of_questions;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuestionHistory", mappedBy="workout", orphanRemoval=true)
     */
    private $questionsHistory;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $score;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;


    public function __construct()
    {
        $this->completed = false;
        $this->questionsHistory = new ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): self
    {
        $this->student = $student;

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

    public function setEndedAt(\DateTimeInterface $ended_at): self
    {
        $this->ended_at = $ended_at;

        return $this;
    }

    public function getNumberOfQuestions(): ?int
    {
        return $this->number_of_questions;
    }

    public function setNumberOfQuestions(int $number_of_questions): self
    {
        $this->number_of_questions = $number_of_questions;

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * @return Collection|QuestionHistory[]
     */
    public function getQuestionsHistory(): Collection
    {
        return $this->questionsHistory;
    }

    public function addQuestionsHistory(QuestionHistory $questionsHistory): self
    {
        if (!$this->questionsHistory->contains($questionsHistory)) {
            $this->questionsHistory[] = $questionsHistory;
            $questionsHistory->setWorkout($this);
        }

        return $this;
    }

    public function removeQuestionsHistory(QuestionHistory $questionsHistory): self
    {
        if ($this->questionsHistory->contains($questionsHistory)) {
            $this->questionsHistory->removeElement($questionsHistory);
            // set the owning side to null (unless already changed)
            if ($questionsHistory->getWorkout() === $this) {
                $questionsHistory->setWorkout(null);
            }
        }

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
