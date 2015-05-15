(function(){
	app.factory('audits', function($http, API_CONFIG, auth, loading){
		var URL = API_CONFIG.API_URL;

		var audits = {

			load: function(filters, _callback){
				var url = URL + '/audit/';
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

		        loading.start();
		        $http.get(url)
		        .success(function(data){
		        	_callback(true, data);
		        })
		    	.error(function(data, status){
		    		_callback(false, status)
		    	})
		        .finally(function() {
                    loading.stop();
                });
		    }
		}

		return audits;
	});

}());
