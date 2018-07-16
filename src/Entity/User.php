<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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
     * @ORM\GeneratedValue()
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
     * @ORM\Column(type="string", length=255, unique=true)
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



    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->isActive = true;
        $this->workouts = new ArrayCollection();
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

}
