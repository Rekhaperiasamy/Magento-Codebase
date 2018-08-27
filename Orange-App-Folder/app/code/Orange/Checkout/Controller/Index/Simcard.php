<?php

namespace Orange\Checkout\Controller\Index;

class Simcard extends \Magento\Framework\App\Action\Action {

 
    public function execute() {
        $sim_num = $this->getRequest()->getParam('sim_number');
        $providers = $this->getRequest()->getParam('providers');
	
        if ($providers == 'Proximus') {
            $result = $this->proximus_sim_number($sim_num);		
            if (!$result) {
                echo "Current SIM card number and operator not matched.";
            }
        } else if ($providers == 'Base') {
            $result = $this->base_sim_number($sim_num);
            if (!$result) {
                echo "Current SIM card number and operator not matched.";
            }
        } else if ($providers == 'Telenet') {
            $result = $this->telenet_sim_number($sim_num);
            if (!$result) {
                 echo "Current SIM card number and operator not matched.";
            }
        } else if ($providers == 'Other' || $providers == 'Autre'  || $providers == 'Andere') {
            $result = $this->other_sim_number($sim_num);
            if (!$result) {
                echo "Current SIM card number and operator not matched.";
            }
        }
    }

    public function other_sim_number($value) {
        $ok = false;
        $value1 = $this->telenet_sim_number($value);
        $value2 = $this->base_sim_number($value);
        $value3 = $this->proximus_sim_number($value);        
        if ($value1 === true ||$value2 === true || $value3 === true ) {           
            $ok = true;
        }
        return $ok;
    }

    public function base_sim_number($value) {
        // ** Code Delivered by mobistar **//
        $ok = false;
        $value = preg_replace('#\s#', '', $value);
        if (preg_match('/(^\d{19}$)|(^\d{13}$)/', $value)) {
            $validat_digit = substr($value, -1); // dernier chiffre
            if (strlen($value) == 19) {
                $SerieNummer = substr($value, 6, -1); // du 7? caract?re jusqu'a l'avant dernier
            } else {
                $SerieNummer = substr($value, 0, -1); // allowing 13 digit number as well
            }
            $totalsom = 0;
            $pos = 0;
            $temp = 0;
            $fois2 = 0;
            $ln_marque = 0;
            $ln_nomarque = 0;
            for ($i = 0; $i < strlen($SerieNummer); $i++) {
                $pos = $pos + 1;
                $temp = substr($SerieNummer, $i, 1);
                $ln_nomarque = $ln_nomarque + $temp;
                if ($pos % 2 == 0) {
                    $fois2 = $temp * 2;
                    if ($fois2 >= 10) {
                        $ln_marque = $ln_marque + 1 + $fois2 - 10;
                    } else {
                        $ln_marque = $ln_marque + $fois2;
                    }
                } else {
                    $ln_marque = $ln_marque + $temp;
                }
            }
            $mod_10 = ($ln_marque % 10);
            if ($mod_10 == 0) {
                $mod_10 = 10;
            }
            if (10 - $mod_10 == $validat_digit) {
                $ok = true;
            }
        }
        return $ok;
    }

    public function telenet_sim_number($value) {
        // ** Code Delivered by mobistar **//
        $length = strlen($value);
        $ok = false;

        if ($length != 19)
            $ok = false;
        else
            $ok = true;
        if ((((int) ($value / 10000000000000)) == 894101) || (((int) ($value / 10000000000000)) == 893207))
            $ok = true;
        else
            $ok = false;

        return $ok;
    }

    public function proximus_sim_number($value) {
        // ** Code Delivered by mobistar **//
        $ok = false;
        if (preg_match('/^[0-9]{13}$/', $value)) {
            if (strlen($value) == 13) {
                $A = array();
                $som = 0;
                $check = 100;
                for ($i = 0; $i < 12; $i++) {
                    if ($i % 2 != 0) {
                        $A[$i] = substr($value, $i, 1) * 2;
                        if ($A[$i] > 9)
                            $A[$i] = $A[$i] - 9;
                    } else {
                        $A[$i] = substr($value, $i, 1);
                    }
                    $som += $A[$i];
                }
                $check = $check - ($som + 4);
                while ($check >= 10) {
                    $check = $check - 10;
                }
                if ($check == substr($value, strlen($value) - 1, 1))
                    $ok = true;
            }
        }
        return $ok;
    }

}
