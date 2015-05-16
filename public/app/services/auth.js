(function(){
	app.factory('auth', function($http, API_CONFIG, loading){
		var URL = API_CONFIG.API_URL;

		var auth = {

			login: function(user, pw, _callback){
				var url = URL + '/auth/login';

				loading.start();
				$http.post( url, {name:user, pass:pw} )
		        .success(function(data) {
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
				var url = URL + '/auth/logout';

				loading.start();
				$http.delete( url )
				.success(function(data){
					location.reload(true);
				})
				.finally(function(data){
					loading.stop();
				});

			},

			getToken: function(){return getToken();}
		};


		return auth;
	});
}());
