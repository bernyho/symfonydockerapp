<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields="email", message="Email already taken.")
 * @method string getUserIdentifier()
 */
class User implements UserInterface, \JsonSerializable
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank()
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank()
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 * @Assert\NotBlank()
	 * @Assert\Email()
	 */
	private $email;

	/**
	 * @ORM\Column(type="json")
	 * @Assert\NotBlank()
	 */
	private $roles = [];

	public function __construct()
	{
		$this->roles = ['ROLE_USER'];
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

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

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}

	public function getSalt()
	{
		return null;
	}

	public function getUsername()
	{
		return $this->email;
	}

	public function eraseCredentials()
	{

	}

	public function jsonSerialize()
	{
		return [
			"name" => $this->getName(),
			"email" => $this->getEmail(),
			"roles" => implode(",", $this->getRoles()),
		];
	}

	public function getRoles(): array
	{
		$roles = $this->roles;
		return array_unique($roles);
	}

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
