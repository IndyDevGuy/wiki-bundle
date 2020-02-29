<?php
namespace IndyDevGuy\WikiBundle\Request\ParamConverter;

use Doctrine\ORM\EntityManagerInterface;
use IndyDevGuy\WikiBundle\Entity\Wiki;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WikiParamConverter implements ParamConverterInterface
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $wikiSlug = $request->attributes->get('wikiName');

        // Check, if route attributes exists
        if (null === $wikiSlug) {
            throw new \InvalidArgumentException('Route error: "wikiName" attribute is missing');
        }

        $wikiRepo = $this->em->getRepository($configuration->getClass());

        // Try to find village by its slug and slug of its district
        $wiki = $wikiRepo->findWikiByNameWithDashes($wikiSlug);

        if (null === $wiki || !($wiki instanceof Wiki)) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
        }

        // Map found village to the route's parameter
        $request->attributes->set('wiki', $wiki);
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration)
    {
        // If there is no manager, this means that only Doctrine DBAL is configured
        // In this case we can do nothing and just return
        if (null === $this->em) {
            return false;
        }

        // Check, if option class was set in configuration
        if (null === $configuration->getClass()) {
            return false;
        }

        // Check, if class name is what we need
        if ('IndyDevGuy\WikiBundle\Entity\Wiki' !== $this->em->getClassMetadata($configuration->getClass())->getName()) {
            return false;
        }

        return true;

    }
}