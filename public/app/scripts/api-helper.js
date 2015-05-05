var API_HELPER = {

	getToken : function(){
		return "token";
	},

	saveCode : function( code ){
		var url = makeURL(code);
		$http.post(url, code).success(function(data, status, headers, config) {
			console.log("Code saved!")
		});
	},

	deleteCode : function( code ){
		var url = makeURL(code);
		$http.delete(url).success(function(data, status, headers, config) {
			console.log("Code deleted!")
		});
	},

	loadCodes : function( _callback, filters ){
		if( !filters ){
			filters = { dc : "DC1" };
		}

		var url = makeGetURL(filters);
		//var url = "app/codes.json";

		$.getJSON(url, function(json, textStatus) {
			console.log(json);
			_callback(json);
		});
	},

	makeGetURL : function( filters ){
		var url = config.API_URL + "/codes/" + getToken() + "/" + filters.dc;
		if( filters.prefix ){
			url = url + "/" + filters.prefix;
		}
		if( filters.filterBy ){
			url = url + "/" + filters.filterBy + "/" + filters.filter;
		}
		return url;
	},

	makeURL : function( code ){
		return config.API_URL + "/codes/" + getToken() + "/" + code.key;
	},

	// Save Token
	saveToken : function(token){

	}

};