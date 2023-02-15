<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AnswerHistoryRepository;

#[ORM\Entity(repositoryClass: AnswerHistoryRepository::class)]
#[ORM\Table(name: 'tbl_history_answer')]
class AnswerHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private $answer_id;

    #[ORM\ManyToOne(targetEntity: QuestionHistory::class, inversedBy:'answersHistory')]
    #[ORM\JoinColumn(nullable: false)]
    private $question_history;

    #[ORM\Column(type: 'text')]
    private $answer_text;

    #[ORM\Column(type: 'boolean')]
    private $answer_correct;

    #[ORM\Column(type: 'boolean')]
    private $correct_given;

    #[ORM\Column(type: 'boolean')]
    private $answer_succes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswerId(): ?int
    {
        return $this->answer_id;
    }

    public function setAnswerId(int $answer_id): self
    {
        $this->answer_id = $answer_id;

        return $this;
    }

    public function getQuestionHistory(): ?QuestionHistory
    {
        return $this->question_history;
    }

    public function setQuestionHistory(?QuestionHistory $question_history): self
    {
        $this->question_history = $question_history;

        return $this;
    }

    public function getAnswerText(): ?string
    {
        return $this->answer_text;
    }

    public function setAnswerText(string $answer_text): self
    {
        $this->answer_text = $answer_text;

        return $this;
    }

    public function getAnswerCorrect(): ?bool
    {
        return $this->answer_correct;
    }

    public function setAnswerCorrect(bool $answer_correct): self
    {
        $this->answer_correct = $answer_correct;

        return $this;
    }

    public function getCorrectGiven(): ?bool
    {
        return $this->correct_given;
    }

    public function setCorrectGiven(bool $correct_given): self
    {
        $this->correct_given = $correct_given;

        return $this;
    }

    public function getAnswerSucces(): ?bool
    {
        return $this->answer_succes;
    }

    public function setAnswerSucces(bool $answer_succes): self
    {
        $this->answer_succes = $answer_succes;

        return $this;
    }
}
