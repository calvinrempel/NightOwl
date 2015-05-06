var API_HELPER = {

	getToken : function(){
		return localStorage.getItem("key");
	},

	saveCode : function( code, _callback, filters ){
		var url = this.makeURL(code);
		$.post(url, code).success(function(data, status, headers, config) {
			console.log("saved!");
			API_HELPER.loadCodes( _callback, filters );
		});
	},

	deleteCode : function( code, _callback, filters ){
	
		$.ajax({
    url: this.makeURL(code),
    type: 'DELETE',
    success: function(result) {
       console.log("deleted!");
       API_HELPER.loadCodes( _callback, filters );
    }
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
			filters = { dc : "dc1" };
		}

		var url = this.makeGetURL(filters);
		//var url = "app/codes.json";

		$.getJSON(url, function(json, textStatus) {
			_callback(json.codes);
		});
	},


	makeURL : function( code ){
		return config.API_URL + "/codes/" + this.getToken() + "/" + code.key;
	},

	// Save Token
	saveToken : function(result){
      localStorage.setItem("key", result.key);
	}

};
