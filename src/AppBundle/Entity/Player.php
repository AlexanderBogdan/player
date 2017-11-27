<?php

declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Gedmo\Timestampable\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="players", indexes={@Index(name="external_id_idx", columns={"external_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Player implements Timestampable
{
    use TimestampableEntity;

    /**
     * @Assert\Uuid()
     *
     * @ORM\Column(type="string", length=36, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("id")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="36")
     *
     * @ORM\Column(type="string", length=36, unique=true)
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("externalId")
     */
    private $externalId;

    /**
     * @Assert\NotBlank()
     * @Assert\Country()
     * @Assert\Length(min="2", max="2")
     *
     * @ORM\Column(type="string", length=2)
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("country")
     */
    private $country;

    /**
     * @Assert\Choice(
     *     choices = { "M", "F" },
     *     message = "Invalid sex parameter."
     * )
     * @ORM\Column(type="string", length=1, nullable=true)
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("sex")
     */
    private $sex;

    /**
     * @Assert\NotBlank()
     * @Assert\Currency()
     * @Assert\Length(min="3", max="3")
     *
     * @ORM\Column(type="string", length=3)
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("currency")
     */
    private $currency;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="2")
     *
     * @ORM\Column(type="string", length=2)
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("jurisdiction")
     */
    private $jurisdiction;

    /**
     * @Assert\NotNull()
     *
     * @ORM\Column(type="boolean")
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("isActive")
     */
    private $active = true;

    public function getId(): string
    {
        return $this->id;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): void
    {
        $this->sex = $sex;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getJurisdiction(): string
    {
        return $this->jurisdiction;
    }

    public function setJurisdiction(string $jurisdiction): void
    {
        $this->jurisdiction = $jurisdiction;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
