<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandRepository::class)]
class Command
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commands')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $id_customer = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_created = null;

    #[ORM\OneToMany(mappedBy: 'command', targetEntity: CustomerCommand::class, orphanRemoval: true)]
    private Collection $customerCommands;

    #[ORM\ManyToOne(inversedBy: 'commands')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shop $Shop = null;

    #[ORM\Column(length: 255)]
    private ?string $recup_date = null;

    public function __construct()
    {
        $this->customerCommands = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCustomer(): ?User
    {
        return $this->id_customer;
    }

    public function setIdCustomer(?User $id_customer): self
    {
        $this->id_customer = $id_customer;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->date_created;
    }

    public function setDateCreated(\DateTimeInterface $date_created): self
    {
        $this->date_created = $date_created;

        return $this;
    }

    /**
     * @return Collection<int, CustomerCommand>
     */
    public function getCustomerCommands(): Collection
    {
        return $this->customerCommands;
    }

    public function addCustomerCommand(CustomerCommand $customerCommand): self
    {
        if (!$this->customerCommands->contains($customerCommand)) {
            $this->customerCommands->add($customerCommand);
            $customerCommand->setCommand($this);
        }

        return $this;
    }

    public function removeCustomerCommand(CustomerCommand $customerCommand): self
    {
        if ($this->customerCommands->removeElement($customerCommand)) {
            // set the owning side to null (unless already changed)
            if ($customerCommand->getCommand() === $this) {
                $customerCommand->setCommand(null);
            }
        }

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->Shop;
    }

    public function setShop(?Shop $Shop): self
    {
        $this->Shop = $Shop;

        return $this;
    }

    public function getRecupDate(): ?string
    {
        return $this->recup_date;
    }

    public function setRecupDate(string $recup_date): self
    {
        $this->recup_date = $recup_date;

        return $this;
    }
}
