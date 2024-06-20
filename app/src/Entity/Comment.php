<?php
/**
 * Comment entity.
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment entity.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'comments')]
class Comment
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
     * Nick.
     *
     * @var string|null Nick
     */
    #[ORM\Column(type: 'string', length: 155)]
    #[Assert\Type('string')]
    #[Assert\NotNull]
    #[Assert\Length(min: 3, max: 64)]
    private ?string $nick = null;

    /**
     * Email.
     *
     * @var string|null Email
     */
    #[ORM\Column(type: 'string', length: 191)]
    #[Assert\Type('string')]
    #[Assert\NotNull]
    #[Assert\Length(min: 3, max: 64)]
    private ?string $email = null;

    /**
     * Comment content.
     *
     * @var string|null Content
     */
    #[ORM\Column(type: 'text')]
    #[Assert\Type('string')]
    #[Assert\NotNull]
    #[Assert\Length(min: 3)]
    private ?string $commentContent = null;

    /**
     * Article.
     *
     * @var Article|null Article
     */
    #[ORM\ManyToOne(targetEntity: Article::class, fetch: 'EXTRA_LAZY', inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id')]
    private ?Article $article = null;

    /**
     * Getter for id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for nick.
     *
     * @return string|null Nick
     */
    public function getNick(): ?string
    {
        return $this->nick;
    }

    /**
     * Setter for nick.
     *
     * @param string $nick Nick
     */
    public function setNick(string $nick): void
    {
        $this->nick = $nick;
    }

    /**
     * Getter for email.
     *
     * @return string|null Email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Setter for email.
     *
     * @param string $email Email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Getter for comment content.
     *
     * @return string|null Content
     */
    public function getCommentContent(): ?string
    {
        return $this->commentContent;
    }

    /**
     * Setter for comment content.
     *
     * @param string $commentContent Content
     */
    public function setCommentContent(string $commentContent): void
    {
        $this->commentContent = $commentContent;
    }

    /**
     * Getter for article.
     *
     * @return Article|null Article
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * Setter for article.
     *
     * @param Article|null $article Article entity
     */
    public function setArticle(?Article $article): void
    {
        $this->article = $article;
    }
}
