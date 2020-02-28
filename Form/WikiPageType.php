<?php
namespace IndyDevGuy\WikiBundle\Form;

use IndyDevGuy\WikiBundle\Entity\WikiPage;
use IndyDevGuy\WikiBundle\Repository\WikiPageRepository;
use App\Validator\Constraint\CodeConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

class WikiPageType extends AbstractType
{
    private $wikiPageRepository;

    public function __construct(WikiPageRepository $wikiPageRepository)
    {
        $this->wikiPageRepository = $wikiPageRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $wikiPageRepository = $this->wikiPageRepository;
        $entity = $options['data'];

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'trim' => true,
                'attr' => [
                    'class'=>'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    // TODO: Resolve this
                    // new CodeConstraint(),
                    new Assert\Callback(
                        static function ($name, ExecutionContext $context) use ($wikiPageRepository, $entity) {
                            if ($findEntity = $wikiPageRepository->findOneByWikiIdAndName($entity->getWiki()->getId(), $name)) {
                                if ($findEntity->getId() != $entity->getId()) {
                                    $context->addViolation('Name already exist');
                                }
                            }
                        }
                    ),
                ],
            ])
            ->add('highlighttheme', ChoiceType::class, [
                'choices'=>[
                    'Default'=>'default',
                    'A 11 Y Dark'=>'a11y-dark',
                    'A 11 Y Light'=>'a11y-light',
                    'Agate'=>'agate',
                    'An Old Hope'=>'an-old-hope',
                    'Android Studio'=>'androidstudio',
                    'Arduino Light'=>'arduino-light',
                    'Arta'=>'art',
                    'Ascetic'=>'ascetic',
                    'Atelier Cave Dark'=>'atelier-cave-dark',
                    'Atelier Cave Light'=>'atelier-cave-light',
                    'Atelier Dune Dark'=>'atelier-dune-dark',
                    'Atelier Dune Light'=>'atelier-dune-light',
                    'Atelier Estuary Dark'=>'atelier-estuary-dark',
                    'Atelier Estuary Light'=>'atelier-estuary-light',
                    'Atelier Forest Dark'=>'atelier-forest-dark',
                    'Atelier Forest Light'=>'atelier-forest-light',
                    'Atelier Health Dark'=>'atelier-health-dark',
                    'Atelier Health Light'=>'atelier-health-light',
                    'Atelier Lakeside Dark'=>'atelier-lakeside-dark',
                    'Atelier Lakeside Light'=>'atelier-lakeside-light',
                    'Atelier Plateau Dark'=>'atelier-plateau-dark',
                    'Atelier Plateau Light'=>'atelier-plateau-light',
                    'Atelier Savanna Dark'=>'atelier-savanna-dark',
                    'Atelier Savanna Light'=>'atelier-savanna-light',
                    'Atelier Seaside Dark'=>'atelier-seaside-dark',
                    'Atelier Seaside Light'=>'atelier-seaside-light',
                    'Atelier Sulphurpool Dark'=>'atelier-sulphurpool-dark',
                    'Atelier Sulphurpool Light'=>'atelier-sulphurpool-light',
                    'Atom One Dark Reasonable'=>'atom-one-dark-reasonable',
                    'Atom One Dark'=>'atom-one-dark',
                    'Atom One Light'=>'atom-one-light',
                    'Brown Paper'=>'brown-paper',
                    'Codepen Embed'=>'codepen-embed',
                    'Color Brewer'=>'color-brewer',
                    'Darcula'=>'darcula',
                    'Dark'=>'dark',
                    'Darkula'=>'darkula',
                    'Docco'=>'docco',
                    'Dracula'=>'dracula',
                    'Far'=>'far',
                    'Foundation'=>'foundation',
                    'Github Gist'=>'github-gist',
                    'Github'=>'github',
                    'Gml'=>'gml',
                    'Googlecode'=>'googlecode',
                    'Gradient Dark'=>'dradient-dark',
                    'Grayscale'=>'grayscale',
                    'Gruvbox Dark'=>'gruvbox-dark',
                    'Gruvbox Light'=>'gruvbox-light',
                    'Hopscotch'=>'hopscotch',
                    'Hybrid'=>'hybrid',
                    'Idea'=>'idea',
                    'Ir Black'=>'ir-black',
                    'Isbl Editor Dark'=>'isbl-editor-dark',
                    'Isbl Editor Light'=>'isbl-editor-light',
                    'Kimbie Dark'=>'kimbie.dark',
                    'Kimbie Light'=>'kimbie.light',
                    'Lightfair'=>'lightfair',
                    'Magula'=>'magula',
                    'Mono Blue'=>'mono-blue',
                    'Monokai Sublime'=>'monokai-sublime',
                    'Monokai'=>'monokai',
                    'Night Owl'=>'night-owl',
                    'Nord'=>'nord',
                    'Obsidian'=>'obsidian',
                    'Ocean'=>'ocean',
                    'Paraiso Dark'=>'paraiso-dark',
                    'Paraiso Light'=>'paraiso-light',
                    'Pojoaque'=>'pojoague',
                    'Purebasic'=>'purebasic',
                    'Qtcreator Dark'=>'qtcreator_dark',
                    'Qtcreator Light'=>'qtcreator_light',
                    'Railscasts'=>'railscasts',
                    'Rainbow'=>'rainbow',
                    'Routeros'=>'routeros',
                    'School Book'=>'school-book',
                    'Shades Of Purple'=>'shades-of-purple',
                    'Solarized Dark'=>'solarized-dark',
                    'Solarized Light'=>'solarized-light',
                    'Sunburst'=>'sunburst',
                    'Tomorrow Night Blue'=>'tomorrow-night-blue',
                    'Tomorrow Night Bright'=>'tomorrow-night-bright',
                    'Tomorrow Night Eighties'=>'tomorrow-night-eighties',
                    'Tomorrow Night'=>'tomorrow-night',
                    'Tomorrow'=>'tomorrow',
                    'Vs'=>'vs',
                    'Vs 2015'=>'vs2015',
                    'Xcode'=>'xcode',
                    'Xt 256'=>'xt256',
                    'Zenburn'=>'zenburn'
                ],
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('content', EasyMDEType::class, [
                'required' => false,
                'trim' => true,
                'elementid' => 'EasyMDE'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WikiPage::class,
        ]);
    }
}
