<?php

namespace HeimrichHannot\GoogleMapsBundle\Twig;

use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use Twig\Extension\RuntimeExtensionInterface;

class GoogleMapsRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly MapManager $mapManager,
    ) {}

    public function render(int $mapId): string
    {
        return $this->mapManager->render($mapId);
    }

    public function renderHtml(int $mapId): string
    {
        return $this->mapManager->renderHtml($mapId);
    }

    public function renderCss(int $mapId): string
    {
        return $this->mapManager->renderCss($mapId);
    }

    public function renderJs(int $mapId): string
    {
        return $this->mapManager->renderJs($mapId);
    }

}