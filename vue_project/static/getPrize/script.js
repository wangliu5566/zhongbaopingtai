function randomnum(smin,smax){var Range=smax-smin;var Rand=Math.random();return(smin+Math.round(Rand*Range))}function runzp(){var data='[{"id":1,"prize":"","v":1.0},{"id":2,"prize":"","v":2.0},{"id":3,"prize":"","v":48.0}]';var obj=eval("("+data+")");var result=randomnum(1,100);var line=0;var temp=0;var returnobj="0";var index=0;for(var i=0;i<obj.length;i++){var obj2=obj[i];var c=parseFloat(obj2.v);temp=temp+c;line=100-temp;if(c!=0){if(result>line&&result<=(line+c)){index=i;returnobj=obj2;break}}}var angle=330;var message="";var myreturn=new Object;if(returnobj!="0"){var angle0=[344,373];var angle1=[226,256];var angle2=[109,136];switch(index){case 0:var r0=randomnum(angle0[0],angle0[1]);angle=r0;break;case 1:var r1=randomnum(angle1[0],angle1[1]);angle=r1;break;case 2:var r2=randomnum(angle2[0],angle2[1]);angle=r2;break}myreturn.prize=returnobj.prize}else{message="";var angle3=[17,103];var angle4=[197,220];var angle5=[259,340];var r=randomnum(3,5);var angle;switch(r){case 3:var r3=randomnum(angle3[0],angle3[1]);angle=r3;break;case 4:var r4=randomnum(angle4[0],angle4[1]);angle=r4;break;case 5:var r5=randomnum(angle5[0],angle5[1]);angle=r5;break}myreturn.prize="恭喜获得积分!"}myreturn.angle=angle;myreturn.message=message;return myreturn};