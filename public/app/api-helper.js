var API_HELPER = (function () {
	var instance;

	function createInstance() {
		var helper = {

			API_URL : "http://nightowlAPI.local",

			getToken : function(){
				return localStorage.getItem("key");
			},

			saveCode : function( code, _callback, filters ){
				var url = this.makePostURL(code, filters);
				this.startLoading(null);
				$.post(url, code).success(function(data, status, headers, config) {
					console.log("saved!");
					console.log(url);
					API_HELPER.loadCodes( _callback, filters );
				})
			  .always(function() {
			    API_HELPER.stopLoading(null);
			  });
			},

			deleteCode : function( code, _callback, filters ){
				this.startLoading(null);
				$.ajax({
					url: this.makePostURL(code, filters),
					type: 'delete',
					success: function(result) {
						console.log("deleted!");
						API_HELPER.loadCodes( _callback, filters );
					}
				})
			  .always(function() {
			    API_HELPER.stopLoading(null);
			  });
			},

			makeGetURL : function( filters ){
				
			},

			loadCodes : function( _callback, filters ){

				if( !filters ){
					filters = { dc : "dc1" };
				}

				var url = this.makeGetURL(filters);
				//var url = "app/codes.json";

				console.log(url);

				this.startLoading(null);
				$.getJSON(url, function(json, textStatus) {
					console.log(json);
					_callback(json.codes);
				})
				.fail(function(){
					console.log("FAILURE");
				})
			  .always(function() {
			    API_HELPER.stopLoading(null);
			  });
			},

            //case for last 24 hours
            //var url = this.API_URL + '/audit/' + this.getToken() + '/{"time":{"$gt":"' + (Date.now()*1000-86400) + '"}';


            loadAudits : function( _callback, filters ){
        		var url = this.API_URL + '/audit/' + this.getToken() + '/';
		        if(filters.filterBy == 'User') {
		            url = url + '{"owner":{"$regex":"' + filters.filter + '","$options":"-i"}}';
		        }
		        else if(filters.filterBy == 'Code') {
		            url = url + '{"code":{"$regex":"' + filters.filter + '","$options":"-i"}}';
		        }
		        else if(filters.filterBy == 'Message') {
		            url = url + '{"message":{"$regex":"' + filters.filter + '","$options":"-i"}}';
		        }
		        else if(filters.filterBy == 'All') {
                    url = url + '{"$or":[{"owner":{"$regex":"' + filters.filter + '","$options":"-i"}},{"code":{"$regex":"' + filters.filter + '","$options":"-i"}},{"message":{"$regex":"' + filters.filter + '","$options":"-i"}}]}';
                }

		        console.log(url);
		        this.startLoading(null);
		        $.getJSON(url, function(result){
		        	_callback(result);
		        })
		        .always(function() {
                        API_HELPER.stopLoading(null);
                    });
            },

			login : function(user, pw, _callback){
				var url = this.API_URL + '/login/' + user + '/' + pw;
				$.getJSON(url, function(data) {
					_callback(data);
				});
			},

			makePostURL : function( code, filters ){
				
			},

			// Save Token
			saveToken : function(result){
				localStorage.setItem("key", result.key);
			},

			deleteToken : function(){
				localStorage.removeItem("key");
			},

			startLoading : function(element){
				$("#loading").fadeIn('fast');
			},

			stopLoading : function(element){
				$("#loading").fadeOut('fast');
			}
		};

		return helper;
	}

	if( !instance )
		instance = createInstance();

	return instance;
})();


