function randomnum(e, r) { 
	var a = r - e,
    n = Math.random(); 
    return e + Math.round(n * a) 
}

function runzp() { 
	for (var data = '[{"id":1,"prize":"","v":1.0},{"id":2,"prize":"","v":2.0},{"id":3,"prize":"","v":48.0}]', obj = eval("(" + data + ")"), result = randomnum(1, 100), line = 0, temp = 0, returnobj = "0", index = 0, i = 0; i < obj.length; i++) { 
		// var obj2 = obj[i],c = parseFloat(obj2.v); 
		// console.log(result)
  //     	if (temp += c, line = 100 - temp, 0 != c && result > line && result <= line + c) {
  //     		index = i, returnobj = obj2; 
  //     		console.log('--------')
  //     		console.log(index)
  //     		break 
  //     	} 
      	if(result <5){
      		returnobj = "1"
      	}else if(10< result &&result <= 20){
      		returnobj = "2"
      	}else if(60< result &&result <= 80){
      		returnobj = "3"
      	}
  	} 
  	var angle = 330,
    message = "恭喜获得积分",
    myreturn = new Object; 
    if ("0" != returnobj) {
	    var angle0 = [344, 373],angle1 = [226, 256],angle2 = [109, 136]; 
	    console.log(returnobj)
	    switch (returnobj) {
	      case "1":
	        var r0 = randomnum(angle0[0], angle0[1]);
	        angle = r0; 
	         console.log(angle)
	        break;
	      case "2":
	        var r1 = randomnum(angle1[0], angle1[1]);
	        console.log(r1)
	        angle = r1;
	        break;
	      case "3":
	        var r2 = randomnum(angle2[0], angle2[1]);
	        console.log(r2)
	        angle = r2 
	    }
	    myreturn.prize = returnobj.prize 

	} else {
		 message = ""; 
		 var angle3 = [17, 103],angle4 = [197, 220],angle5 = [259, 340],r = randomnum(3, 5),angle; 
		 console.log('r = ' + r)
	      switch (r) {
	      case 3:
	        var r3 = randomnum(angle3[0], angle3[1]);
	        angle = r3; 
	        break;
	      case 4:
	        var r4 = randomnum(angle4[0], angle4[1]);
	        angle = r4; 
	        break;
	      case 5:
	        var r5 = randomnum(angle5[0], angle5[1]);
	        angle = r5 
	    }
	     myreturn.prize = "恭喜获得积分!" 
    } 

    return myreturn.angle = angle, myreturn.message = message, myreturn 
}
