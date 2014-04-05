<?php

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Mink;
use OpenTribes\Core\Repository\City as CityRepository;
use OpenTribes\Core\Repository\MapTiles as MapTilesRepository;
use OpenTribes\Core\Repository\User as UserRepository;
use OpenTribes\Core\Repository\Building as BuildingRepository;
use OpenTribes\Core\Repository\CityBuildings as CityBuildingsRepository;
use OpenTribes\Core\Service\LocationCalculator;
/**
 * Description of CityHelper
 *
 * @author BlackScorp<witalimik@web.de>
 */
class SilexCityHelper extends CityHelper {

    private $mink;
    private $sessionName;

    /**
     * @var DocumentElement
     */
    private $page;
    private $x;
    private $y;

    public function __construct(Mink $mink, CityRepository $cityRepository, MapTilesRepository $mapTilesRepository, UserRepository $userRepository, LocationCalculator $locationCalculator, CityBuildingsRepository $cityBuildingsRepository, BuildingRepository $buildingRepository) {
        parent::__construct($cityRepository, $mapTilesRepository, $userRepository, $locationCalculator, $cityBuildingsRepository, $buildingRepository);
        $this->mink        = $mink;
        $this->sessionName = $this->mink->getDefaultSessionName();
    }

    private function loadPage() {
        $this->page = $this->mink->getSession($this->sessionName)->getPage();
    }

    public function selectLocation($direction, $username) {
        $this->loadPage();

        $this->mink->getSession()->setCookie('username', $username);
        $this->page->selectFieldOption('direction', $direction);
        $this->page->pressButton('select');
    }

    public function assertCityIsInArea($minX, $maxX, $minY, $maxY) {
        $this->loadPage();
        $spanX = $this->page->find('css', 'span.x');
        $spanY = $this->page->find('css', 'span.y');

        $this->mink->assertSession()->statusCodeEquals(200);
        assertNotNull($spanY, 'span class="y" not found');
        assertNotNull($spanX, 'span class="x" not found');
        $this->x = (int) $spanX->getText();
        $this->y = (int) $spanY->getText();

        assertGreaterThanOrEqual((int) $minX, $this->x);
        assertLessThanOrEqual((int) $maxX, $this->x);
        assertGreaterThanOrEqual((int) $minY, $this->y);
        assertLessThanOrEqual((int) $maxY, $this->y);
    }

    public function assertCityIsNotAtLocations(array $locations) {

        foreach ($locations as $location) {
            $x           = $location[1];
            $y           = $location[0];
            $expectedKey = sprintf('Y%d/X%d', $y, $x);
            $currentKey  = sprintf('Y%d/X%d', $this->y, $this->x);
            assertNotSame($currentKey, $expectedKey, sprintf("%s is not %s", $expectedKey, $currentKey));
        }
    }

    /**
     * @param integer $y
     * @param integer $x
     */
    public function assertCityExists($name, $owner, $y, $x) {
        $this->loadPage();
        $this->page->hasContent($name);
        $this->page->hasContent($owner);
        $this->page->hasContent($y);
        $this->page->hasContent($x);
    }
    public function assertCityHasBuilding($name, $level) {
        throw new \Behat\Behat\Exception\PendingException;
    }
}
