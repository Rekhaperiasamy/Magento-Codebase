<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Model;

use Colissimo\Shipping\Api\Data\PickupInterface;
use Colissimo\Shipping\Model\Pickup\Collection;
use Colissimo\Shipping\Model\ResourceModel\Pickup as ResourceModel;
use Colissimo\Shipping\Helper\Data as ShippingHelper;
use Magento\Framework\DataObject;

/**
 * Class Pickup
 */
class Pickup extends DataObject implements PickupInterface
{

    /**
     * @var ShippingHelper $shippingHelper
     */
    protected $shippingHelper;

    /**
     * @var Collection $pickupCollection
     */
    protected $pickupCollection;

    /**
     * @var Soap $soap
     */
    protected $soap;

    /**
     * @var ResourceModel $pickup
     */
    protected $pickup;

    /**
     * @param Collection $pickupCollection
     * @param ShippingHelper $shippingHelper
     * @param Soap $soap
     * @param ResourceModel $pickup
     * @param array $data
     */
    public function __construct(
        Collection $pickupCollection,
        ShippingHelper $shippingHelper,
        Soap $soap,
        ResourceModel $pickup,
        array $data = []
    ) {
        $this->shippingHelper = $shippingHelper;
        $this->pickupCollection = $pickupCollection;
        $this->soap = $soap;
        $this->pickup = $pickup;
        parent::__construct($data);
    }

    /**
     * Retrieve Pickup List
     *
     * @param string $street
     * @param string $city
     * @param string $postcode
     * @param string $country
     * @return \Colissimo\Shipping\Model\Pickup\Collection
     */
    public function getList($street, $city, $postcode, $country)
    {
        return $this->pickupCollection->loadItems($this, $street, $city, $postcode, $country);
    }

    /**
     * Load specific pickup
     *
     * @param string $pickupId
     * @param string $network
     * @return \Colissimo\Shipping\Api\Data\PickupInterface
     */
    public function load($pickupId, $network)
    {
        if (!$pickupId || !$network) {
            return $this;
        }

        $data = [
            'date'        => date('d/m/Y'),
            'filterRelay' => '1',
            'id'          => $pickupId,
            'reseau'      => $network ?: '',
        ];

        $response = $this->soap->execute('findPointRetraitAcheminementByID', $data);

        if ($response['error']) {
            return $this;
        }

        if (isset($response['response']->pointRetraitAcheminement)) {
            foreach ($response['response']->pointRetraitAcheminement as $k => $v) {
                $key = preg_replace_callback(
                    '/([A-Z])/',
                    create_function('$m', 'return "_".strtolower($m[1]);'),
                    $k
                );
                $this->setData(trim($key, '_'), $v);
            }
        }

        return $this;
    }

    /**
     * Retrieve current pickup for quote
     *
     * @param string|int $cartId
     * @return $this
     */
    public function current($cartId)
    {
        $pickup = $this->pickup->currentPickup($cartId);

        if ($pickup) {
            $this->load($pickup['pickup_id'], $pickup['network_code']);
        }

        return $this;
    }

    /**
     * Save pickup data for quote
     *
     * @param string $cartId
     * @param string $pickupId
     * @param string $networkCode
     * @return bool
     */
    public function save($cartId, $pickupId, $networkCode)
    {
        return $this->pickup->savePickup($cartId, $pickupId, $networkCode);
    }

    /**
     * Reset pickup data for quote
     *
     * @param string $cartId
     * @return bool
     */
    public function reset($cartId)
    {
        return $this->pickup->resetPickup($cartId);
    }

    /**
     * Pickup name
     *
     * @return string
     */
    public function getNom()
    {
        $value = $this->getData('nom');
        if ($this->getCodePays() == 'DE') {
            $value = preg_replace('/¿/', 'B', $value);
        }
        return $value;
    }

    /**
     * Pickup address line 1
     *
     * @return string
     */
    public function getAdresse1()
    {
        $value = $this->getData('adresse1');
        if ($this->getCodePays() == 'DE') {
            $value = preg_replace('/¿/', 'B', $value);
        }
        return $value;
    }

    /**
     * Pickup address line 2
     *
     * @return string
     */
    public function getAdresse2()
    {
        $value = $this->getData('address2');
        if ($this->getCodePays() == 'DE') {
            $value = preg_replace('/¿/', 'B', $value);
        }
        return $value;
    }

    /**
     * Pickup address line 3
     *
     * @return string
     */
    public function getAdresse3()
    {
        return $this->getData('adresse3');
    }

    /**
     * Pickup postcode
     *
     * @return string
     */
    public function getCodePostal()
    {
        return $this->getData('code_postal');
    }

    /**
     * Pickup city
     *
     * @return string
     */
    public function getLocalite()
    {
        $value = $this->getData('localite');
        if ($this->getCodePays() == 'DE') {
            $value = preg_replace('/¿/', 'B', $value);
        }
        return $value;
    }

    /**
     * Pickup country code
     *
     * @return string
     */
    public function getCodePays()
    {
        return $this->getData('code_pays');
    }

    /**
     * Pickup language
     *
     * @return string
     */
    public function getLangue()
    {
        return $this->getData('langue');
    }

