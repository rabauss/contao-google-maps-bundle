<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Twig;

use Contao\CoreBundle\String\HtmlAttributes;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\GoogleMapsBundle\MapBuilder\MapBuilder;
use HeimrichHannot\GoogleMapsBundle\MapBuilder\MapBuilderFactory;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Runtime\EscaperRuntime;
use Twig\TwigFunction;

class GoogleMapsExtension extends AbstractExtension
{
    public function __construct(
        private readonly Environment $environment,
    ) {
        $escaperRuntime = $this->environment->getRuntime(EscaperRuntime::class);
        $escaperRuntime->addSafeClass(MapBuilder::class, ['html', 'contao_html']);
    }

    public function getFunctions(): array
    {
        $functions = [];



        foreach ($this->getMapping() as $name => $method) {
            $functions[] = new TwigFunction($name, [GoogleMapsRuntime::class, $method], ['is_safe' => ['html']]);
        }

        return $functions;
    }

    private function getMapping(): array
    {
        return [
            'google_map_create' => 'create',


            'google_map' => 'render',
            'google_map_html' => 'renderHtml',
            'google_map_css' => 'renderCss',
            'google_map_js' => 'renderJs',
            'google_map_marker_link' => 'getMarkerLink',
        ];
    }
}
