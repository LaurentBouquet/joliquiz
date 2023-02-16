<?php

namespace App\Entity;

use InvalidArgumentException;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`tbl_user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Workout::class, orphanRemoval: true)]
    private Collection $workouts;

    #[ORM\OneToMany(mappedBy: 'created_by', targetEntity: Quiz::class, orphanRemoval: true)]
    private Collection $quizzes;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Language $prefered_language = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $passwordRequestedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $organization_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $organization_label = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $account_type = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $login_type = null;

    // #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'users')]
    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'users')]
    #[ORM\JoinTable(name: 'tbl_user_group')]
    private Collection $groups;

    #[ORM\Column(nullable: true)]
    private ?bool $toReceiveMyResultByEmail = null;

    #[ORM\OneToMany(mappedBy: 'created_by', targetEntity: Question::class)]
    private Collection $questions;

    #[ORM\OneToMany(mappedBy: 'created_by', targetEntity: Category::class)]
    private Collection $categories;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $current_school_year = null;

    #[ORM\Column(nullable: true)]
    private ?int $ed_id = null;

    private $plainPassword;

    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->isActive = true;        
        $this->workouts = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function __toString(): string
    {    
        if (empty($this->getFirstname()) && empty($this->getLastname())) {
            return $this->username;
        } else {
            return trim($this->getFirstname() . " " . $this->getLastname());
        }             
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->__toString();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        // for the user entity has always at least the role 'ROLE_USER'
        return array_unique(array_merge(['ROLE_USER'], $this->roles));
    }
    
    public function setRoles($roles): self
    {
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        foreach ($roles as $role) {
            if (substr($role, 0, 5) !== 'ROLE_') {
                throw new InvalidArgumentException('A role name should start with "ROLE_"');
            }
        }

        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        if (!$this->roles) {
            $this->roles[] = $role;
        }
        
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }
    
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password): self
    {
        $this->plainPassword = $password;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, Workout>
     */
    public function getWorkouts(): Collection
    {
        return $this->workouts;
    }

    public function addWorkout(Workout $workout): self
    {
        if (!$this->workouts->contains($workout)) {
            $this->workouts->add($workout);
            $workout->setStudent($this);
        }

        return $this;
    }

    public function removeWorkout(Workout $workout): self
    {
        if ($this->workouts->removeElement($workout)) {
            // set the owning side to null (unless already changed)
            if ($workout->getStudent() === $this) {
                $workout->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): self
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setCreatedBy($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->removeElement($quiz)) {
            // set the owning side to null (unless already changed)
            if ($quiz->getCreatedBy() === $this) {
                $quiz->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getPreferedLanguage(): ?Language
    {
        return $this->prefered_language;
    }

    public function setPreferedLanguage(?Language $prefered_language): self
    {
        $this->prefered_language = $prefered_language;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt): self
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getOrganizationCode(): ?string
    {
        return $this->organization_code;
    }

    public function setOrganizationCode(?string $organization_code): self
    {
        $this->organization_code = $organization_code;

        return $this;
    }

    public function getOrganizationLabel(): ?string
    {
        return $this->organization_label;
    }

    public function setOrganizationLabel(?string $organization_label): self
    {
        $this->organization_label = $organization_label;

        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->account_type;
    }

    public function setAccountType(?string $account_type): self
    {
        $this->account_type = $account_type;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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

    public function getLoginType(): ?string
    {
        return $this->login_type;
    }

    public function setLoginType(?string $login_type): self
    {
        $this->login_type = $login_type;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        $this->groups->removeElement($group);

        return $this;
    }

    public function isToReceiveMyResultByEmail(): ?bool
    {
        return $this->toReceiveMyResultByEmail;
    }

    public function setToReceiveMyResultByEmail(?bool $toReceiveMyResultByEmail): self
    {
        $this->toReceiveMyResultByEmail = $toReceiveMyResultByEmail;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setCreatedBy($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getCreatedBy() === $this) {
                $question->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getCreatedBy() === $this) {
                $category->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCurrentSchoolYear(): ?string
    {
        return $this->current_school_year;
    }

    public function setCurrentSchoolYear(?string $current_school_year): self
    {
        $this->current_school_year = $current_school_year;

        return $this;
    }

    public function getEdId(): ?int
    {
        return $this->ed_id;
    }

    public function setEdId(?int $ed_id): self
    {
        $this->ed_id = $ed_id;

        return $this;
    }
}
