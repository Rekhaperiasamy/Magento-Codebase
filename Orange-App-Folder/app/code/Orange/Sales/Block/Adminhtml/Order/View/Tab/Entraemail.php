<?php

namespace Orange\Sales\Block\Adminhtml\Order\View\Tab;


class Entraemail extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'order/view/tab/entraemail.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
		$this->_layoutFactory = $layoutFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Entra Email');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Entra Email');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        // For me, I wanted this tab to always show
        // You can play around with the ACL settings 
        // to selectively show later if you want
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        // For me, I wanted this tab to always show
        // You can play around with conditions to
        // show the tab later
        return false;
    }

    /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        // I wanted mine to load via AJAX when it's selected
        // That's what this does
        return 'ajax only';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

    /**
     * Get Tab Url
     *
     * @return string
     */
    public function getTabUrl()
    {
        // entratab is a adminhtml router we're about to define
        // the full route can really be whatever you want
        return $this->getUrl('entratab/*/entratab', ['_current' => true]);
    }
	
	//get entra status
	public function getEntraStatus()
	{
		$order = $this->getOrder();
		$status = $order->getData("entra_status");
		switch ($status) {
			case "0":
				$entra_status = "Not Sent";
				break;
			case "1";
				$entra_status = "Sent";
				break;
			default:
				$entra_status = "Not Sent";
				break;
		}
		return $entra_status;
	}
	public function getTimeAccordingToTimeZone($dateTime) {
        // for convert date time according to magento time zone
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$timezoneInterface = $objectManager->create('Magento\Framework\Stdlib\DateTime\TimezoneInterface');        
        $dateTimeAsTimeZone = $timezoneInterface
									->date(new \DateTime($dateTime))
									->format('d/m/Y H:i:s A');
        return $dateTimeAsTimeZone;
    }
	
	public function getEntraEmailData() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $this->getOrder();
		// Getting store id
        $storeId = $order->getStoreId();
        $items = $order->getAllItems();
        $billingAddress = $order->getBillingAddress();
		$model = $objectManager->create('Orange\Abandonexport\Model\Items');
		$abandonexport = $model->getCollection()->addFieldToFilter('quote_id',$order->getQuoteId());
		$gender = unserialize($abandonexport->getFirstItem()->getStepsecond());
		$hasShipping = false;
		foreach($items as $item) {
            if(is_object($item->getProduct())) {
                if ($item->getProduct()->getTypeId() == 'bundle') {
                    $hasShipping = true;
                }
            }
		}
        $data = array();
        $street = $billingAddress->getStreet();
		if(isset($gender['scoringResponse'])) {
			$data['score'] = $gender['scoringResponse'];
		} else {
			$data['score'] = $order->getScore();
		}
		if($billingAddress->getPrefix() == '1'){
		$data['prefix'] = 'Mr.';
		} else {
		$data['prefix'] = 'Mrs.';
			if ($storeId == '1') {
				$data['prefix'] = 'Mevr.';
			} else if($storeId == '2') {
			   $data['prefix'] = 'Mme.';
			}
		}
        $data['custFirstName'] = $billingAddress->getFirstname();
        $data['CustLastName'] = $billingAddress->getLastname();
        $data['email'] = $billingAddress->getEmail();
		if ($hasShipping) {
			$data['hasShipping'] = true;
			if(isset($gender['c_delivery_address']) && $gender['c_delivery_address'] ==1) {
				if ($storeId == '1') {
					$data['shippingMethod'] = 'Gebruik hetzelfde adres als het factuuradres';
				} else {
					$data['shippingMethod'] = 'Utiliser la même adresse que l’adresse de facturation';
				}
			} else if(isset($gender['c_delivery_address']) && $gender['c_delivery_address'] ==2) {
				$data['hasShippingOther'] = true;
				if(isset($gender['s_firstname'])) {
					$data['s_firstname'] = $gender['s_firstname'].' ';
				}
				if(isset($gender['s_name'])) {
					$data['s_name'] = $gender['s_name'].' ';
				}
				if(isset($gender['s_attention_n'])) {
					$data['s_attention_n'] = $gender['s_attention_n'].' ';
				}
				if(isset($gender['s_postcode_city']) && $gender['s_postcode_city']) {
					$zipCode = $gender['s_postcode_city'];
				} else {
					$zipCode = $gender['b_postcode_city'];
				}
				if(isset($gender['s_street'])) {
					$data['s_street'] = $gender['s_street'];
				}
				if(isset($gender['s_number'])) {
					$data['s_number'] = $gender['s_number'];
				}
				if(isset($gender['s_box'])) {
					$data['s_box'] = $gender['s_box'];
				}
				if(isset($gender['s_city']) && $gender['s_city'] ) {
					$city = $gender['s_city'];
				} else {
					$city = $gender['b_city'];
				}
				$data['s_postcode_city'] = $zipCode.' '.$city;
				if ($storeId == '1') {
					$data['shippingMethod'] = 'Anders';
				} else {
					$data['shippingMethod'] = 'Autre';
				}
			}
		}
		if(isset($gender['cust_telephone']) && $gender['cust_telephone'] ) {
			$data['phone'] = $gender['cust_telephone'];
		}
        $yep = $order->getDateOfBirth();
		$time = strtotime($yep);
		$newformat = date('d/m/Y',$time);
        $data['dob'] = $newformat;
		if($order->getGrandTotal() > 0 ) {
			$data['ispaid'] = true;
			$payments = $order->getPaymentsCollection();
			foreach ($payments as $_payment) {
				$method = $_payment->getMethod();
				$card_details = $_payment->getCcLast4();
			}
			$data['cardmethod'] = $order->getPayment()->getMethodInstance()->getTitle();
			if (isset($card_details)) {
				$data['cardDetails'] = $card_details;
			} else {
				$data['cardDetails'] = '';
			}
			$data['grandTotal'] = number_format($order->getGrandTotal(),2).' €';
		}
        $data['Country'] = 'Belgium';
		/*if(isset($gender['nationality']) && $gender['nationality']){
		    if ($gender['nationality'] =='belgian') {
				if ($storeId == '1') {
					$data['nationality'] = 'Belg';
					$data['tIdentityCardno'] = 'Identiteitskaartnummer';
				} else {
					$data['nationality'] = 'Belge';
					$data['tIdentityCardno'] = 'N° de carte d’identité';
				}
			} else if($gender['nationality'] =='other'){
				if($gender['registered'] == 1) {
					if ($storeId == '1') {
						$data['nationality'] = 'Andere';
						$data['NationalityOther'] = 'Ja';
						$data['tNationalityOther'] = 'Ben je in België geregistreerd?';
						$data['tIdentityCardno'] = 'Nummer verblijfskaart';
					} else {
						$data['nationality'] = 'Autre';
						$data['NationalityOther'] = 'Oui';
						$data['tNationalityOther'] = 'Êtes-vous enregistré en Belgique ?';
						$data['tIdentityCardno'] = 'N° de titre de séjour';
					}
				} else {
					if ($storeId == '1') {
						$data['nationality'] = 'Andere';
						$data['NationalityOther'] = 'Neen';
						$data['tNationalityOther'] = 'Ben je in België geregistreerd?';
						$data['tIdentityCardno'] = 'Paspoortnummer';
					} else {
						$data['nationality'] = 'Autre';
						$data['NationalityOther'] = 'Non';
						$data['tNationalityOther'] = 'Êtes-vous enregistré en Belgique ?';
						$data['tIdentityCardno'] = 'N° de passeport';
					}
				}
			}
		}*/ // 1802 NID project
		if(isset($gender['passport_number']) && $gender['passport_number']) {
			$data['passport'] = str_replace("-","",$gender['passport_number']);
		} else if(isset($gender['id_number']) && $gender['id_number']) {
			$data['passport'] = str_replace("-","",$gender['id_number']);
		} else if(isset($gender['residence_number']) && $gender['residence_number']) {
			$data['passport'] = str_replace(" ","",$gender['residence_number']);
		}
		// Start National ID implementation
		if(isset($gender['nationality']) && $gender['nationality']){
		    if ($gender['nationality'] =='belgian') {
				if ($storeId == '1') {
					$data['nationality'] = 'Ja';
					$data['tIdentityCardno'] = 'Nummer identiteitbewijs';
				} else {
					$data['nationality'] = 'Oui';
					$data['tIdentityCardno'] = "N° du document d'identité";
				}
			}
		}
		if(isset($gender['id_number']) && $gender['id_number']) {
			$data['id_number'] = str_replace(" ","", str_replace("-","",$gender['id_number']));
		}
		if(isset($gender['national_id_number']) && $gender['national_id_number']) {
			$data['national_id'] = str_replace(" ","",$gender['national_id_number']);
		}
		// End National ID implementation
		if($order->getAccountNumber()) {
			$data['domiciliatoin'] = str_replace(" ","",$order->getAccountNumber());
		}
		if($order->getAccountNumber() == ''){
			if ($storeId == '1') {
				$data['domiciliatoinVirment'] = 'Overschrijving';
			}else{
				$data['domiciliatoinVirment'] = 'Virement';
			}			
		}else{
			if ($storeId == '1') {
				$data['domiciliatoinVirment'] = 'Domiciliëring';
			}else{
				$data['domiciliatoinVirment'] = 'Domiciliation';
			}
		}
		if(isset($gender['offer_subscription']) && $gender['offer_subscription']) {
			if ($storeId == '1') {
				$newsletter = 'Ja';
			}
			else{
				$newsletter = 'Oui';
			}
		}else{
			if ($storeId == '1') {
				$newsletter = 'Neen';
			}
			else{
				$newsletter = 'non';
			}
		}
		if(isset($gender['b_street']) && $gender['b_street']) {
			$data['street'] = strtoupper(trim($gender['b_street']));
		}
		if(isset($gender['b_number']) && $gender['b_number']) {
			$data['b_number'] = $gender['b_number'];
		}
		if(isset($gender['b_box']) && $gender['b_box']) {
			$data['box'] = $gender['b_box'];
		}
        if ($billingAddress->getTxDropDown() != "") {
			if ($storeId == '1') {
				$data['isprofile'] = 'Ja';
			} else {
			   $data['isprofile'] = 'Oui';
			}
        } else {
			if ($storeId == '1') {
				$data['isprofile'] = 'Neen';
			} else {
				$data['isprofile'] = 'non';
			}
        }
		if ($billingAddress->getTxDropDown() != "") {
            $data['profile'] = $billingAddress->getTxDropDown();
        }
		if($billingAddress->getLegalStatus() !="") {
			$data['legal'] = $billingAddress->getLegalStatus();
		}
        if ($billingAddress->getCompanyName() != "") {
            $data['companyname'] = $billingAddress->getCompanyName();
        }
        if ($billingAddress->getVatNumber() != "") {
            $data['vat'] = $billingAddress->getVatNumber();
        }
        $data['city'] = $billingAddress->getCity();
        $data['PostCode'] = $billingAddress->getPostcode().' '.strtoupper(trim($billingAddress->getCity()));
        $data['OrderId'] = $order->getIncrementId();
        $orderCreated = $this->getTimeAccordingToTimeZone($order->getCreatedAt());
		//$orderCreatedTime = strtotime($orderCreated);
		//$data['OrderTime'] = date('d/m/Y H:i:s A',$orderCreatedTime);
		$data['OrderTime'] = $orderCreated;
        $data['newsletter_subscription'] = $newsletter;
		$couponBlock = $this->_layoutFactory->create()->createBlock('Amasty\Coupons\Block\Coupon');
		$couponCodes = strlen($order->getCouponCode());
		if($couponCodes > 1) {
			$data['iscoupon'] = true;
			$data['couponcode'] = $order->getCouponCode();
		}
        /* language Translation */
        if ($storeId == '2') {
            /* French Language Pack */
            $data['tYourTelephoneNumberFor'] = 'Votre numéro de téléphone pour';
            $data['tYourPhoneNumber'] = 'Votre numéro d appel pour';
            $data['tKeepYourPhoneNumber'] = 'Numéro d appel  Garder votre';
            $data['tCurrentOpeartor'] = 'Opérateur actuel';
            $data['tCurrentPhoneNumber'] = 'Numéro de GSM actuel';
            $data['tYourPersonalData'] = 'Vos données personnelles';
            $data['tTitle'] = 'Titre';
            $data['tFirstName'] = 'Prénom';
			$data['tdomicilationnumber'] = 'N° de compte';
            $data['tName'] = 'Nom';
            $data['tAttention'] = 'A l’attention de';
            $data['tEmailAddress'] = 'Adresse email';
            $data['tPhoneNumber'] = 'Numéro de téléphone';
            $data['tDob'] = 'Date de naissance';
            $data['tNationality'] = "Document d'identité belge";
			$data['tNationalIdentityCardno'] = "N° de Registre national";
            $data['tYourAddress'] = 'Votre adresse';
            $data['tCodePostal'] = 'Code postal';
            $data['tStreet'] = 'Rue';
            $data['tNumber'] = 'Numéro';
            $data['tBox'] = 'Boîte';
            $data['tCountry'] = 'Pays';
            $data['tYourBill'] = 'Votre facture';
            $data['tAddToExitingMobistarBill'] = 'Voulez-vous ajouter ce produit sur une facture Mobistar existante ?';
			$data['tPaymentMethod'] = 'Méthode de paiement';
            $data['tMeansOfPaymentTransfer'] = 'Moyen de paiement de votre facture';
            $data['tYouWillRecieveEmail'] = 'Vous recevrez chaque mois un mail avec votre facture en format PDF à l adresse mail suivante';
            $data['tYourDataAvailableWithUs'] = 'En plus, votre facture reste disponible dans votre espace client à tout moment';
            $data['tYouWantToRecieveMobiNews'] = 'Voulez-vous recevoir des offres intéressantes et les dernières nouvelles Orange?';
            //specially for soho Customers french
            $data['tCurrentSimCardNumber'] = 'Numéro de carte SIM actuel';
            $data['tCurrentCustomerNumber'] = 'Numéro de client actuel';
            $data['tAreYouHolderContract'] = 'Etes-vous le titulaire du contrat ?';
            $data['tYourProfile'] = 'Votre profil';
            $data['tprofile'] = 'Profil';
            $data['tisprofile'] = 'Indépendant ou société';
            $data['tlegal'] = 'Statut juridique';
            $data['tVatNumber'] = 'N° de TVA';
            $data['tDelievery'] = 'Livraison';
            $data['tDelieverAddress'] = 'Adresse de livraison';
            $data['tcompanyName'] = 'Nom de la société';
            $data['tForAttentionOf'] = 'A lattention de';
			$data['tcardDetails'] = 'Numéro de carte';
			$data['tgrandTotal'] = 'Total payé maintenant';
			$data['tsubsPayment'] = 'Paiement de l’abonnement';
			$data['torderPayment'] = 'Paiement de votre commande';
			$data['tshippingMethod'] = 'Adresse de livraison de votre téléphone';
			$data['sAddress'] = 'Adresse';
        } elseif ($storeId == '1') {
            /* Dutch Language Pack */
            $data['tYourTelephoneNumberFor'] = 'Je oproepnummer voor';
            $data['tYourPhoneNumber'] = 'Je oproepnummer voor';
            $data['tKeepYourPhoneNumber'] = 'Gsm nummer	Behoud je nummer';
            $data['tCurrentOpeartor'] = 'Huidige operator';
            $data['tCurrentPhoneNumber'] = 'Huidig GSM nummer';
			$data['tdomicilationnumber'] = 'Rekeningnummer';
            $data['tYourPersonalData'] = 'Je persoonlijke gegevens';
            $data['tTitle'] = 'Titel';
            $data['tFirstName'] = 'Voornaam';
            $data['tName'] = 'Naam';
			$data['tAttention'] = 'Ter attentie van';
            $data['tEmailAddress'] = 'E-mailadres';
            $data['tPhoneNumber'] = 'Telefoonnummer';
            $data['tDob'] = 'Geboortedatum';
            $data['tNationality'] = "Belgisch identiteitsbewijs";
            $data['tNationalIdentityCardno'] = "Rijksregisternummer";
            $data['tYourAddress'] = 'Je adres';
            $data['tCodePostal'] = 'Postcode';
            $data['tStreet'] = 'Straat';
            $data['tNumber'] = 'Nummer';
            $data['tBox'] = 'Bus';
            $data['tCountry'] = 'Land';
            $data['tYourBill'] = 'Je factuur';
            $data['tAddToExitingMobistarBill'] = 'Wilt u deze factuur toe te voegen aan een bestaande mobistar bill?';
			$data['tPaymentMethod'] = 'Betaalmethode';
            $data['tMeansOfPaymentTransfer'] = 'Manier om uw factuur te betalen';
            $data['tYouWillRecieveEmail'] = 'Je ontvangt je factuur in PDF formaat op volgend e-mail adres:';
            $data['tYourDataAvailableWithUs'] = 'Bovendien is de factuur beschikbaar blijft in uw account op elk gewenst moment';
            $data['tYouWantToRecieveMobiNews'] = 'Wilt u interessante aanbiedingen en nieuws Orange ontvangen?';
            //specially for soho Dutch
            $data['tCurrentSimCardNumber'] = 'Huidig simkaartnummer';
            $data['tCurrentCustomerNumber'] = 'Huidige klantnummer';
            $data['tAreYouHolderContract'] = 'Ben jij de verzekeringnemer?';
            $data['tYourProfile'] = 'Je profiel';
            $data['tprofile'] = 'Profiel';
			$data['tisprofile'] = 'Zelfstandige of bedrijf';
			$data['tlegal'] = 'Juridisch statuut';
            $data['tVatNumber'] = 'Btw-nummer';
            $data['tDelievery'] = 'Levering';
            $data['tDelieverAddress'] = 'Afleveradres';
            $data['tcompanyName'] = 'Bedrijfsnaam';
            $data['tForAttentionOf'] = 'Om de aandacht van';
			$data['tcardDetails'] = 'Kaartnummer';
			$data['tgrandTotal'] = 'Totaal nu betaald';
			$data['tsubsPayment'] = 'Betaling abonnement';
			$data['torderPayment'] = 'Betaling van je bestelling';
			$data['tshippingMethod'] = 'Leveringsadres van je toestel';
			$data['sAddress'] = 'Adres';
        }
		return $data;
	}
}