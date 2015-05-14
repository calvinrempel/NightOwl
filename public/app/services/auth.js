(function(){
	app.factory('auth', function($http, API_CONFIG, loading){
		var URL = API_CONFIG.API_URL

		function saveToken(data){
			localStorage.setItem("key", data.key);
		}

		function getToken(){
			return localStorage.getItem("key");
		}

		function destroyToken(){
			localStorage.removeItem("key");
		}

		var auth = {
			
			login: function(user, pw, _callback){
				var url = URL + '/login/' + user + '/' + pw;
				
				loading.start();
				$http.get( url )
		        .success(function(data) {
		            saveToken(data);
		            _callback(true);
		        })
		        .error(function() {
		        	_callback(false)
		        })
		        .finally(function(){
		        	loading.stop();
		        });
			},

			logout: function(){
				destroyToken();
				location.reload(true);
			},

			getToken: function(){return getToken();}
		};

		
		return auth;
	});
}());