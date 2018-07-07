<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionHistoryRepository")
 * @ORM\Table(name="tbl_history_question")
 */
class QuestionHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $date_time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Workout", inversedBy="questionsHistory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $workout;

    /**
     * @ORM\Column(type="integer")
     */
    private $question_id;

    /**
     * @ORM\Column(type="text")
     */
    private $question_text;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $question_success;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $duration;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AnswerHistory", mappedBy="question_history", orphanRemoval=true)
     */
    private $answersHistory;


    public function __construct()
    {
        $this->answersHistory = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDateTime(): ?\DateTimeImmutable
    {
        return $this->date_time;
    }

    public function setDateTime(\DateTimeImmutable $date_time): self
    {
        $this->date_time = $date_time;

        return $this;
    }

    public function getWorkout(): ?Workout
    {
        return $this->workout;
    }

    public function setWorkout(?Workout $workout): self
    {
        $this->workout = $workout;

        return $this;
    }

    public function getQuestionId(): ?int
    {
        return $this->question_id;
    }

    public function setQuestionId(int $question_id): self
    {
        $this->question_id = $question_id;

        return $this;
    }

    public function getQuestionText(): ?string
    {
        return $this->question_text;
    }

    public function setQuestionText(string $question_text): self
    {
        $this->question_text = $question_text;

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

    public function getQuestionSuccess(): ?bool
    {
        return $this->question_success;
    }

    public function setQuestionSuccess(?bool $question_success): self
    {
        $this->question_success = $question_success;

        return $this;
    }

    public function getDuration(): ?\DateInterval
    {
        return $this->duration;
    }

    public function setDuration(?\DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection|AnswerHistory[]
     */
    public function getAnswersHistory(): Collection
    {
        return $this->answersHistory;
    }

    public function addAnswersHistory(AnswerHistory $answersHistory): self
    {
        if (!$this->answersHistory->contains($answersHistory)) {
            $this->answersHistory[] = $answersHistory;
            $answersHistory->setQuestionHistory($this);
        }

        return $this;
    }

    public function removeAnswersHistory(AnswerHistory $answersHistory): self
    {
        if ($this->answersHistory->contains($answersHistory)) {
            $this->answersHistory->removeElement($answersHistory);
            // set the owning side to null (unless already changed)
            if ($answersHistory->getQuestionHistory() === $this) {
                $answersHistory->setQuestionHistory(null);
            }
        }

        return $this;
    }
}
