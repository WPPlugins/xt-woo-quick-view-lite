!function($){function t(){var t=document.createElement("p"),a=!1;if(t.addEventListener)t.addEventListener("DOMAttrModified",function(){a=!0},!1);else{if(!t.attachEvent)return!1;t.attachEvent("onDOMAttrModified",function(){a=!0})}return t.setAttribute("id","target"),a}function a(t,a){if(t){var e=this.data("attr-old-value");if(a.attributeName.indexOf("style")>=0){e.style||(e.style={});var n=a.attributeName.split(".");a.attributeName=n[0],a.oldValue=e.style[n[1]],a.newValue=n[1]+":"+this.prop("style")[$.camelCase(n[1])],e.style[n[1]]=a.newValue}else a.oldValue=e[a.attributeName],a.newValue=this.attr(a.attributeName),e[a.attributeName]=a.newValue;this.data("attr-old-value",e)}}var e=window.MutationObserver||window.WebKitMutationObserver;$.fn.attrchange=function(n,r){if("object"==typeof n){var i={trackValues:!1,callback:$.noop};if("function"==typeof n?i.callback=n:$.extend(i,n),i.trackValues&&this.each(function(t,a){for(var e={},n,t=0,r=a.attributes,i=r.length;t<i;t++)n=r.item(t),e[n.nodeName]=n.value;$(this).data("attr-old-value",e)}),e){var c={subtree:!1,attributes:!0,attributeOldValue:i.trackValues},o=new e(function(t){t.forEach(function(t){var a=t.target;i.trackValues&&(t.newValue=$(a).attr(t.attributeName)),"connected"===$(a).data("attrchange-status")&&i.callback.call(a,t)})});return this.data("attrchange-method","Mutation Observer").data("attrchange-status","connected").data("attrchange-obs",o).each(function(){o.observe(this,c)})}return t()?this.data("attrchange-method","DOMAttrModified").data("attrchange-status","connected").on("DOMAttrModified",function(t){t.originalEvent&&(t=t.originalEvent),t.attributeName=t.attrName,t.oldValue=t.prevValue,"connected"===$(this).data("attrchange-status")&&i.callback.call(this,t)}):"onpropertychange"in document.body?this.data("attrchange-method","propertychange").data("attrchange-status","connected").on("propertychange",function(t){t.attributeName=window.event.propertyName,a.call($(this),i.trackValues,t),"connected"===$(this).data("attrchange-status")&&i.callback.call(this,t)}):this}if("string"==typeof n&&$.fn.attrchange.hasOwnProperty("extensions")&&$.fn.attrchange.extensions.hasOwnProperty(n))return $.fn.attrchange.extensions[n].call(this,r)}}(jQuery);