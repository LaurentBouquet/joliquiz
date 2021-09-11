<?php

namespace App\Entity;

use App\Entity\Group;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="tbl_user")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int The id of this user
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string The username of the user
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[a-z0-9]+$/i"
     * )
     */
    private $username;

    /**
     * @var string The email of the user
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string The password of the user
     * @ORM\Column(type="string", length=64)
     */
    // The length=64 works well with bcrypt algorithm.
    private $password;

    /**
    * @var string
    *
    * This field will not be persisted
    * It is used to store the password in the form
     * @Assert\NotBlank(message="Password cannot be empty", groups={"Update"})
     * @Assert\Regex(
     *      pattern="/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/",
     *      message="Password Error: Use 1 upper case letter, 1 lower case letter, and 1 number",
     *      groups={"Update"}
     * )
     * @Assert\Length(max=4096)
    */
    private $plainPassword;

    /**
     * @var boolean Is the user account active
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Workout", mappedBy="student", orphanRemoval=true)
     */
    private $workouts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="users")
     */
    private $prefered_language;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $organization_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $organization_label;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $account_type;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $login_type;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, inversedBy="users")
     * @ORM\JoinTable(name="tbl_user_group")à
     */
    private $groups;

    /**
     * @ORM\Column(type="boolean")
     */
    private $toReceiveMyResultByEmail;

    /**
     * @ORM\OneToMany(targetEntity=Quiz::class, mappedBy="created_by")
     */
    private $quizzes;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="created_by")
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity=Category::class, mappedBy="created_by")
     */
    private $categories;



    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->isActive = true;
        $this->workouts = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->setToReceiveMyResultByEmail(false);
    }
    
    public function __toString(): string
    {
        return $this->username;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getSalt()
    {
        // we don't need a salt because bcrypt do this internally (algorithm: bcrypt in security.yaml).
        return null;
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

    public function getRoles()
    {
        // for the user entity has always at least the role 'ROLE_USER'
        return array_unique(array_merge(['ROLE_USER'], $this->roles));
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

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
                $this->id,
                $this->username,
                $this->password,
                // we don't need a salt because bcrypt do this internally (algorithm: bcrypt in security.yaml).
                // $this->salt,
            ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
                $this->id,
                $this->username,
                $this->password,
                // we don't need a salt because bcrypt do this internally (algorithm: bcrypt in security.yaml).
                // $this->salt
            ) = unserialize($serialized, ['allowed_classes' => false]);
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
            $workout->setStudent($this);
        }

        return $this;
    }

    public function removeWorkout(Workout $workout): self
    {
        if ($this->workouts->contains($workout)) {
            $this->workouts->removeElement($workout);
            // set the owning side to null (unless already changed)
            if ($workout->getStudent() === $this) {
                $workout->setStudent(null);
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
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        $this->groups->removeElement($group);

        return $this;
    }

    public function getToReceiveMyResultByEmail(): ?bool
    {
        return $this->toReceiveMyResultByEmail;
    }

    public function setToReceiveMyResultByEmail(bool $toReceiveMyResultByEmail): self
    {
        $this->toReceiveMyResultByEmail = $toReceiveMyResultByEmail;

        return $this;
    }

    /**
     * @return Collection|Quiz[]
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): self
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes[] = $quiz;
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

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
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
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
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

}
