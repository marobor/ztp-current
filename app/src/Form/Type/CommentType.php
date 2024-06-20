<?php
/**
 * Comment type.
 */

namespace App\Form\Type;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryType.
 */
class CommentType extends AbstractType
{
    /**
     * Builds form.
     *
     * @param FormBuilderInterface $builder Builder
     * @param array                $options Options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'nick',
            TextType::class,
            [
                'label' => 'label.nick',
                'required' => true,
                'attr' => ['max_length' => 64],
            ]
        );
        $builder->add(
            'email',
            TextType::class,
            [
                'label' => 'label.email',
                'required' => true,
                'attr' => ['max_length' => 64],
            ]
        );
        $builder->add(
            'comment_content',
            TextType::class,
            [
                'label' => 'label.comment_content',
                'required' => true,
            ]
        );
    }

    /**
     * Configures options for this type.
     *
     * @param OptionsResolver $resolver Resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Comment::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string Prefix
     */
    public function getBlockPrefix(): string
    {
        return 'comment';
    }
}
