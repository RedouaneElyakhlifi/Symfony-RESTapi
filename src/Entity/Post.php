<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Carbon\Carbon;
use Symfony\Component\Serializer\Annotation\SerializedName;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;




/**
 * @ApiResource(
 *      collectionOperations={"get", "post"},
 *      itemOperations={"put", "get"},
 *      normalizationContext={"groups"={"post:read"}},
 *      denormalizationContext={"groups"={"post:write"}},
 *      attributes={
 *          "pagination_items_per_page"=5
 *     }
 * )
 * 
 * @ApiFilter(BooleanFilter::class, properties={"is_published"})
 * @ApiFilter(SearchFilter::class, properties={"title" : "partial"})
 * 
 * @ORM\Entity(repositoryClass=PostRepository::class)
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
     * @Groups({"post:read", "post:write"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=1000)
     * 
     * @Groups({"post:read"})
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
     * @Groups({"post:read", "post:write"})
     */
    private $is_published = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * 
     * @Groups({"post:read"})
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Groups({"post:read", "post:write"})
     */
    private $author;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
    }

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
        return $this->created_at;
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
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
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
}
