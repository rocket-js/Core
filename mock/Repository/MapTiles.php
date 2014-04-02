<?php

namespace OpenTribes\Core\Mock\Repository;
use OpenTribes\Core\Repository\MapTiles as MapTilesRepository;
use OpenTribes\Core\Entity\Map as MapEntity;

/**
 * Description of MapTiles
 *
 * @author Witali
 */
class MapTiles implements MapTilesRepository {
    /**
     * @var MapEntity[]
     */
    private $map;

    public function add(MapEntity $map) {
        $this->map = $map;
    }

    public function getMap() {
        return $this->map;
    }

}