!function(D){D.fn.extend({slimScroll:function(E){var R=D.extend({width:"auto",height:"250px",size:"6px",color:"#666",position:"right",distance:"1px",start:"top",opacity:.4,alwaysVisible:!0,disableFadeOut:!1,railVisible:!0,railColor:"#ccc",railOpacity:.2,railDraggable:!0,railClass:"slimScrollRail",barClass:"slimScrollBar",wrapperClass:"slimScrollDiv",allowPageScroll:!1,wheelStep:20,touchScrollStep:200,borderRadius:"7px",railBorderRadius:"7px"},E);return this.each(function(){var s,e,o,i,a,r,l,n,c="<div></div>",u=30,h=!1,d=D(this);if(d.parent().hasClass(R.wrapperClass)){var p=d.scrollTop();if(m=d.siblings("."+R.barClass),v=d.siblings("."+R.railClass),C(),D.isPlainObject(E)){var g=E.height;if(d.parent().css("max-height",g),d.css("max-height",g),"scrollTo"in E)p=parseInt(R.scrollTo);else if("scrollBy"in E)p+=parseInt(R.scrollBy);else if("destroy"in E)return m.remove(),v.remove(),void d.unwrap();y(p,!1,!0)}}else if(!(D.isPlainObject(E)&&"destroy"in E)){R.height="auto"==R.height?d.parent().height():R.height;var f=D(c).addClass(R.wrapperClass).css({position:"relative",overflow:"hidden",width:R.width,"max-height":R.height});d.css({overflow:"hidden",width:R.width,"max-height":R.height});var b,v=D(c).addClass(R.railClass).css({width:R.size,height:"100%",position:"absolute",top:0,display:R.alwaysVisible&&R.railVisible?"block":"none","border-radius":R.railBorderRadius,background:R.railColor,opacity:R.railOpacity,zIndex:90}),m=D(c).addClass(R.barClass).css({background:R.color,width:R.size,position:"absolute",top:0,opacity:R.opacity,display:R.alwaysVisible?"block":"none","border-radius":R.borderRadius,BorderRadius:R.borderRadius,MozBorderRadius:R.borderRadius,WebkitBorderRadius:R.borderRadius,zIndex:99}),w="right"==R.position?{right:R.distance}:{left:R.distance};v.css(w),m.css(w),d.wrap(f),d.parent().append(m),d.parent().append(v),R.railDraggable&&m.bind("mousedown",function(e){var i=D(document);return o=!0,t=parseFloat(m.css("top")),pageY=e.pageY,i.bind("mousemove.slimscroll",function(e){currTop=t+e.pageY-pageY,m.css("top",currTop),y(0,m.position().top,!1)}),i.bind("mouseup.slimscroll",function(e){o=!1,S(),i.unbind(".slimscroll")}),!1}).bind("selectstart.slimscroll",function(e){return e.stopPropagation(),e.preventDefault(),!1}),v.hover(function(){H()},function(){S()}),m.hover(function(){e=!0},function(){e=!1}),d.hover(function(){s=!0,H(),S()},function(){s=!1,S()}),d.bind("touchstart",function(e,t){e.originalEvent.touches.length&&(a=e.originalEvent.touches[0].pageY)}),d.bind("touchmove",function(e){(h||e.originalEvent.preventDefault(),e.originalEvent.touches.length)&&(y((a-e.originalEvent.touches[0].pageY)/R.touchScrollStep,!0),a=e.originalEvent.touches[0].pageY)}),C(),"bottom"===R.start?(m.css({top:d.outerHeight()-m.outerHeight()}),y(0,!0)):"top"!==R.start&&(y(D(R.start).position().top,null,!0),R.alwaysVisible||m.hide()),b=this,window.addEventListener?(b.addEventListener("DOMMouseScroll",x,!1),b.addEventListener("mousewheel",x,!1)):document.attachEvent("onmousewheel",x)}function x(e){if(s){var t=0;(e=e||window.event).wheelDelta&&(t=-e.wheelDelta/120),e.detail&&(t=e.detail/3);var i=e.target||e.srcTarget||e.srcElement;D(i).closest("."+R.wrapperClass).is(d.parent())&&y(t,!0),e.preventDefault&&!h&&e.preventDefault(),h||(e.returnValue=!1)}}function y(e,t,i){h=!1;var s=e,o=d.outerHeight()-m.outerHeight();if(t&&(s=parseInt(m.css("top"))+e*parseInt(R.wheelStep)/100*m.outerHeight(),s=Math.min(Math.max(s,0),o),s=0<e?Math.ceil(s):Math.floor(s),m.css({top:s+"px"})),s=(l=parseInt(m.css("top"))/(d.outerHeight()-m.outerHeight()))*(d[0].scrollHeight-d.outerHeight()),i){var a=(s=e)/d[0].scrollHeight*d.outerHeight();a=Math.min(Math.max(a,0),o),m.css({top:a+"px"})}d.scrollTop(s),d.trigger("slimscrolling",~~s),H(),S()}function C(){r=Math.max(d.outerHeight()/d[0].scrollHeight*d.outerHeight(),u),m.css({height:r+"px"});var e=r==d.outerHeight()?"none":"block";m.css({display:e})}function H(){if(C(),clearTimeout(i),l==~~l){if(h=R.allowPageScroll,n!=l){var e=0==~~l?"top":"bottom";d.trigger("slimscroll",e)}}else h=!1;n=l,r>=d.outerHeight()?h=!0:(m.stop(!0,!0).fadeIn("fast"),R.railVisible&&v.stop(!0,!0).fadeIn("fast"))}function S(){R.alwaysVisible||(i=setTimeout(function(){R.disableFadeOut&&s||e||o||(m.fadeOut("slow"),v.fadeOut("slow"))},1e3))}}),this}}),D.fn.extend({slimscroll:D.fn.slimScroll})}(jQuery);