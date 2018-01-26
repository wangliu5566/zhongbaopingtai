

!function(e){"undefined"==typeof module?this.charming=e:module.exports=e}(function(e,n){"use strict";n=n||{};var t=n.tagName||"span",o=null!=n.classPrefix?n.classPrefix:"char",r=1,a=function(e){for(var n=e.parentNode,a=e.nodeValue,c=a.length,l=-1;++l<c;){var d=document.createElement(t);o&&(d.className=o+r,r++),d.appendChild(document.createTextNode(a[l])),n.insertBefore(d,e)}n.removeChild(e)};return function c(e){for(var n=[].slice.call(e.childNodes),t=n.length,o=-1;++o<t;)c(n[o]);e.nodeType===Node.TEXT_NODE&&a(e)}(e),e});


(function(u,r){"function"===typeof define&&define.amd?define([],r):"object"===typeof module&&module.exports?module.exports=r():u.anime=r()})(this,function(){var u={duration:1E3,delay:0,loop:!1,autoplay:!0,direction:"normal",easing:"easeOutElastic",elasticity:400,round:!1,begin:void 0,update:void 0,complete:void 0},r="translateX translateY translateZ rotate rotateX rotateY rotateZ scale scaleX scaleY scaleZ skewX skewY".split(" "),y,f={arr:function(a){return Array.isArray(a)},obj:function(a){return-1<
Object.prototype.toString.call(a).indexOf("Object")},svg:function(a){return a instanceof SVGElement},dom:function(a){return a.nodeType||f.svg(a)},num:function(a){return!isNaN(parseInt(a))},str:function(a){return"string"===typeof a},fnc:function(a){return"function"===typeof a},und:function(a){return"undefined"===typeof a},nul:function(a){return"null"===typeof a},hex:function(a){return/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(a)},rgb:function(a){return/^rgb/.test(a)},hsl:function(a){return/^hsl/.test(a)},
col:function(a){return f.hex(a)||f.rgb(a)||f.hsl(a)}},D=function(){var a={},b={Sine:function(a){return 1-Math.cos(a*Math.PI/2)},Circ:function(a){return 1-Math.sqrt(1-a*a)},Elastic:function(a,b){if(0===a||1===a)return a;var d=1-Math.min(b,998)/1E3,g=a/1-1;return-(Math.pow(2,10*g)*Math.sin(2*(g-d/(2*Math.PI)*Math.asin(1))*Math.PI/d))},Back:function(a){return a*a*(3*a-2)},Bounce:function(a){for(var b,d=4;a<((b=Math.pow(2,--d))-1)/11;);return 1/Math.pow(4,3-d)-7.5625*Math.pow((3*b-2)/22-a,2)}};["Quad",
"Cubic","Quart","Quint","Expo"].forEach(function(a,e){b[a]=function(a){return Math.pow(a,e+2)}});Object.keys(b).forEach(function(c){var e=b[c];a["easeIn"+c]=e;a["easeOut"+c]=function(a,b){return 1-e(1-a,b)};a["easeInOut"+c]=function(a,b){return.5>a?e(2*a,b)/2:1-e(-2*a+2,b)/2};a["easeOutIn"+c]=function(a,b){return.5>a?(1-e(1-2*a,b))/2:(e(2*a-1,b)+1)/2}});a.linear=function(a){return a};return a}(),z=function(a){return f.str(a)?a:a+""},E=function(a){return a.replace(/([a-z])([A-Z])/g,"$1-$2").toLowerCase()},
F=function(a){if(f.col(a))return!1;try{return document.querySelectorAll(a)}catch(b){return!1}},A=function(a){return a.reduce(function(a,c){return a.concat(f.arr(c)?A(c):c)},[])},t=function(a){if(f.arr(a))return a;f.str(a)&&(a=F(a)||a);return a instanceof NodeList||a instanceof HTMLCollection?[].slice.call(a):[a]},G=function(a,b){return a.some(function(a){return a===b})},R=function(a,b){var c={};a.forEach(function(a){var d=JSON.stringify(b.map(function(b){return a[b]}));c[d]=c[d]||[];c[d].push(a)});
return Object.keys(c).map(function(a){return c[a]})},H=function(a){return a.filter(function(a,c,e){return e.indexOf(a)===c})},B=function(a){var b={},c;for(c in a)b[c]=a[c];return b},v=function(a,b){for(var c in b)a[c]=f.und(a[c])?b[c]:a[c];return a},S=function(a){a=a.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i,function(a,b,c,m){return b+b+c+c+m+m});var b=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(a);a=parseInt(b[1],16);var c=parseInt(b[2],16),b=parseInt(b[3],16);return"rgb("+a+","+c+","+b+")"},
T=function(a){a=/hsl\((\d+),\s*([\d.]+)%,\s*([\d.]+)%\)/g.exec(a);var b=parseInt(a[1])/360,c=parseInt(a[2])/100,e=parseInt(a[3])/100;a=function(a,b,c){0>c&&(c+=1);1<c&&--c;return c<1/6?a+6*(b-a)*c:.5>c?b:c<2/3?a+(b-a)*(2/3-c)*6:a};if(0==c)c=e=b=e;else var d=.5>e?e*(1+c):e+c-e*c,g=2*e-d,c=a(g,d,b+1/3),e=a(g,d,b),b=a(g,d,b-1/3);return"rgb("+255*c+","+255*e+","+255*b+")"},p=function(a){return/([\+\-]?[0-9|auto\.]+)(%|px|pt|em|rem|in|cm|mm|ex|pc|vw|vh|deg)?/.exec(a)[2]},I=function(a,b,c){return p(b)?
b:-1<a.indexOf("translate")?p(c)?b+p(c):b+"px":-1<a.indexOf("rotate")||-1<a.indexOf("skew")?b+"deg":b},w=function(a,b){if(b in a.style)return getComputedStyle(a).getPropertyValue(E(b))||"0"},U=function(a,b){var c=-1<b.indexOf("scale")?1:0,e=a.style.transform;if(!e)return c;for(var d=/(\w+)\((.+?)\)/g,g=[],m=[],f=[];g=d.exec(e);)m.push(g[1]),f.push(g[2]);e=f.filter(function(a,c){return m[c]===b});return e.length?e[0]:c},J=function(a,b){if(f.dom(a)&&G(r,b))return"transform";if(f.dom(a)&&(a.getAttribute(b)||
f.svg(a)&&a[b]))return"attribute";if(f.dom(a)&&"transform"!==b&&w(a,b))return"css";if(!f.nul(a[b])&&!f.und(a[b]))return"object"},K=function(a,b){switch(J(a,b)){case "transform":return U(a,b);case "css":return w(a,b);case "attribute":return a.getAttribute(b)}return a[b]||0},L=function(a,b,c){if(f.col(b))return b=f.rgb(b)?b:f.hex(b)?S(b):f.hsl(b)?T(b):void 0,b;if(p(b))return b;a=p(a.to)?p(a.to):p(a.from);!a&&c&&(a=p(c));return a?b+a:b},M=function(a){var b=/-?\d*\.?\d+/g;return{original:a,numbers:z(a).match(b)?
z(a).match(b).map(Number):[0],strings:z(a).split(b)}},V=function(a,b,c){return b.reduce(function(b,d,g){d=d?d:c[g-1];return b+a[g-1]+d})},W=function(a){a=a?A(f.arr(a)?a.map(t):t(a)):[];return a.map(function(a,c){return{target:a,id:c}})},N=function(a,b,c,e){"transform"===c?(c=a+"("+I(a,b.from,b.to)+")",b=a+"("+I(a,b.to)+")"):(a="css"===c?w(e,a):void 0,c=L(b,b.from,a),b=L(b,b.to,a));return{from:M(c),to:M(b)}},X=function(a,b){var c=[];a.forEach(function(e,d){var g=e.target;return b.forEach(function(b){var l=
J(g,b.name);if(l){var q;q=b.name;var h=b.value,h=t(f.fnc(h)?h(g,d):h);q={from:1<h.length?h[0]:K(g,q),to:1<h.length?h[1]:h[0]};h=B(b);h.animatables=e;h.type=l;h.from=N(b.name,q,h.type,g).from;h.to=N(b.name,q,h.type,g).to;h.round=f.col(q.from)||h.round?1:0;h.delay=(f.fnc(h.delay)?h.delay(g,d,a.length):h.delay)/k.speed;h.duration=(f.fnc(h.duration)?h.duration(g,d,a.length):h.duration)/k.speed;c.push(h)}})});return c},Y=function(a,b){var c=X(a,b);return R(c,["name","from","to","delay","duration"]).map(function(a){var b=
B(a[0]);b.animatables=a.map(function(a){return a.animatables});b.totalDuration=b.delay+b.duration;return b})},C=function(a,b){a.tweens.forEach(function(c){var e=c.from,d=a.duration-(c.delay+c.duration);c.from=c.to;c.to=e;b&&(c.delay=d)});a.reversed=a.reversed?!1:!0},Z=function(a){if(a.length)return Math.max.apply(Math,a.map(function(a){return a.totalDuration}))},O=function(a){var b=[],c=[];a.tweens.forEach(function(a){if("css"===a.type||"transform"===a.type)b.push("css"===a.type?E(a.name):"transform"),
a.animatables.forEach(function(a){c.push(a.target)})});return{properties:H(b).join(", "),elements:H(c)}},aa=function(a){var b=O(a);b.elements.forEach(function(a){a.style.willChange=b.properties})},ba=function(a){O(a).elements.forEach(function(a){a.style.removeProperty("will-change")})},ca=function(a,b){var c=a.path,e=a.value*b,d=function(d){d=d||0;return c.getPointAtLength(1<b?a.value+d:e+d)},g=d(),f=d(-1),d=d(1);switch(a.name){case "translateX":return g.x;case "translateY":return g.y;case "rotate":return 180*
Math.atan2(d.y-f.y,d.x-f.x)/Math.PI}},da=function(a,b){var c=Math.min(Math.max(b-a.delay,0),a.duration)/a.duration,e=a.to.numbers.map(function(b,e){var f=a.from.numbers[e],l=D[a.easing](c,a.elasticity),f=a.path?ca(a,l):f+l*(b-f);return f=a.round?Math.round(f*a.round)/a.round:f});return V(e,a.to.strings,a.from.strings)},P=function(a,b){var c;a.currentTime=b;a.progress=b/a.duration*100;for(var e=0;e<a.tweens.length;e++){var d=a.tweens[e];d.currentValue=da(d,b);for(var f=d.currentValue,m=0;m<d.animatables.length;m++){var l=
d.animatables[m],k=l.id,l=l.target,h=d.name;switch(d.type){case "css":l.style[h]=f;break;case "attribute":l.setAttribute(h,f);break;case "object":l[h]=f;break;case "transform":c||(c={}),c[k]||(c[k]=[]),c[k].push(f)}}}if(c)for(e in y||(y=(w(document.body,"transform")?"":"-webkit-")+"transform"),c)a.animatables[e].target.style[y]=c[e].join(" ");a.settings.update&&a.settings.update(a)},Q=function(a){var b={};b.animatables=W(a.targets);b.settings=v(a,u);var c=b.settings,e=[],d;for(d in a)if(!u.hasOwnProperty(d)&&
"targets"!==d){var g=f.obj(a[d])?B(a[d]):{value:a[d]};g.name=d;e.push(v(g,c))}b.properties=e;b.tweens=Y(b.animatables,b.properties);b.duration=Z(b.tweens)||a.duration;b.currentTime=0;b.progress=0;b.ended=!1;return b},n=[],x=0,ea=function(){var a=function(){x=requestAnimationFrame(b)},b=function(b){if(n.length){for(var e=0;e<n.length;e++)n[e].tick(b);a()}else cancelAnimationFrame(x),x=0};return a}(),k=function(a){var b=Q(a),c={};b.tick=function(a){b.ended=!1;c.start||(c.start=a);c.current=Math.min(Math.max(c.last+
a-c.start,0),b.duration);P(b,c.current);var d=b.settings;d.begin&&c.current>=d.delay&&(d.begin(b),d.begin=void 0);c.current>=b.duration&&(d.loop?(c.start=a,"alternate"===d.direction&&C(b,!0),f.num(d.loop)&&d.loop--):(b.ended=!0,b.pause(),d.complete&&d.complete(b)),c.last=0)};b.seek=function(a){P(b,a/100*b.duration)};b.pause=function(){ba(b);var a=n.indexOf(b);-1<a&&n.splice(a,1)};b.play=function(a){b.pause();a&&(b=v(Q(v(a,b.settings)),b));c.start=0;c.last=b.ended?0:b.currentTime;a=b.settings;"reverse"===
a.direction&&C(b);"alternate"!==a.direction||a.loop||(a.loop=1);aa(b);n.push(b);x||ea()};b.restart=function(){b.reversed&&C(b);b.pause();b.seek(0);b.play()};b.settings.autoplay&&b.play();return b};k.version="1.1.1";k.speed=1;k.list=n;k.remove=function(a){a=A(f.arr(a)?a.map(t):t(a));for(var b=n.length-1;0<=b;b--)for(var c=n[b],e=c.tweens,d=e.length-1;0<=d;d--)for(var g=e[d].animatables,k=g.length-1;0<=k;k--)G(a,g[k].target)&&(g.splice(k,1),g.length||e.splice(d,1),e.length||c.pause())};k.easings=D;
k.getValue=K;k.path=function(a){a=f.str(a)?F(a)[0]:a;return{path:a,value:a.getTotalLength()}};k.random=function(a,b){return Math.floor(Math.random()*(b-a+1))+a};return k});

