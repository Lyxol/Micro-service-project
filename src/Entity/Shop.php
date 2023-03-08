<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShopRepository::class)]
#[ApiResource]
class Shop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'id_shop', targetEntity: ProductShop::class, orphanRemoval: true)]
    private Collection $products;

    #[ORM\Column(length: 255)]
    private ?string $State = null;

    #[ORM\Column(length: 255)]
    private ?string $open_time = null;

    #[ORM\Column(length: 255)]
    private ?string $closing_time = null;

    #[ORM\OneToMany(mappedBy: 'Shop', targetEntity: Command::class)]
    private Collection $commands;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->commands = new ArrayCollection();
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

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

    /**
     * @return Collection<int, ProductShop>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(ProductShop $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setIdShop($this);
        }

        return $this;
    }

    public function removeProduct(ProductShop $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getIdShop() === $this) {
                $product->setIdShop(null);
            }
        }

        return $this;
    }

    public function getState(): ?string
    {
        return $this->State;
    }

    public function setState(string $State): self
    {
        $this->State = $State;

        return $this;
    }

    public function getOpenTime(): ?string
    {
        return $this->open_time;
    }

    public function setOpenTime(string $open_time): self
    {
        $this->open_time = $open_time;

        return $this;
    }

    public function getClosingTime(): ?string
    {
        return $this->closing_time;
    }

    public function setClosingTime(string $closing_time): self
    {
        $this->closing_time = $closing_time;

        return $this;
    }

    /**
     * @return Collection<int, Command>
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): self
    {
        if (!$this->commands->contains($command)) {
            $this->commands->add($command);
            $command->setShop($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getShop() === $this) {
                $command->setShop(null);
            }
        }

        return $this;
    }
}
