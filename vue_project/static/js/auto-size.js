!(function(c,b){function d(){var f=window.innerWidth;b.documentElement.style.fontSize=(f/750)*200+"px"}var a="onorientationchange" in c?"orientationchange":"resize";var e=null;c.addEventListener(a,function(){clearTimeout(e);e=setTimeout(d,300)},false);c.addEventListener("pageshow",function(f){if(f.persisted){clearTimeout(e);e=setTimeout(d,300)}},false);d()}(window,document));