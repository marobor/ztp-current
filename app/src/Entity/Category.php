<?php
/**
 * Category entity.
 */

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Category.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'categories')]
#[ORM\UniqueConstraint(name: 'uq_categories_name', columns: ['name'])]
#[UniqueEntity(fields: ['name'])]
class Category
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
    #[Assert\Length(min: 3, max: 64)]
    private ?string $name = null;

    /**
     * Articles.
     *
     * @var ArrayCollection Articles
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Article::class)]
    private $articles;

    /**
     * Slug.
     *
     * @var string|null Slug
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Slug(fields: ['name'])]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 64)]
    private ?string $slug = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
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
     * Getter for name.
     *
     * @return string|null Title
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Setter for name.
     *
     * @param string|null $name Name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Getter for articles.
     *
     * @return Collection<int, Article> Articles
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    //    /**
    //     * Add article.
    //     *
    //     * @param Article $article Article entity
    //     *
    //     * @return $this
    //     */
    //    public function addArticle(Article $article): self
    //    {
    //        if (!$this->articles->contains($article)) {
    //            $this->articles[] = $article;
    //            $article->setCategory($this);
    //        }
    //
    //        return $this;
    //    }
    //
    //    /**
    //     * Remove article.
    //     *
    //     * @param Article $article Article entity
    //     */
    //    public function removeArticle(Article $article): void
    //    {
    //        if ($this->articles->removeElement($article)) {
    //            // set the owning side to null (unless already changed)
    //            if ($article->getCategory() === $this) {
    //                $article->setCategory(null);
    //            }
    //        }
    //    }

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
}
