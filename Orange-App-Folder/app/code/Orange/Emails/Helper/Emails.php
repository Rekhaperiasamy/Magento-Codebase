<?php

namespace Orange\Emails\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Mail\Template\TransportBuilder;

class Emails extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'entra/mail/entra_email_template';

    /* Here section and group refer to name of section and group where you create this field in configuration */

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    protected $_logger;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var string
     */
    protected $temp_id;
	protected $_logHelper;
	protected $_timezoneInterface;

    /**
     * @param Magento\Framework\App\Helper\Context $context
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
		\Magento\Sales\Model\Order $_orderModel,
		\Magento\Framework\File\Csv $csvProcessor,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Orange\Upload\Helper\Data $logHelper
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_logger = $context->getLogger();
        $this->_storeManager = $storeManager;
        $this->csvProcessor = $csvProcessor;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_orderModel = $_orderModel;
		$this->_logHelper = $logHelper;
		$this->_layoutFactory = $layoutFactory;
		$this->_timezoneInterface 	= $timezoneInterface;
    }

    protected function getConfigValue($path, $storeId) {
        return $this->scopeConfig->getValue(
                        $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getStore() {
        return $this->_storeManager->getStore();
    }

    public function entraCredentials() {
        $Credentials = array();
        $Credentials['Entra_EmailSender'] = $this->scopeConfig->getValue('entra/entra_configuration/entra_sender', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_EmailSenderName'] = $this->scopeConfig->getValue('entra/entra_configuration/entra_sender_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_EmailReciever'] = $this->scopeConfig->getValue('entra/entra_configuration/entra_reciever', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_Prmotion_Message'] = $this->scopeConfig->getValue('entra/entra_configuration/promotion_block_entra', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $Credentials['Entra_Email_Reciever_Cc'] = $this->scopeConfig->getValue('entra/entra_configuration/entra_reciever_cc', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $Credentials;
    }
	
	public function getTimeAccordingToTimeZone($dateTime) {
        // for convert date time according to magento time zone
        $dateTimeAsTimeZone = $this->_timezoneInterface
									->date(new \DateTime($dateTime))
									->format('d/m/Y H:i:s A');
        return $dateTimeAsTimeZone;
    }

    public function entraMailProcess($orderId,$scoringResponse) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderId, 'increment_id');
        
        $log_mode = $this->scopeConfig->getValue('custom_modulelog/modulelog__configuration/setting_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		// Getting store id
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $store = $storeManager->getStore();
        $storeId = $store->getStoreId();
        $items = $order->getAllItems();
        $info = $this->entraCredentials();
        $promotions = $info['Entra_Prmotion_Message'];
        $billingAddress = $order->getBillingAddress();
		$model = $objectManager->create('Orange\Abandonexport\Model\Items');
		$abandonexport = $model->getCollection()->addFieldToFilter('quote_id',$order->getQuoteId());
		$gender = unserialize($abandonexport->getFirstItem()->getStepsecond());
		$hasShipping = false;
		foreach($items as $item) {
			if($item->getProduct()->getTypeId() == 'bundle'){
				$hasShipping = true;
			}
		}
        $emailTempVariables = array();
        $data = array();
        $street = $billingAddress->getStreet();
        $data['score'] = $scoringResponse;        
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
			}
			$data['grandTotal'] = number_format($order->getGrandTotal(),2).' €';
		}
        $data['Country'] = 'Belgium';
        $data['promotion'] = $promotions;
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
        $data['order'] = $order;
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
        //setting the final data for template
        $emailTempVariables = $data;
        /* Receiver Detail  */
        $info = $this->entraCredentials();
        $sender = $info['Entra_EmailSender'];
        $senderName = $info['Entra_EmailSenderName'];
        $reciever = $info['Entra_EmailReciever'];
        if ($reciever != '') {
            $receiverInfo = explode(',',$reciever);
        } else {
            echo 'Entra Email Receiver not configured in admin';
        }
        /* Sender Detail  */
        if ($senderName != '' && $sender != '') {
            $senderInfo = [
                'name' => $senderName,
                'email' => $sender,
            ];
        } else {
            echo 'Entra Email Sender Name and Email  not configured in admin';
        }
        if (($reciverCC = $info['Entra_Email_Reciever_Cc']) != "") {
            $reciverCC = explode(',',$info['Entra_Email_Reciever_Cc']);
        } else {
            $reciverCC = '';
        }
		$oneTimeEmailFalg ='true';		
		//echo '<pre>'; print_r($order->getData());exit;
		/*foreach ($items as $item) {
			$productType = $item->getProductType();
			 $Items_attributeSetId = $item->getProduct()->getAttributeSetId();	
			 if ($Items_attributeSetId && ($Items_attributeSetId == 11 || $Items_attributeSetId == 15)){
				echo '<pre>'; print_r($item->getData());
			 }
			 }exit;*/
        foreach ($items as $item) {
			
            //checking whether the customer is Soho or not            
            $customerSession = $objectManager->get('Magento\Customer\Model\Session');
            //CustomerGroup type is B2c & Soho
            $customerGrp = $customerSession->getCustomerTypeName();			
            //$customerGrp = 'B2c';
            $productType = $item->getProductType();
            $Items_attributeSetId = $item->getProduct()->getAttributeSetId();			
            if ($customerGrp == 'SOHO') {
                //customer is soho
                /*if (($productType == 'virtual') && ($item->getParentItemId() != "")) {
                    //product is plan and subsidy
                    if ($Items_attributeSetId && ($Items_attributeSetId == 11 || $Items_attributeSetId == 15) && $oneTimeEmailFalg =='true') {
						$oneTimeEmailFalg ='false';
                        //product is postpaid                        
                        $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
                        $templateVars = $emailTempVariables;
                        $from = $senderInfo;
                        $this->inlineTranslation->suspend();
                        $to = $receiverInfo;
                        $transport = $this->_transportBuilder->setTemplateIdentifier('entra_email_soho_bundle')
                                ->setTemplateOptions($templateOptions)
                                ->setTemplateVars($templateVars)
                                ->setFrom($from)
                                ->addTo($to)
                                ->addCc($reciverCC)
                                ->getTransport();
                        $transport->sendMessage();
                        $this->inlineTranslation->resume();
                    }
                } else*/ if (($productType == 'virtual')) {
                    //product is plan and not subsidy
                    if ($Items_attributeSetId && ($Items_attributeSetId == 11 || $Items_attributeSetId == 15) && $oneTimeEmailFalg =='true') {
						$oneTimeEmailFalg ='false';
                        //product is postpaid                        
                        $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
                        $templateVars = $emailTempVariables;
                        $from = $senderInfo;
                        $this->inlineTranslation->suspend();
                        $to = $receiverInfo;
                        $transport = $this->_transportBuilder->setTemplateIdentifier('entra_email_soho')
                                ->setTemplateOptions($templateOptions)
                                ->setTemplateVars($templateVars)
                                ->setFrom($from)
                                ->addTo($to)
                                ->addCc($reciverCC)
                                ->getTransport();
                        $transport->sendMessage();
                        $this->inlineTranslation->resume();
						$order->setEntraStatus(1);//Setting Entra Mail status as sent
						$order->save();
                                                if(isset($log_mode) && $log_mode==1){
                                                    $this->_logHelper->logCreate('/var/log/entra_mail_cron.log','Entra Mail Sent and entra mail status updated for the order '.$order->getIncrementId());
                                                }
						break;
                    }
                }
            } else {
                //customer is B2C
                /*if (($productType == 'virtual') && ($item->getParentItemId() != "")) {
                    //product is plan and subsidy
                    if ($Items_attributeSetId && ($Items_attributeSetId == 11 || $Items_attributeSetId == 15 ) && $oneTimeEmailFalg ='true') {
						$oneTimeEmailFalg ='false';						
                        //product is postpaid                        						
                        $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
                        $templateVars = $emailTempVariables;
                        $from = $senderInfo;
                        $this->inlineTranslation->suspend();
                        $to = $receiverInfo;
                        $transport = $this->_transportBuilder->setTemplateIdentifier('entra_email_bundle')
                                ->setTemplateOptions($templateOptions)
                                ->setTemplateVars($templateVars)
                                ->setFrom($from)
                                ->addTo($to)
                                ->addCc($reciverCC)
                                ->getTransport();
                        $transport->sendMessage();
                        $this->inlineTranslation->resume();
                    }
                } else*/ if (($productType == 'virtual')) {
                    //product is plan and not subsidy
                    if ($Items_attributeSetId && ($Items_attributeSetId == 11 || $Items_attributeSetId == 15)&& $oneTimeEmailFalg =='true') {						
						$oneTimeEmailFalg ='false';
                        //product is postpaid						
                        $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
                        $templateVars = $emailTempVariables;
                        $from = $senderInfo;
                        $this->inlineTranslation->suspend();
                        $to = $receiverInfo;
                        $transport = $this->_transportBuilder->setTemplateIdentifier('entra_email_template')
                                ->setTemplateOptions($templateOptions)
                                ->setTemplateVars($templateVars)
                                ->setFrom($from)
                                ->addTo($to)
                                ->addCc($reciverCC)
                                ->getTransport();
                        $transport->sendMessage();
                        $this->inlineTranslation->resume();
						$order->setEntraStatus(1);//Setting Entra Mail status as sent
						$order->save();
                                                if(isset($log_mode) && $log_mode==1){
                                                    $this->_logHelper->logCreate('/var/log/entra_mail_cron.log','Entra Mail Sent and entra mail status updated for the order '.$order->getIncrementId());
                                                }
						break;
                    }
                }
            }			
        }
    }

}