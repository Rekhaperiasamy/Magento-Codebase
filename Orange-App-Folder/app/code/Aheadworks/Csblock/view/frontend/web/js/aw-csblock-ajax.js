define(["jquery"],function(t){"use strict";return t.widget("mage.awCsblockAjax",{options:{url:"/",dataPattern:"aw-csblock-block-name"},_create:function(){var a=t("[data-"+this.options.dataPattern+"]");a&&a.length&&this.ajax(a)},ajax:function(a){var e=this,n={blocks:[]};a.each(function(){n.blocks.push(t(this).data(e.options.dataPattern))}),n.blocks=JSON.stringify(n.blocks.sort()),t.ajax({url:this.options.url,data:n,type:"GET",cache:!1,dataType:"json",context:this,success:function(n){a.each(function(){var a=t(this),o=a.data(e.options.dataPattern);n.hasOwnProperty(o)&&e.replacePlaceholder(a,n[o])})}})},replacePlaceholder:function(a,e){if(a){var n=t(a).parent();a.replaceWith(e),t(n).trigger("contentUpdated")}}}),t.mage.awCsblockAjax});