(function(window) {

	'use strict';

	function lineEq(y2, y1, x2, x1, currentVal) {
		// y = mx + b
		var m = (y2 - y1) / (x2 - x1),
			b = y1 - m * x1;

		return m * currentVal + b;
	}

	TextFx.prototype.effects = {
		'fx1' : {
			in: {
				duration: 1000,
				delay: function(el, index) { return 75+index*40; },
				easing: 'easeOutElastic',
				elasticity: 650,
				opacity: {
					value: 1,
					easing: 'easeOutExpo',
				},
				translateY: ['50%','0%']
			},
			out: {
				duration: 400,
				delay: function(el, index) { return index*40; },
				easing: 'easeOutExpo',
				opacity: 0,
				translateY: '-100%'
			}
		},
		'fx2' : {
			in: {
				duration: 700,
				delay: function(el, index) { return index*50; },
				easing: 'easeOutCirc',
				opacity: 1,
				translateX: function(el, index) {
					return [(50+index*10),0]
				}
			},
			out: {
				duration: 0,
				opacity: 0
			}
		},
		'fx3' : {
			in: {
				duration: 800,
				delay: function(el, index) { return index*50; },
				easing: 'easeOutElastic',
				opacity: 1,
				translateY: function(el, index) {
					return index%2 === 0 ? ['-80%', '0%'] : ['80%', '0%'];
				}
			},
			out: {
				duration: 800,
				delay: function(el, index) { return index*50; },
				easing: 'easeOutExpo',
				opacity: 0,
				translateY: function(el, index) {
					return index%2 === 0 ? '80%' : '-80%';
				}
			}
		},
		'fx4' : {
			in: {
				duration: 700,
				delay: function(el, index) { return (el.parentNode.children.length-index-1)*80; },
				easing: 'easeOutElastic',
				opacity: 1,
				translateY: function(el, index) {
					return index%2 === 0 ? ['-80%', '0%'] : ['80%', '0%'];
				},
				rotateZ: [90,0]
			},
			out: {
				duration: 500,
				delay: function(el, index) { return (el.parentNode.children.length-index-1)*80; },
				easing: 'easeOutExpo',
				opacity: 0,
				translateY: function(el, index) {
					return index%2 === 0 ? '80%' : '-80%';
				},
				rotateZ: function(el, index) {
					return index%2 === 0 ? -25 : 25;
				}
			}
		},
		'fx5' : {
			perspective: 1000,
			in: {
				duration: 700,
				delay: function(el, index) { return 550+index*50; },
				easing: 'easeOutQuint',
				opacity: {
					value: 1,
					easing: 'linear',
				},
				translateY: ['-150%','0%'],
				rotateY: [180,0]
			},
			out: {
				duration: 700,
				delay: function(el, index) { return index*60; },
				easing: 'easeInQuint',
				opacity: {
					value: 0,
					easing: 'linear',
				},
				translateY: '150%',
				rotateY: -180
			}
		},
		'fx6' : {
			in: {
				duration: 600,
				easing: 'easeOutQuart',
				opacity: 1,
				translateY: function(el, index) {
					return index%2 === 0 ? ['-40%', '0%'] : ['40%', '0%'];
				},
				rotateZ: [10,0]
			},
			out: {
				duration: 0,
				opacity: 0
			}
		},
		'fx7' : {
			in: {
				duration: 250,
				delay: function(el, index) { return 200+index*25; },
				easing: 'easeOutCubic',
				opacity: 1,
				translateY: ['-50%','0%']
			},
			out: {
				duration: 250,
				delay: function(el, index) { return index*25; },
				easing: 'easeOutCubic',
				opacity: 0,
				translateY: '50%'
			}
		},
		'fx8' : {
			in: {
				duration: 400,
				delay: function(el, index) { return 150+(el.parentNode.children.length-index-1)*20; },
				easing: 'easeOutQuad',
				opacity: 1,
				translateY: ['100%','0%']
			},
			out: {
				duration: 400,
				delay: function(el, index) { return (el.parentNode.children.length-index-1)*20; },
				easing: 'easeInOutQuad',
				opacity: 0,
				translateY: '-100%'
			}
		},
		'fx9' : {
			perspective: 1000,
			origin: '50% 100%',
			in: {
				duration: 400,
				delay: function(el, index) { return index*50; },
				easing: 'easeOutSine',
				opacity: 1,
				rotateY: [-90,0]
			},
			out: {
				duration: 200,
				delay: function(el, index) { return index*50; },
				easing: 'easeOutSine',
				opacity: 0,
				rotateY: 45
			}
		},
		'fx10' : {
			in: {
				duration: 1000,
				delay: function(el, index) { return 100+index*30; },
				easing: 'easeOutElastic',
				elasticity: anime.random(400, 700),
				opacity: 1,
				rotateZ: function(el, index) {
					return [anime.random(20,40),0];
				}
			},
			out: {
				duration: 0,
				opacity: 0
			}
		},
		'fx11' : {
			perspective: 1000,
			origin: '50% 100%',
			in: {
				duration: 400,
				delay: function(el, index) { return 200+index*20; },
				easing: 'easeOutExpo',
				opacity: 1,
				rotateY: [-90,0]
			},
			out: {
				duration: 400,
				delay: function(el, index) { return index*20; },
				easing: 'easeOutExpo',
				opacity: 0,
				rotateY: 90
			}
		},
		'fx12' : {
			perspective: 1000,
			origin: '50% 100%',
			in: {
				duration: 400,
				delay: function(el, index) { return 200+index*30; },
				easing: 'easeOutExpo',
				opacity: 1,
				rotateX: [90,0]
			},
			out: {
				duration: 400,
				delay: function(el, index) { return index*30; },
				easing: 'easeOutExpo',
				opacity: 0,
				rotateX: -90
			}
		},
		'fx13' : {
			in: {
				duration: 800,
				easing: 'easeOutExpo',
				opacity: 1,
				translateY: function(el, index) {
					var p = el.parentNode,
						lastElOffW = p.lastElementChild.offsetWidth,
						firstElOffL = p.firstElementChild.offsetLeft,
						w = p.offsetWidth - lastElOffW - firstElOffL - (p.offsetWidth - lastElOffW - p.lastElementChild.offsetLeft),
						tyVal = lineEq(0, 200, firstElOffL + w/2, firstElOffL, el.offsetLeft);

					return [Math.abs(tyVal)+50+'%','0%'];
				},
				rotateZ: function(el, index) {
					var p = el.parentNode,
						lastElOffW = p.lastElementChild.offsetWidth,
						firstElOffL = p.firstElementChild.offsetLeft,
						w = p.offsetWidth - lastElOffW - p.firstElementChild.offsetLeft - (p.offsetWidth - lastElOffW - p.lastElementChild.offsetLeft),
						rz = lineEq(90, -90,firstElOffL + w, firstElOffL, el.offsetLeft);

					return [rz,0];
				}
			},
			out: {
				duration: 500,
				easing: 'easeOutExpo',
				opacity: 0,
				translateY: '-150%'
			}
		},
		'fx14' : {
			in: {
				duration: 500,
				easing: 'easeOutExpo',
				delay: function(el, index) { return 200+index*30; },
				opacity: 1,
				rotateZ: [20,0],
				translateY: function(el, index) {
					var p = el.parentNode,
						lastElOffW = p.lastElementChild.offsetWidth,
						firstElOffL = p.firstElementChild.offsetLeft,
						w = p.offsetWidth - lastElOffW - firstElOffL - (p.offsetWidth - lastElOffW - p.lastElementChild.offsetLeft),
						tyVal = lineEq(-130, -60, firstElOffL+w, firstElOffL, el.offsetLeft);

					return [Math.abs(tyVal)+'%','0%'];
				}
			},
			out: {
				duration: 400,
				easing: 'easeOutExpo',
				delay: function(el, index) { return (el.parentNode.children.length-index-1)*30; },
				opacity: 0,
				rotateZ: 20,
				translateY: function(el, index) {
					var p = el.parentNode,
						lastElOffW = p.lastElementChild.offsetWidth,
						firstElOffL = p.firstElementChild.offsetLeft,
						w = p.offsetWidth - lastElOffW - firstElOffL - (p.offsetWidth - lastElOffW - p.lastElementChild.offsetLeft),
						tyVal = lineEq(-60, -130, firstElOffL+w, firstElOffL, el.offsetLeft);

					return tyVal+'%';
				}
			}
		},
		'fx15' : {
			perspective: 1000,
			in: {
				duration: 400,
				delay: function(el, index) { return 100+index*50; },
				easing: 'easeOutExpo',
				opacity: 1,
				rotateX: [110,0]
			},
			out: {
				duration: 400,
				delay: function(el, index) { return index*50; },
				easing: 'easeOutExpo',
				opacity: 0,
				rotateX: -110
			}
		},
		'fx16' : {
			in: {
				duration: function(el, index) { return anime.random(800,1000) },
				delay: function(el, index) { return anime.random(0,75) },
				easing: 'easeInOutExpo',
				opacity: 1,
				translateY: ['-300%','0%'],
				rotateZ: function(el, index) { return [anime.random(-50,50), 0]; }
			},
			out: {
				duration: function(el, index) { return anime.random(800,1000) },
				delay: function(el, index) { return anime.random(0,80) },
				easing: 'easeInOutExpo',
				opacity: 0,
				translateY: '300%',
				rotateZ: function(el, index) { return anime.random(-50,50); }
			}
		},
		'fx17' : {
			in: {
				duration: 650,
				easing: 'easeOutQuint',
				delay: function(el, index) { return 450+(el.parentNode.children.length-index-1)*30; },
				opacity: 1,
				translateX: function(el, index) {
					return [-1*el.offsetLeft,0];
				}
			},
			out: {
				duration: 1,
				delay: 400,
				opacity: 0
			}
		},
		'fx18' : {
			in: {
				duration: 800,
				delay: function(el, index) { return 600+index*150; },
				easing: 'easeInOutQuint',
				opacity: 1,
				scaleY: [8,1],
				scaleX: [0.5,1],
				translateY: ['-100%','0%']
			},
			out: {
				duration: 800,
				delay: function(el, index) { return index*150; },
				easing: 'easeInQuint',
				opacity: 0,
				scaleY: {
					value: 8,
					delay: function(el, index) { return 100+index*150; },
				},
				scaleX: 0.5,
				translateY: '100%'
			}
		}
	};

	TextFx.prototype._init2 = function() {
		// Split word(s) into letters/spans.
		charming(this.el, {classPrefix: 'letter'});
		this.letters = [].slice.call(this.el.querySelectorAll('span'));
		this.lettersTotal = this.letters.length;
	};

	function TextFx(el) {
		this.el = el;
		this._init2();
	}

	TextFx.prototype.show = function(effect, callback) {
		arguments.length ? this._animate('in', effect, callback) : this.letters.forEach(function(letter) { letter.style.opacity = 1; });
	};

	TextFx.prototype.hide = function(effect, callback) {
		if( arguments.length ) {
			this._animate('out', effect, callback);
		}
		else {
			anime.remove(this.letters);
			this.letters.forEach(function(letter) { letter.style.opacity = 0; })
		}
	};

	TextFx.prototype._animate = function(direction, effect, callback) {
		var effecSettings = typeof effect === 'string' ? this.effects[effect] : effect;

		if( effecSettings.perspective != undefined ) {
			this.el.style.WebkitPerspective = this.el.style.perspective = effecSettings.perspective + 'px';
		}
		if( effecSettings.origin != undefined ) {
			this.letters.forEach(function(letter) { 
				letter.style.WebkitTransformOrigin = letter.style.transformOrigin = effecSettings.origin;
			});
		}

		// Custom effect
		var iscustom = this._checkCustomFx(direction, effect, callback);

		var animOpts = effecSettings[direction];
		animOpts.targets = this.letters;
		
		if( !iscustom ) {
			animOpts.complete = callback;
		}

		anime(animOpts);
	};

	/**
	 * Extra stuff for an effect.. this is just an example for effect 17.
	 * TODO! (for now, just some quick way to implement something different only for fx17)
	 */
	TextFx.prototype._checkCustomFx = function(direction, effect, callback) {
		var custom = typeof effect === 'string' && effect === 'fx17' && direction === 'out';
		
		if( custom ) {
			var tmp = document.createElement('div');
			tmp.style.width = tmp.style.height = '100%';
			tmp.style.top = tmp.style.left = 0;
			tmp.style.background = '#e24b1e';
			tmp.style.position = 'absolute';
			tmp.style.WebkitTransform = tmp.style.transform = 'scale3d(0,1,1)';
			tmp.style.WebkitTransformOrigin = tmp.style.transformOrigin = '0% 50%';
			this.el.appendChild(tmp);
			var self = this;
			anime({
				targets: tmp,
				duration: 400,
				easing: 'easeInOutCubic',
				scaleX: [0,1],
				complete: function() {
					tmp.style.WebkitTransformOrigin = tmp.style.transformOrigin = '100% 50%';
					anime({
						targets: tmp,
						duration: 400,
						easing: 'easeInOutCubic',
						scaleX: [1,0],
						complete: function() {
							self.el.removeChild(tmp);
							if( typeof callback === 'function' ) {
								callback();
							}
						}
					});
				}
			});
		}

		return custom;
	};

	window.TextFx = TextFx;

 // ----------------------------------------------------------------------------------------------------------------

	// Helper vars and functions.
	function extend( a, b ) {
		for( var key in b ) { 
			if( b.hasOwnProperty( key ) ) {
				a[key] = b[key];
			}
		}
		return a;
	}
	// From http://www.quirksmode.org/js/events_properties.html#position
	function getMousePos(e) {
		var posx = 0;
		var posy = 0;
		if (!e) var e = window.event;
		if (e.pageX || e.pageY) 	{
			posx = e.pageX;
			posy = e.pageY;
		}
		else if (e.clientX || e.clientY) 	{
			posx = e.clientX + document.body.scrollLeft
				+ document.documentElement.scrollLeft;
			posy = e.clientY + document.body.scrollTop
				+ document.documentElement.scrollTop;
		}
		return {
			x : posx,
			y : posy
		}
	}
	// Detect mobile. From: http://stackoverflow.com/a/11381730/989439
	function mobilecheck() {
		var check = false;
		(function(a){if(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
		return check;
	}

	// The Day obj.
	function Day(options) {
		this.options = extend({}, this.options);
		extend(this.options, options);
		this.number = this.options.number;
		this.color = this.options.color;
		this.previewTitle = this.options.previewTitle;
		this.isActive = !this.options.inactive;
		this._layout();
	}

	Day.prototype.options = {
		number: 0,
		color: '#f1f1f1',
		previewTitle: '',
		inactive: false
	};

	// Build the 3D cube element.
	Day.prototype._layout = function() {
		this.cube = document.createElement('div');
		this.cube.className = this.isActive ? 'cube' : 'cube cube--inactive';
		this.cube.innerHTML = '<div class="cube__side cube__side--back"></div><div class="cube__side cube__side--left"></div><div class="cube__side cube__side--right"></div><div class="cube__side cube__side--bottom"></div><div class="cube__side cube__side--top"></div><div class="cube__side cube__side--front"><img src="/static/animation/img/mandala.png" class="myShowImgs" /><p class="title-p"></p></div>';
		this.currentTransform = {translateZ: 0, rotateX: 0, rotateY: 0};
	};

	Day.prototype._rotate = function(ev) {
		anime.remove(this.cube);

		var dir = this._getDirection(ev),
			type = ev.type.toLowerCase() === 'mouseenter' ? 1 : -1,
			animationSettings = {
				targets: this.cube,
				duration: 500,
				easing: 'easeOutExpo'
			};

		animationSettings.translateZ = {
			value: type === 1 ? 90 : 0,
			duration: 900,
			easing: 'easeOutExpo'
		};

		switch(dir) {
			case 0 : // from/to top
				animationSettings.rotateX = type === 1 ? -180 : 0; 
				animationSettings.rotateY = 0;
				break; 
			case 1 : // from/to right
				animationSettings.rotateY = type === 1 ? -180 : 0; 
				animationSettings.rotateX = 0;
				break; 
			case 2 : // from/to bottom
				animationSettings.rotateX = type === 1 ? 180 : 0; 
				animationSettings.rotateY = 0;
				break; 
			case 3 : // from/to left
				animationSettings.rotateY = type === 1 ? 180 : 0; 
				animationSettings.rotateX = 0;
				break;
		};

		this.currentTransform = {
			translateZ: animationSettings.translateZ, 
			rotateX: animationSettings.rotateX, 
			rotateY: animationSettings.rotateY
		};

		anime(animationSettings);
	};

	Day.prototype._setContentTitleFx = function(contentTitleEl) {
		this.titlefx = new TextFx(contentTitleEl);
		this.titlefxSettings = {
			in: {
				duration: 800,
				delay: function(el, index) { return 650 + index*10; },
				easing: 'easeOutExpo',
				opacity: {
					duration: 200,
					value: [0,1],
					easing:'linear'
				},
				translateY: [100,0],
				rotateZ: function(el, index) { return [anime.random(-20,20), 0]; }
			},
			out: {
				duration: 800,
				delay: 400,
				easing: 'easeInExpo',
				opacity: 0,
				translateY: 350
			}
		};
	};

	// From: https://codepen.io/noeldelgado/pen/pGwFx?editors=0110 by Noel Delgado (@noeldelgado).
	Day.prototype._getDirection = function(ev) {
		var obj = this.cube.querySelector('.cube__side--front'),
			w = obj.offsetWidth, 
			h = obj.offsetHeight,
			bcr = obj.getBoundingClientRect(),
			x = (ev.pageX - (bcr.left + window.pageXOffset) - (w / 2) * (w > h ? (h / w) : 1)),
			y = (ev.pageY - (bcr.top + window.pageYOffset) - (h / 2) * (h > w ? (w / h) : 1)),
			d = Math.round( Math.atan2(y, x) / 1.57079633 + 5 ) % 4;

		return d;
	};

	// The Calendar obj.
	function Calendar(el) {
		this.el = el;
		this.calendarDays = [].slice.call(this.el.querySelectorAll('.cube'));
		
		// Let´s build the days/cubes structure.
		this.cubes = document.createElement('div');
		this.cubes.className = 'cubes';
		this.el.appendChild(this.cubes);

		// Array of days (each day is represented by a 3D Cube)
		this.days = [];
		var self = this;
		this.calendarDays.forEach(function(d, pos) {
			// Get the bg color defined in the data-bg-color of each division.
			var day = new Day({
					number: pos,
					color: d.getAttribute('data-bg-color') || '#f1f1f1',
					previewTitle: d.getAttribute('data-title') || '',
					inactive: d.hasAttribute('data-inactive')
				}),
				content = contents[pos];

			if( content !== undefined ) {
				var contentTitle = contents[pos].querySelector('.content__title');
				day._setContentTitleFx(contentTitle);
			}

			self.days.push(day);
			self.cubes.appendChild(day.cube);
			self.el.removeChild(d);
			self._initDayEvents(day);
		});

		// structure to show each day preview (day + title + subtitle etc, when the user hovers one day/cube).
		this.dayPreview = document.createElement('h2');
		this.dayPreview.className = 'title';
		this.el.appendChild(this.dayPreview);
		
		this._initEvents();
	}

	Calendar.prototype._initEvents = function() {
		var self = this;

		// Mousemove event / tilt functionality
		if( settings.tilt ) {
			this.mousemoveFn = function(ev) {
				if( self.isOpen ) {
					return false;
				};
				requestAnimationFrame(function() {
					// Mouse position relative to the document.
					var mousepos = getMousePos(ev);
					self._rotateCalendar(mousepos);
				});
			};
			
			this.handleOrientation = function() {
				if( self.isOpen ) {
					return false;
				};

				var y = event.gamma;
				// To make computation easier we shift the range of x and y to [0,180]
				y += 90;
				requestAnimationFrame(function() {
					// Transform values.
					var movement = {ry:40},
						rotY = 2*movement.ry / 180 * y - movement.ry;

					self.cubes.style.WebkitTransform = self.cubes.style.transform = 'rotate3d(0,-1,0,' + rotY + 'deg)';
				});
			};
			if( isMobile ) {
				window.addEventListener('deviceorientation', this.handleOrientation);
			}
			else {
				document.addEventListener('mousemove', this.mousemoveFn);
			}
		}
	};

	Calendar.prototype._initDayEvents = function(day) {
		var self = this;

		// Day/Cube mouseenter/mouseleave event.
		var instance = day;
		if( !isMobile ) {
			instance.mouseenterFn = function(ev) {
				if( instance.isRotated || self.isOpen ) {
					return false;
				};
				clearTimeout(colortimeout);
				instance.rotatetimeout = setTimeout(function() {
					colortimeout = setTimeout(function() { self._changeBGColor(instance.color); }, 30);
					instance._rotate(ev);
					self._showPreviewTitle(instance.previewTitle, instance.number);
					instance.isRotated = true;
				}, 30);
			};
			instance.mouseleaveFn = function(ev) {
				if( self.isOpen ) {
					return false;
				};
				clearTimeout(instance.rotatetimeout);
				clearTimeout(colortimeout);
				if( instance.isRotated ) {
					colortimeout = setTimeout(function() { self._resetBGColor(); }, 35);
					self._resetBGColor();
					instance._rotate(ev);
					self._hidePreviewTitle();
					instance.isRotated = false;
				}
			};
		}
		// Day/Cube click event.
		instance.clickFn = function(ev) {
			var id = this.getAttribute('activityId'),type = this.getAttribute('type')
			if(type ==1){  //大赛
				window.location.href="/frontPage/initDetail?activityId="+id+"&type="+type+"&status="+1
			}else if(type ==2){  //问卷
				window.location.href="/frontPage/initQuestion?questionId="+id+"&type="+type+"&status="+1
			}
		};
		instance.cube.querySelector('.cube__side--front').addEventListener('mouseenter', instance.mouseenterFn);
		instance.cube.addEventListener('mouseleave', instance.mouseleaveFn);
		instance.cube.addEventListener('click', instance.clickFn);
		instance.cube.addEventListener('mousedown', function() {
			clearTimeout(instance.rotatetimeout );
		});
	};

	Calendar.prototype._rotateCalendar = function(mousepos) {
		// Transform values.
		var movement = {rx:3, ry:3},
			rotX = 2 * movement.rx / this.cubes.offsetHeight * mousepos.y - movement.rx,
			rotY = 2 * movement.ry / this.cubes.offsetWidth * mousepos.x - movement.ry;
		
		this.cubes.style.WebkitTransform = this.cubes.style.transform = 'rotate3d(-1,0,0,' + rotX + 'deg) rotate3d(0,1,0,' + rotY + 'deg)';
	};

	Calendar.prototype._showPreviewTitle = function(text, number) {
		this.dayPreview.innerHTML = text;
		this.dayPreview.setAttribute('data-number', parseInt(number+1));
		
		this.txtfx = new TextFx(this.dayPreview);
		this.txtfx.hide();
		this.dayPreview.style.opacity = 0;
		this.txtfx.show({
			in: {
				duration: 800,
				delay: function(el, index) { return index*20; },
				easing: 'easeOutElastic',
				opacity: 1,
				translateY: function(el, index) {
					return index%2 === 0 ? [-25, 0] : [25, 0];
				}
			}
		});
	};

	Calendar.prototype._hidePreviewTitle = function() {
		var self = this;
		if( this.txtfx ) {
			this.txtfx.hide();
		}
		this.dayPreview.style.opacity = 0;
	};

	Calendar.prototype._showContent = function(day) {
		// The content box for the clicked day.
		var content = contents[this.currentDayIdx],
			title = content.querySelector('.content__title'),
			description = content.querySelector('.content__description'),
			meta = content.querySelector('.content__meta');

		content.classList.add('content__block--current');

		day.titlefx.hide();
		day.titlefx.show(day.titlefxSettings);	
		
		anime({
			targets: [description, meta],
			duration: 800,
			delay: function(el, index) { return index === 0 ? 900 : 1000 },
			easing: 'easeOutExpo',
			opacity: [0,1],
			translateY: [100,0]
		});

		anime({
			targets: backCtrl,
			duration: 1000,
			delay: 800,
			easing: 'easeOutExpo',
			opacity: [0,1],
			translateY: [50,0]
		});

		contentNumber.innerHTML = this.currentDayIdx + 1;
		anime({
			targets: contentNumber,
			duration: 500,
			delay: 800,
			easing: 'easeOutExpo',
			opacity: [0,1],
			translateX: [100,0]
		});
	};

	Calendar.prototype._hideContent = function() {
		var day = this.days[this.currentDayIdx],
			// The content box for the clicked day.
			content = contents[this.currentDayIdx],
			// The content title, description and meta for this day.
			title = content.querySelector('.content__title'),
			description = content.querySelector('.content__description'),
			meta = content.querySelector('.content__meta');

		// The content number placeholder animation.
		anime({
			targets: contentNumber,
			duration: 500,
			easing: 'easeInExpo',
			opacity: 0,
			translateX: 100
		});

		// The back button animation.
		anime({
			targets: backCtrl,
			duration: 1000,
			easing: 'easeInExpo',
			opacity: 0,
			translateY: 50
		});

		// The description and meta animation.
		anime({
			targets: [description, meta],
			duration: 800,
			delay: function(el, index) { return index === 0 ? 150 : 0 },
			easing: 'easeInExpo',
			opacity: [1,0],
			translateY: [0,200]
		});

		// The content title animation.
		day.titlefx.hide(day.titlefxSettings, function() {
			content.classList.remove('content__block--current');
		});
	};

	Calendar.prototype._changeBGColor = function(color) {
		bgEl.style.backgroundColor = color;
	};

	Calendar.prototype._resetBGColor = function() {
		bgEl.style.backgroundColor = defaultBgColor;
	};

	// Snow obj. Based on // https://gist.github.com/OmShiv/4368164.
	function Snow() {
		this.canvas = document.createElement('canvas');
		this.canvas.id = 'snow';
		this.canvas.className = 'background';
		this.canvas.style.background = defaultBgColor;
		document.body.insertBefore(this.canvas, document.body.firstElementChild);

		this.flakes = [];
		this.ctx = this.canvas.getContext('2d');
		this.flakeCount = 300;
		this.mX = -100,
		this.mY = -100

		this.width = this.canvas.width = window.innerWidth;
		this.height = this.canvas.height = window.innerHeight;

		this._init1();
	}

	Snow.prototype._init1 = function() {
		var self = this;
		window.addEventListener('resize', function() {
			self.width = self.canvas.width = window.innerWidth;
			self.height = self.canvas.height = window.innerHeight;
		});

		for(var i = 0; i < this.flakeCount; ++i) {
			var x = Math.floor(Math.random() * this.width),
				y = Math.floor(Math.random() * this.height),
				size = (Math.random()*3.5) + .5,
				speed = size*.5,
				opacity = (Math.random() * 0.5) + 0.1;

			this.flakes.push({
				speed: speed,
				velY: speed,
				velX: 0,
				x: x,
				y: y,
				size: size,
				stepSize: (Math.random()) / 30,
				step: 0,
				opacity: opacity
			});
		}

		this._snow();
	};

	Snow.prototype._snow = function() {
		this.ctx.clearRect(0, 0, this.width, this.height);

		for(var i = 0; i < this.flakeCount; ++i) {
			var flake = this.flakes[i],
				x = this.mX,
				y = this.mY,
				minDist = 150,
				x2 = flake.x,
				y2 = flake.y,
				dist = Math.sqrt((x2 - x) * (x2 - x) + (y2 - y) * (y2 - y)),
				dx = x2 - x,
				dy = y2 - y;

			if( dist < minDist ) {
				var force = minDist / (dist * dist),
				xcomp = (x - x2) / dist,
				ycomp = (y - y2) / dist,
				deltaV = force / 2;
				flake.velX -= deltaV * xcomp;
				flake.velY -= deltaV * ycomp;
			}
			else {
				flake.velX *= .98;
				if( flake.velY <= flake.speed ) {
					flake.velY = flake.speed
				}
				flake.velX += Math.cos(flake.step += .05) * flake.stepSize;
			}

			this.ctx.fillStyle = "rgba(255,255,255," + flake.opacity + ")";
			flake.y += flake.velY;
			flake.x += flake.velX;

			if( flake.y >= this.height || flake.y <= 0 ) {
				this._reset(flake);
			}

			if( flake.x >= this.width || flake.x <= 0 ) {
				this._reset(flake);
			}

			this.ctx.beginPath();
			this.ctx.arc(flake.x, flake.y, flake.size, 0, Math.PI * 2);
			this.ctx.fill();
		}
		requestAnimationFrame(this._snow.bind(this));
	};

	Snow.prototype._reset = function(flake) {
		flake.x = Math.floor(Math.random() * this.width);
		flake.y = 0;
		flake.size = (Math.random() * 3.5) + .5;
		flake.speed = flake.size*.5,
		flake.velY = flake.speed;
		flake.velX = 0;
		flake.opacity = (Math.random() * 0.5) + 0.1;
	};


	var calendarEl = document.querySelector('.calendar'),
		calendarDays = calendarEl.querySelectorAll('.cube'),
		settings = {
			snow: false,
			tilt: false,
		},
		bgEl = document.querySelector('.js'),
		defaultBgColor = bgEl.style.backgroundColor,
		colortimeout,
		contentEl = document.querySelector('.content'),
		contents = contentEl.querySelectorAll('.content__block'),
		backCtrl = contentEl.querySelector('.btn-back'),
		contentNumber = contentEl.querySelector('.content__number'),
		isMobile = mobilecheck();


	function layout() {
		new Calendar(calendarEl);
		// If settings.snow === true then create the canvas element for the snow effect.
		if( settings.snow ) {
			var snow = new Snow();
			bgEl = snow.canvas;
		}
	}

	layout();

	
})(window);