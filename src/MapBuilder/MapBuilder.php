<?php

namespace HeimrichHannot\GoogleMapsBundle\MapBuilder;

use Contao\Model\Collection;
use HeimrichHannot\GoogleMapsBundle\Event\GoogleMapsPrepareExternalItemEvent;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\GoogleMapsBundle\Manager\OverlayManager;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use HeimrichHannot\GoogleMapsBundle\MapBuilder\MarkerHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MapBuilder implements \Stringable
{
    private array|Collection $overlays;
    private int $mapId;

    private bool $prepared = false;
    private array $mapTemplateData;

    public function __construct(
        private readonly MapManager               $mapManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly OverlayManager $overlayManager,
        private readonly RequestStack $requestStack,
    ) {}

    public function setMapId(int $id): self
    {
        $this->mapId = $id;
        return $this;
    }

    public function setMapIfIfExist(mixed $mapId): self
    {
        if (empty($mapId)) {
            return $this;
        }

        if (is_scalar($mapId)) {
            return $this->setMapId((int)$mapId);
        }

        if (is_array($mapId)) {
            foreach ($mapId as $id) {
                if (is_scalar($id)) {
                    $this->setMapIfIfExist((int)$id);
                    if (isset($this->mapId)) {
                        return $this;
                    }
                }
            }
        }

        return $this;
    }

    public function addOverlays(array|Collection $overlays): self
    {
        if ($this->prepared) {
            throw new \RuntimeException('Map already build.');
        }
        $this->overlays = $overlays;
        return $this;
    }

    public function buildIfExist(): ?self
    {
        try {
            $this->build();
        } catch (\RuntimeException) {
            return null;
        }

        return $this;
    }

    public function build(array $config = []): self
    {
        if (!isset($this->mapId)) {
            throw new \RuntimeException('Map ID not set.');
        }
        if ($this->prepared) {
            return $this;
        }
        $overlays = $this->buildOverlays($this->mapId, $this->overlays);

        $templateData = $this->mapManager->prepareMap($this->mapId, $config, $overlays);

        if (null === $templateData) {
            throw new \RuntimeException('Map data not found in template.');
        }

        $this->mapTemplateData = $templateData;

        $this->prepared = true;
        return $this;
    }

    public function getMarker(int $id): ?MarkerHelper
    {
        if (!$this->prepared) {
            throw new \RuntimeException('Map must be build before accessing marker.');
        }

        $markerVariableMapping = $this->overlayManager->getMarkerVariableMapping();
        if (!isset($markerVariableMapping[$id])) {
            return null;
        }

        return new MarkerHelper(
            $markerVariableMapping[$id],
            $this->requestStack->getCurrentRequest() ?: new Request()
        );
    }


    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(array $overrideConfig = []): string
    {
        if (!$this->prepared) {
            $this->build($overrideConfig);
        }

        $data = array_merge($this->mapTemplateData, $overrideConfig);

        return $this->mapManager->renderMapObject(
            $data['mapModel'],
            $this->mapId,
            $data['mapConfigModel'],
            $data
        );
    }

    public function cloneWith()
    {

    }

    public function html(): string
    {


        return $this->toString([
            'skipCss' => true,
            'skipJs' => true,
        ]);
    }

    public function js(): string
    {
        return $this->toString([
            'skipHtml' => true,
            'skipCss' => true,
        ]);
    }

    public function css(): string
    {
        return $this->toString([
            'skipHtml' => true,
            'skipJs' => true,
        ]);
    }

    private function buildOverlays(int $mapId, array $overlayDefinitions): ?Collection
    {
        $overlays = [];

        foreach ($overlayDefinitions as $overlayData) {

            if ($overlayData instanceof OverlayModel) {
                $model = $overlayData;
                $overlayData = $model->row();
            } else {
                $model = new OverlayModel();
                $model->setRow($overlayData);
            }

            $event = new GoogleMapsPrepareExternalItemEvent(
                $overlayData,
                $model,
                [
                    'mapId' => $mapId,
                ]
            );
            $this->eventDispatcher->dispatch($event);
            $overlays[] = $event->overlayModel;
        }

        return new Collection($overlays, OverlayModel::getTable());
    }
}