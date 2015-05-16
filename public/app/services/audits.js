(function(){
	app.factory('audits', function($http, API_CONFIG, auth, loading){
		var URL = API_CONFIG.API_URL;

		var audits = {

			load: function(filters, _callback){
				var url = URL + '/audit/';
				var filter = encodeURIComponent(filters.filter);

		        if(filters.filterBy == 'User') {
		            url = url + '{"owner":{"$regex":"' + filter + '","$options":"-i"}}';
		        }
		        else if(filters.filterBy == 'Code') {
		            url = url + '{"code":{"$regex":"' + filter + '","$options":"-i"}}';
		        }
		        else if(filters.filterBy == 'Message') {
		            url = url + '{"message":{"$regex":"' + filter + '","$options":"-i"}}';
		        }
		        else if(filters.filterBy == 'All') {
                    url = url + '{"$or":[{"owner":{"$regex":"' + filter + '","$options":"-i"}},{"code":{"$regex":"' + filter + '","$options":"-i"}},{"message":{"$regex":"' + filter + '","$options":"-i"}}]}';
               	}
                else if(filters.filterBy == 'Last 24 Hours') {
                    var d = new Date();
                    var day = d.getDate();
                    d.setDate(day-1);
                    var date = d.toJSON();

                    url = url + '{"time":{"$gt":"' + date + '"}}';
                }

		        loading.start();
		        $http.get(url)
		        .success(function(data){
		        	_callback(true, data);

		        })
		    	.error(function(data, status){
		    		_callback(false, status);
		    	})
		        .finally(function() {
                    loading.stop();
                });
		    }
		}

		return audits;
	});

}());
