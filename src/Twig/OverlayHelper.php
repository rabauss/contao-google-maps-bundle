<?php

namespace HeimrichHannot\GoogleMapsBundle\Twig;

use League\Uri\Uri;
use Symfony\Component\HttpFoundation\Request;

class OverlayHelper
{
    public function __construct(
        public readonly string $variable,
        public readonly Request $request
    ) {}

    public function getHref(): Uri
    {
        return Uri::new($this->request->getUri())->withFragment($this->variable);
    }
}