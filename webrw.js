// webrw.js is a javascript library to operate webrw.php
// to play with it directly in the console do
// s=document.createElement('script');s.src='http://localhost:8888/webrw/webrw.js';document.body.appendChild(s);

//if (!webrw){webrw={}} // so that its doesn't destroy pre-existing webrw object
webrw={};
// Utils - this part is just to facilitate content

webrw.stringify = function(x){ // stringify both values and functions
	var y=typeof(x);
	switch (y){
		case 'function':
		y=x.toString();
		break;		
		case 'object':
		if (Array.isArray(x)){
			var y='[';
			for(var i=0;i<x.length;i=i+1){
				y=y+this.stringify(x[i])+','
			}
			y=y.slice(0,y.length-1)+']';
		}
		else{
			y='{';
			for(var v in x){
				y=y+v+':'+this.stringify(x[v])+',';
			}
			y=y.slice(0,y.length-1)+'}';
		}
		break;
		case 'string':
		y=JSON.stringify(x);
		break;
		case 'number':
		y=JSON.stringify(x);
		default:
	}
	return y;
}

webrw.parse = function(x){
	eval('var res='+x);
	return res;
}

// Actual API starts here

webrw.uid=function(prefix){
	if(!prefix){prefix='UID'}
	var uid=prefix+Math.random().toString().slice(2);
	return uid
}

webrw.parseUrl=function(url){ // parsing url and its arguments out
	var u = {};
	u.url=url.match(/[htf]+tp[s]*:\/\/[^?]+/);
	if (u.url.length!==1){throw ('something is wrong with the syntax this url: '+url)}
	else{u.url=u.url[0]}
	return u
}

webrw.jobs={}; // jobs will be queued here

webrw.call=function(q,callback){ // so everything is to be specified through q
	if (!callback){callback=function(x){console.log(x);return false}}
	switch (typeof(q)){
		case 'string':
		// parse string into structure and call again
		break;

		case 'object':
		// is this a GET or a SET?
		if (!q.set){ // it is a GET
			// is this about a token ?
			var tk = q.get.match(/\/webrw[0-9]+/);
			if(!tk){q.url=q.get}else{q.url=q.get+'somethingMissingHere'}
		}
		else { //it is a SET
			if (!q.url){q.url='http://sandbox1.mathbiol.org/'}; // later make this try localhost/webrw/ first
			if (!!q.set){
				//webrw.jsonp();
				var action='kvwrite';
			}
		}
		var uid = webrw.uid();
		webrw.jobs[uid]={fun:callback};

		url=q.url;
		if(url[url.length-1]!=='/'){url=url+'/'} // make sure url ends in '/'
		url=url+'webrw.php?action='+action+'&value='+q.set+'&callback=webrw.jobs.'+uid+'.fun';
		s=document.createElement('script');
		s.id = uid;s.src=url;
		document.body.appendChild(s);
		setTimeout('document.body.removeChild(document.getElementById("'+uid+'"));delete webrw.jobs.'+uid+';',10000); // is the waiting still needed
		break;
	}

	return q;
}


