<?php
namespace IndyDevGuy\WikiBundle;

use IndyDevGuy\WikiBundle\DependencyInjection\WikiExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class WikiBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new WikiExtension();
        }
        return $this->extension;
    }
}
