<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Api\Data;

/**
 * Interface PickupInterface
 */
interface PickupInterface
{
    /**
     * @return string
     */
    public function getNom();

    /**
     * @return string
     */
    public function getAdresse1();

    /**
     * @return string
     */
    public function getAdresse2();

    /**
     * @return string
     */
    public function getAdresse3();

    /**
     * @return string
     */
    public function getCodePostal();

    /**
     * @return string
     */
    public function getLocalite();

    /**
     * @return string
     */
    public function getCodePays();

    /**
     * @return string
     */
    public function getLangue();

    /**
     * @return string
     */
    public function getLibellePays();

    /**
     * @return string
     */
    public function getParking();

    /**
     * @return string
     */
    public function getIdentifiant();

    /**
     * @return string
     */
    public function getTypeDePoint();

    /**
     * @return string
     */
    public function getReseau();

    /**
     * @return string
     */
    public function getCoordGeolocalisationLatitude();

    /**
     * @return string
     */
    public function getCoordGeolocalisationLongitude();

    /**
     * @return int
     */
    public function getAccesPersonneMobiliteReduite();

    /**
     * @return string
     */
    public function getCongesPartiel();

    /**
     * @return string
     */
    public function getDistanceEnMetre();

    /**
     * @return string
     */
    public function getHorairesOuvertureLundi();

    /**
     * @return string
     */
    public function getHorairesOuvertureMardi();

    /**
     * @return string
     */
    public function getHorairesOuvertureMercredi();

    /**
     * @return string
     */
    public function getHorairesOuvertureJeudi();

    /**
     * @return string
     */
    public function getHorairesOuvertureVendredi();

    /**
     * @return string
     */
    public function getHorairesOuvertureSamedi();

    /**
     * @return string
     */
    public function getHorairesOuvertureDimanche();

    /**
     * @return string
     */
    public function getIndiceDeLocalisation();

    /**
     * @return string
     */
    public function getPeriodeActiviteHoraireDeb();

    /**
     * @return string
     */
    public function getPeriodeActiviteHoraireFin();

    /**
     * @return string
     */
    public function getPoidsMaxi();

    /**
     * @return string
     */
    public function getLoanOfHandlingTool();

    /**
     * @return string
     */
    public function getDistributionSort();

    /**
     * @return string
     */
    public function getLotAcheminement();

    /**
     * @return string
     */
    public function getVersionPlanTri();

    /**
     * @return string[]|null
     */
    public function getListeConges();
}