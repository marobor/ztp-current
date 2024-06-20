<?php
/**
 * Category fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        $this->createMany(15, 'comments', function (int $i) {
            $comment = new Comment();
            $comment->setNick($this->faker->firstName);
            $comment->setEmail($this->faker->email);
            $comment->setCommentContent($this->faker->paragraph);
            /** @var Article $article */
            $article = $this->getRandomReference('articles');
            $comment->setArticle($article);

            return $comment;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: ArticleFixtures::class}
     */
    public function getDependencies(): array
    {
        return [ArticleFixtures::class];
    }
}
