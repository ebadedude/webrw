
getURL = function(urlName,funName)
	{
    if (!funName){funName='getURL.console'};
    if (typeof(funName)!=='string'){ // funName is a function, name it:
		var tempFunName='fun_'+Math.random().toString().slice(2);
		getURL[tempFunName]=funName;
		funName='getURL.'+tempFunName;
		//throw('it has to be a function name, not a function itself')
	}
	//createElement(urlName);
	jQuery.getScript("http://sandbox1.mathbiol.org/geturl.php?url=" + urlName +"&callback="+funName);
	return false;
	}

getURL.console = function(x) 
	{ 
	console.log(x); 
	}
