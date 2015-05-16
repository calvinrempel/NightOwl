(function(){
	app.factory('codes', function($http, API_CONFIG, loading){
		var URL = API_CONFIG.API_URL

		function getURL(filters){
			var url = URL + "/codes/" + filters.dataCenter;

			if( filters.prefix ){
				url = url + "/" + encodeURIComponent(filters.prefix);
			}

			if( filters.filterBy && filters.filter ){
				url = url + "/" + filters.filterBy + "/" + filters.filter;
			}
			
			return url;
		}

		function postURL(code, filters){
			return URL + "/codes/" + filters.dataCenter + "/" +  encodeURIComponent(code.key);
		}

		function sanitize( code, filters ){
			var key, restriction, value, description, availableToJS;
			
			code.key = filters.prefix + "/" + code.key;

			code.restriction = code.restriction || 'boolean';

			code.value = code.value || "false";

			code.description = code.description || "";

			if(code.availableToJS && code.availableToJS != "false"){
				code.availableToJS = 'true';
			}else{
				code.availableToJS = 'false';
			}

			return code;
		}

		var codes = {

			save : function(code, filters){
				code = sanitize(code, filters);

				var url = postURL(code, filters);

				return $http.post(url, code);
			},
				

			load : function(filters){
				var url = getURL(filters);
				
				return $http.get( url );
			},

			remove : function( code, filters){
				code = sanitize(code, filters);
				var url = postURL(code, filters);
				
				return $http.delete( url );
			}
		};


		return codes;
	});
}());
