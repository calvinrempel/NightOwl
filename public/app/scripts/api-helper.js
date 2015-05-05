var API_HELPER = {

	getToken : function(){
		return "token";
	},

	saveCode : function( code ){
		var url = this.makeURL(code);
		$http.post(url, code).success(function(data, status, headers, config) {
			console.log("Code saved!")
		});
	},

	deleteCode : function( code ){
		var url = this.makeURL(code);
		$http.delete(url).success(function(data, status, headers, config) {
			console.log("Code deleted!")
		});
	},

	makeGetURL : function( filters ){
		var url = config.API_URL + "/codes/" + this.getToken() + "/" + filters.dc;
		if( filters.prefix ){
			url = url + "/" + filters.prefix;
		}
		if( filters.filterBy ){
			url = url + "/" + filters.filterBy + "/" + filters.filter;
		}
		return url;
	},

	loadCodes : function( _callback, filters ){
		if( !filters ){
			filters = { dc : "DC1" };
		}

		var url = this.makeGetURL(filters);
		//var url = "app/codes.json";

		$.getJSON(url, function(json, textStatus) {
			console.log(json);
			_callback(json);
		});
	},


	makeURL : function( code ){
		return config.API_URL + "/codes/" + this.getToken() + "/" + code.key;
	},

	// Save Token
	saveToken : function(token){

	}

};