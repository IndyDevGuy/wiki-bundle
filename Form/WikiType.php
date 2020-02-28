<?php
namespace IndyDevGuy\WikiBundle\Form;

use IndyDevGuy\WikiBundle\Entity\Wiki;
use IndyDevGuy\WikiBundle\Repository\WikiRepository;
use App\Validator\Constraint\CodeConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

class WikiType extends AbstractType
{
    protected $wikiRepository;

    public function __construct(WikiRepository $wikiRepository)
    {
        $this->wikiRepository = $wikiRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $wikiRepository = $this->wikiRepository;
        $entity = $options['data'];

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'trim' => true,
                'attr'=>[
                    'class'=>'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    // TODO: resolve this
                    // new CodeConstraint(),
                    new Assert\Callback(
                        static function ($name, ExecutionContext $context) use ($wikiRepository, $entity) {
                            if ($findEntity = $wikiRepository->findOneByName($name)) {
                                if ($findEntity->getId() != $entity->getId()) {
                                    $context->addViolation('Name already exist');
                                }
                            }
                        }
                    ),
                ],
            ])
            ->add('description', TextType::class, [
                'required' => true,
                'trim' => true,
                'attr'=>[
                    'class'=>'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('read_role', TextType::class, [
                'required' => false,
                'trim' => true,
                'attr' => [
                    'class'=>'form-control',
                    'placeholder'=>'ex. ROLE_EXAMPLE, ROLE_SUPERUSER'
                ],
            ])
            ->add('write_role', TextType::class, [
                'required' => false,
                'trim' => true,
                'attr' => [
                    'class'=>'form-control',
                    'placeholder'=>'ex. ROLE_EXAMPLE, ROLE_SUPERUSER'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Wiki::class,
        ]);
    }
}
