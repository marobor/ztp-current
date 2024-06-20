<?php
/**
 * Article entity.
 */

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Task.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'articles')]
class Article
{
    /**
     * Primary key.
     *
     * @var int|null Id
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Title.
     *
     * @var string|null Title
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    /**
     * Content.
     *
     * @var string|null Content
     */
    #[ORM\Column(type: 'text')]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    private ?string $content = null;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable|null Created at
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'create')]
    #[Assert\Type(\DateTimeImmutable::class)]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Categories.
     *
     * @var Category|null Category
     */
    #[ORM\JoinTable(name: 'articles_categories')]
    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY', inversedBy: 'articles')]
    #[Assert\Type(Category::class)]
    #[Assert\NotBlank]
    private ?Category $category = null;

    /**
     * Slug.
     *
     * @var string|null Slug
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Slug(fields: ['title'])]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $slug = null;

    /**
     * Comments.
     *
     * @var Collection<int, Comment> Comment collection
     */
    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id')]
    private Collection $comments;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for article title.
     *
     * @param string $title Title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Getter for content.
     *
     * @return string|null Content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for content.
     *
     * @param string|null $content Content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * Getter for createdAt.
     *
     * @return \DateTimeImmutable|null Created at
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter for createdAt.
     *
     * @param \DateTimeImmutable $createdAt Created at
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for category.
     *
     * @return Category|null Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     *
     * @param Category|null $category Category
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    /**
     * Getter for slug.
     *
     * @return string|null Slug
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Setter for slug.
     *
     * @param string $slug Slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * Getter for comment.
     *
     * @return Collection<int, Comment> Comments
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    //    /**
    //     * Add Comment.
    //     *
    //     * @param Comment $comment Comment
    //     */
    //    public function addComment(Comment $comment): void
    //    {
    //        if (!$this->comments->contains($comment)) {
    //            $this->comments[] = $comment;
    //            $comment->setArticle($this);
    //        }
    //    }
    //
    //    /**
    //     * Remove comment.
    //     *
    //     * @param Comment $comment Comment
    //     */
    //    public function removeComment(Comment $comment): void
    //    {
    //        // set the owning side to null (unless already changed)
    //        if ($this->comments->removeElement($comment) && $comment->getArticle() === $this) {
    //            $comment->setArticle(null);
    //        }
    //    }
}
