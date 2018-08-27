require(['jquery',
      'jquery/ui',
      'mage/translate',
	  "mage/calendar",
	  'js/tooltipform',
	  ], function ($) {	
		$('[data-toggle="tooltip"]').tooltip();
		$("#calendar_inputField").calendar({
              buttonText:"<?php echo __('Select Date') ?>",
         });
});