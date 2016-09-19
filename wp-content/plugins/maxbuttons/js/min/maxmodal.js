var maxModal;jQuery(document).ready(function(t){maxModal=function(){},maxModal.prototype={currentModal:null,modals:[],controls:[],parent:"#maxbuttons",multiple:!1,windowHeight:!1,windowWidth:!1,setWidth:!1,setHeight:!1},maxModal.prototype.init=function(){this.windowHeight=t(window).height(),this.windowWidth=t(window).width(),t(document).on("click",".maxmodal",t.proxy(this.buildModal,this)),t(window).on("resize",t.proxy(this.checkResize,this))},maxModal.prototype.focus=function(){this.currentModal.show()},maxModal.prototype.get=function(){return this.currentModal},maxModal.prototype.show=function(){t(".maxmodal_overlay").remove(),this.writeOverlay(),this.setWidth&&this.currentModal.width(this.setWidth),this.setHeight&&this.currentModal.height(this.setHeight);var o=this.currentModal.height(),e=this.currentModal.width(),i=(this.windowHeight-o)/2,a=(this.windowWidth-e)/2;30>i&&(i=30),o>this.windowHeight&&this.currentModal.height(this.windowHeight-i-5+"px"),this.currentModal.css("left",a+"px"),this.currentModal.css("top",i+"px"),this.currentModal.css("height",o),this.currentModal.show(),t(".maxmodal_overlay").show(),t(document).off("keydown",t.proxy(this.keyPressHandler,this)),t(document).on("keydown",t.proxy(this.keyPressHandler,this))},maxModal.prototype.keyPressHandler=function(t){27===t.keyCode&&this.close()},maxModal.prototype.checkResize=function(){this.windowHeight=t(window).height(),this.windowWidth=t(window).width(),null!==this.currentModal&&(this.currentModal.removeAttr("style"),this.currentModal.find(".modal_content").removeAttr("style"),this.show())},maxModal.prototype.close=function(){this.currentModal.remove(),this.currentModal=null,t(".maxmodal_overlay").remove()},maxModal.prototype.setTitle=function(t){this.currentModal.find(".modal_title").text(t)},maxModal.prototype.setControls=function(o){var e=this.currentModal.find(".modal_content"),a=t('<div class="controls">');for(i=0;i<this.controls.length;i++)a.append(this.controls[i]);"undefined"!=typeof o&&a.append(o),e.append(a),t(this.currentModal).find(".modal_close").off("click"),t(this.currentModal).find(".modal_close").on("click",t.proxy(this.close,this))},maxModal.prototype.addControl=function(o,e,i){var a="";switch(o){case"yes":a=modaltext.yes;break;case"ok":a=modaltext.ok;break;case"no":a=modaltext.no;break;case"cancel":a=modaltext.cancel;break;case"insert":a=mbtrans.insert}var d=t('<a class="button-primary '+o+'">'+a+"</a>");d.on("click",e,i),this.controls.push(d)},maxModal.prototype.setContent=function(t){this.currentModal.find(".modal_content").html(t)},maxModal.prototype.buildModal=function(o){var e=t(o.target);"undefined"==typeof e.data("modal")&&(e=e.parents(".maxmodal"));var i=e.data("modal"),a=t("#"+i);this.setWidth="undefined"!=typeof a.data("width")?a.data("width"):!1,this.setHeight="undefined"!=typeof a.data("height")?a.data("height"):!1;var d=t(a).find(".title").text(),n=t(a).find(".controls").html(),s=t(a).find(".content").html();if(this.newModal(i),this.setTitle(d),this.setContent(s),this.setControls(n),"undefined"!=typeof a.data("load")){var l=a.data("load")+"(modal)",r=new Function("modal",l);try{r(this)}catch(h){console.log("MB Modal Callback Error: "+h.message)}}this.show()},maxModal.prototype.newModal=function(o){null!==this.currentModal&&this.close();var e=t('<div class="max-modal '+o+'" > 						   <div class="modal_header"> 							   <div class="modal_close dashicons dashicons-no"></div><h3 class="modal_title"></h3> 						   </div> 						   <div class="inner modal_content"></div>					   </div>');return t(this.parent).length>0?t(this.parent).append(e):t("body").append(e),t(e).draggable({handle:".modal_header"}),this.modals.push(e),this.currentModal=e,this.controls=[],this},maxModal.prototype.writeOverlay=function(){t(this.parent).append('<div class="maxmodal_overlay"></div>'),t(".maxmodal_overlay").on("click",t.proxy(this.close,this))}});