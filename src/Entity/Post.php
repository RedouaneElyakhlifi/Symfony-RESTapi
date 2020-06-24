<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\PostRepository;
use DateTimeImmutable;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Carbon\Carbon;
use Symfony\Component\Serializer\Annotation\SerializedName;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;




/**
 * @ApiResource(
 *      normalizationContext={"groups"={"post:read"}},
 *      denormalizationContext={"groups"={"post:write"}},
 *      collectionOperations={"get", "post"},
 *      itemOperations={"put","get"},
 *      attributes={
 *          "pagination_items_per_page"=5
 *     }
 * )
 * 
 * @ApiFilter(BooleanFilter::class, properties={"is_published"})
 * @ApiFilter(SearchFilter::class, properties={"title" : "partial"})
 * 
 * @ORM\Entity(repositoryClass=PostRepository::class) 
 * @ORM\HasLifecycleCallbacks 
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Groups({"post:read", "post:write", "category:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=1000)
     * 
     * @Groups({"post:read", "category:read"})
     */
    private $body;

    /**
     * @ORM\Column(type="datetime")
     * 
     * @Groups({"post:read"})
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Groups({"post:read", "post:write"})
     */
    private $category;

    /**
     * @ORM\Column(type="boolean")
     * 
     * @Groups({"post:read", "post:write", "category:read"})
     */
    private $is_published = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * 
     * @Groups({"post:read", "category:read"})
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Groups({"post:read", "post:write", "category:read"})
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @SerializedName("body")
     * 
     * @Groups({"post:write"})
     */
    public function setTextBody(string $body): self
    {
        $this->body = nl2br($body);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        if ($this->created_at !== null) {

            return date_timezone_set($this->created_at, new DateTimeZone('Europe/Brussels'));
        }

        return null;
    }

    public function setCreatedAt(?DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @Groups({"post:read"})
     */
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setIsPublished(bool $is_published): self
    {
        $this->is_published = $is_published;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return date_timezone_set($this->updated_at, new DateTimeZone('Europe/Brussels'));
    }

    /**
     * @Groups({"post:read"})
     */
    public function getUpdatedAtAgo(): string
    {
        return Carbon::instance($this->getUpdatedAt())->diffForHumans();
    }

    public function setUpdatedAt(DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getAuthor(): ?user
    {
        return $this->author;
    }

    public function setAuthor(?user $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));    
        if ($this->getCreatedAt() === null) {
            $timestamp = new \DateTimeImmutable('now');
            $timestamp2 = DateTime::createFromImmutable($timestamp);
            $this->setCreatedAt($timestamp);
            $this->setUpdatedAt($timestamp2);

        }
    }
}
