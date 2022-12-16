<?php
namespace IndyDevGuy\WikiBundle\Request\ParamConverter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use IndyDevGuy\WikiBundle\Entity\WikiPage;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WikiPageParamConverter implements ParamConverterInterface
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $wikiPageSlug = $request->attributes->get('wikiPage');

        // Check, if route attributes exists
        if (null === $wikiPageSlug) {
            throw new InvalidArgumentException('Route error: "wikiPage" attribute is missing');
        }

        $wikiPageRepo = $this->em->getRepository($configuration->getClass());

        // Try to find village by its slug and slug of its district
        $wikiPage = $wikiPageRepo->findWikiPageByNameWithDashes($wikiPageSlug);

        if (!($wikiPage instanceof WikiPage)) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
        }

        // Map found village to the route's parameter
        $request->attributes->set($configuration->getName(), $wikiPage);
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        // Check, if option class was set in configuration
        if (null === $configuration->getClass()) {
            return false;
        }

        // Check, if class name is what we need
        if ('IndyDevGuy\WikiBundle\Entity\WikiPage' !== $this->em->getClassMetadata($configuration->getClass())->getName()) {
            return false;
        }

        return true;

    }
}