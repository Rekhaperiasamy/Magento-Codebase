<?php
/**
 * Colissimo Label Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Label\Model\Deposit;

use Zend_Pdf;
use Zend_Pdf_Page;
use Zend_Pdf_Font;
use Zend_Pdf_Color_Rgb;
use Zend_Pdf_Color_GrayScale;
use Zend_Pdf_Exception;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Pdf
 */
class Pdf
{

    /**
     * @var Zend_Pdf $zendPdf
     */
    protected $zendPdf;

    /**
     * @var DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @param DateTime $dateTime
     */
    public function __construct(
        DateTime $dateTime
    ) {
        $this->zendPdf  = new Zend_Pdf();
        $this->dateTime = $dateTime;
    }

    /**
     * Retrieve PDF file (FR)
     *
     * @param array $data
     * @return string
     * @throws Zend_Pdf_Exception
     */
    public function getFile($data) {
        $pdf = $this->zendPdf;
        $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

        $minYPosToChangePage = 60;
        $xPos = 20;
        $yPos = $page->getHeight() - 40;
        $lineHeight = 15;

        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES);
        $fontBold = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_BOLD);

        /* DATE */
        $page->setFont($font, 11);
        $page->drawText('DATE : '. $this->dateTime->date('Y-m-d'), $page->getWidth() - 120, $yPos, 'UTF-8');
        $page->setFont($font, 12);

        /* TITRE */
        $page->setFont($fontBold, 12);
        $page->drawText('BORDEREAU DE REMISE Offre Entreprises Colissimo', $xPos, $yPos, 'UTF-8');
        $page->setFont($font, 12);

        $yPos -= 10;

        $page->drawLine($xPos, $yPos, $page->getWidth() - 20, $yPos);

        $yPos -= 40;

        /* SITE */
        $page->drawText('SITE DE PRISE EN CHARGE : ', $xPos, $yPos, 'UTF-8');
        $page->drawText($data['site']['number'], $xPos+230, $yPos, 'UTF-8');
        $yPos -= $lineHeight;

        $page->drawText('LIBELLE SITE DE PRISE EN CHARGE : ', $xPos, $yPos, 'UTF-8');
        $page->drawText($data['site']['name'], $xPos+230, $yPos, 'UTF-8');
        $yPos -= $lineHeight;
        $yPos -= 15;

        /* CUSTOMER */
        $page->drawText('CLIENT : ', $xPos, $yPos, 'UTF-8');
        $page->drawText($data['customer']['account_number'], $xPos+150, $yPos, 'UTF-8');
        $yPos -= $lineHeight;

        $page->drawText('LIBELLE CLIENT : ', $xPos, $yPos);
        $page->drawText($data['customer']['commercial_name'], $xPos+150, $yPos, 'UTF-8');
        $yPos -= $lineHeight;

        /* SHIPMENTS */
        $yPos -= 30;
        $page->setFont($fontBold, 12);
        $page->drawText('DETAIL DES ENVOIS', $xPos, $yPos, 'UTF-8');

        $yPos -= 10;
        $page->drawLine($xPos, $yPos, $page->getWidth() - 20, $yPos);

        $yPos -= ($lineHeight+5);
        $page->setFont($font, 12);

        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.85, 0.85, 0.85));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle($xPos, $yPos, 570, $yPos -20);
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
        $yPos -= 15;

        $page->drawText('REFERENCE', $xPos+5, $yPos, 'UTF-8');
        $page->drawText('NOM', $xPos+90, $yPos, 'UTF-8');
        $page->drawText('COLIS', $xPos+220, $yPos, 'UTF-8');
        $page->drawText('CPOST', $xPos+320, $yPos, 'UTF-8');
        $page->drawText('CPAYS', $xPos+375, $yPos, 'UTF-8');
        $page->drawText('POIDS (KG)', $xPos+430, $yPos, 'UTF-8');
        $page->drawText('NM', $xPos+510, $yPos, 'UTF-8');
        $yPos -= 5;

        foreach($data['shipments'] as $shipment) {
            $page->setFont($font, 12);
            $page->setFillColor(new Zend_Pdf_Color_Rgb(255, 255, 255));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle($xPos, $yPos, 570, $yPos -20);
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
            $yPos -= 15;

            $page->drawText($shipment['increment_id'], $xPos+5, $yPos, 'UTF-8');
            $page->drawText($shipment['name'], $xPos+90, $yPos, 'UTF-8');
            $page->drawText($shipment['tracking'], $xPos+220, $yPos, 'UTF-8');
            $page->drawText($shipment['postcode'], $xPos+320, $yPos, 'UTF-8');
            $page->drawText($shipment['country'], $xPos+375, $yPos, 'UTF-8');
            $page->drawText($shipment['weight'], $xPos+430, $yPos, 'UTF-8');
            $page->drawText($shipment['nm'], $xPos+510, $yPos, 'UTF-8');
            $yPos -= 5;

            if($yPos <= $minYPosToChangePage) {
                $pdf->pages[] = $page;
                $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
                $yPos = $page->getHeight()-20;
            }
        }

        /* SUMMARY */
        $yPos -= 50;
        $page->setFont($fontBold, 12);
        $page->drawText('RESUME ', $xPos, $yPos, 'UTF-8');
        $page->setLineColor(new Zend_Pdf_Color_Rgb(0, 0, 0));

        $yPos -= 10;
        $page->drawLine($xPos, $yPos, $page->getWidth() - 20, $yPos);

        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));

        $yPos -= ($lineHeight+5);
        $page->setFont($font, 12);

        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.85, 0.85, 0.85));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle($xPos, $yPos, 570, $yPos -20);
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
        $yPos -= 15;

        $page->drawText('NOMBRE TOTAL DE COLIS', $xPos+5, $yPos, 'UTF-8');
        $page->drawText('POIDS TOTAL DES COLIS (KG)', $xPos+180, $yPos, 'UTF-8');

        $yPos -= 5;

        $page->setFillColor(new Zend_Pdf_Color_Rgb(255, 255, 255));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle($xPos, $yPos, 570, $yPos -20);
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
        $yPos -= 15;

        $page->drawText($data['summary']['total_shipment'], $xPos+5, $yPos, 'UTF-8');
        $page->drawText($data['summary']['total_weight'], $xPos+180, $yPos, 'UTF-8');

        $yPos -= 5;

        if($yPos <= $minYPosToChangePage) {
            $pdf->pages[] = $page;
            $page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
            $yPos = $page->getHeight()-20;
        }

        /* SIGNATURE */
        $yPos -= 60;
        $page->setFont($font, 12);
        $page->drawText('SIGNATURE DU CLIENT', $xPos, $yPos, 'UTF-8');
        $page->drawText('SIGNATURE DE L\'AGENT', 400, $yPos, 'UTF-8');

        $pdf->pages[] = $page;

        return $pdf->render();
    }

    /**
     * Retrieve deposit file name
     *
     * @return string
     */
    public function getFileName()
    {
        return 'colissimo-deposit-' . $this->dateTime->date('Y-m-d') . '.pdf';
    }
}