    /**
     * Pickup country
     *
     * @return string
     */
    public function getLibellePays()
    {
        return $this->getData('libelle_pays');
    }

    /**
     * Pickup has parking
     *
     * @return string
     */
    public function getParking()
    {
        return $this->getData('parking');
    }

    /**
     * Pickup identifier
     *
     * @return string
     */
    public function getIdentifiant()
    {
        return $this->getData('identifiant');
    }

    /**
     * Pickup product code
     *
     * @return string
     */
    public function getTypeDePoint()
    {
        return $this->getData('type_de_point');
    }

    /**
     * Pickup network code
     *
     * @return string
     */
    public function getReseau()
    {
        return $this->getData('reseau');
    }

    /**
     * Pickup latitude
     *
     * @return string
     */
    public function getCoordGeolocalisationLatitude()
    {
        return $this->getData('coord_geolocalisation_latitude');
    }

    /**
     * Pickup longitude
     *
     * @return string
     */
    public function getCoordGeolocalisationLongitude()
    {
        return $this->getData('coord_geolocalisation_longitude');
    }

    /**
     * Pickup has handicap access
     *
     * @return int
     */
    public function getAccesPersonneMobiliteReduite()
    {
        return $this->getData('acces_personneMobilite_reduite');
    }

    /**
     * Pickup partial holidays
     *
     * @return string
     */
    public function getCongesPartiel()
    {
        return $this->getData('conges_partiel');
    }

    /**
     * Pickup distance from address in meter
     *
     * @return string
     */
    public function getDistanceEnMetre()
    {
        return $this->getData('distance_en_metre');
    }

    /**
     * Pickup monday opening
     *
     * @return string
     */
    public function getHorairesOuvertureLundi()
    {
        return $this->formatOpening(
            $this->getData('horaires_ouverture_lundi')
        );
    }

    /**
     * Pickup tuesday opening
     *
     * @return string
     */
    public function getHorairesOuvertureMardi()
    {
        return $this->formatOpening(
            $this->getData('horaires_ouverture_mardi')
        );
    }

    /**
     * Pickup wednesday opening
     *
     * @return string
     */
    public function getHorairesOuvertureMercredi()
    {
        return $this->formatOpening(
            $this->getData('horaires_ouverture_mercredi')
        );
    }

    /**
     * Pickup thursday opening
     *
     * @return string
     */
    public function getHorairesOuvertureJeudi()
    {
        return $this->formatOpening(
            $this->getData('horaires_ouverture_jeudi')
        );
    }

    /**
     * Pickup friday opening
     *
     * @return string
     */
    public function getHorairesOuvertureVendredi()
    {
        return $this->formatOpening(
            $this->getData('horaires_ouverture_vendredi')
        );
    }

    /**
     * Pickup saturday opening
     *
     * @return string
     */
    public function getHorairesOuvertureSamedi()
    {
        return $this->formatOpening(
            $this->getData('horaires_ouverture_samedi')
        );
    }

    /**
     * Pickup sunday opening
     *
     * @return string
     */
    public function getHorairesOuvertureDimanche()
    {
        return $this->formatOpening(
            $this->getData('horaires_ouverture_dimanche')
        );
    }

    /**
     * Pickup localisation tip
     *
     * @return string
     */
    public function getIndiceDeLocalisation()
    {
        return $this->getData('indice_de_localisation');
    }

    /**
     * Pickup activity beginning
     *
     * @return string
     */
    public function getPeriodeActiviteHoraireDeb()
    {
        return $this->getData('periode_activite_horaire_deb');
    }

    /**
     * Pickup activity ending
     *
     * @return string
     */
    public function getPeriodeActiviteHoraireFin()
    {
        return $this->getData('periode_activite_horaire_fin');
    }

    /**
     * Pickup maximum weight
     *
     * @return string
     */
    public function getPoidsMaxi()
    {
        return $this->getData('poids_maxi');
    }

    /**
     * Pickup has handling tool
     *
     * @return string
     */
    public function getLoanOfHandlingTool()
    {
        return $this->getData('loan_of_handling_tool');
    }

    /**
     * Pickup data for pickup shipping label
     *
     * @return string
     */
    public function getDistributionSort()
    {
        return $this->getData('distribution_sort');
    }

    /**
     * Pickup data for pickup shipping label
     *
     * @return string
     */
    public function getLotAcheminement()
    {
        return $this->getData('lot_acheminement');
    }

    /**
     * Pickup data for pickup shipping label
     *
     * @return string
     */
    public function getVersionPlanTri()
    {
        return $this->getData('version_plan_tri');
    }

    /**
     * Pickup Holidays
     *
     * @return string[]|null
     */
    public function getListeConges()
    {
        return is_object($this->getData('liste_conges')) ?
            [
                'calendarDeDebut' => $this->getData('liste_conges')->calendarDeDebut,
                'calendarDeFin'   => $this->getData('liste_conges')->calendarDeFin
            ]
            : null;
    }

    /**
     * Format opening day
     *
     * @param string $day
     * @return string|null
     */
    protected function formatOpening($day)
    {
        $date = trim(
            preg_replace(
                ['/00:00-00:00/', '/:/', '/ /', '/-/'],
                ['', 'h', ' / ', ' - '],
                $day
            ), ' / '
        );

        return $date ?: null;
    }
}