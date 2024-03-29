## Symfony 5 WikiBundle

Installation
============

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require indydevguy/wiki-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project (if it doesn't exists yet):

```php
<?php
// config/bundles.php

return [
    // ...
    IndyDevGuy\Bundle\WikiBundle\WikiBundle::class => ['all' => true],
    // ...
];
```

Create routing file to enable routes from wiki bundle like this:
`config/routes/indydevguy-wiki-bundle.yaml`
And add loading of routes like the following:
```yaml
wiki:
    resource: '@WikiBundle/Resources/config/routing.yaml'
    type: yaml
```
### Step 3: Enable Twig Extensions
Add the following snippet to your 'app/config/services.yml' file:
```yaml
services:
    # ...
    
    markdown.engine:
        class: Aptoma\Twig\Extension\MarkdownEngine\MichelfMarkdownEngine
    twig.markdown:
        class: Aptoma\Twig\Extension\MarkdownExtension
        arguments: ['@markdown.engine']
        tags:
            - { name: twig.extension }
```

### Step 4: TBC
