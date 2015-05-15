(function(){
	app.factory('audits', function($http, API_CONFIG, auth, loading){
		var URL = API_CONFIG.API_URL;

		var audits = {

			load: function(filters, _callback){
				var url = URL + '/audit/' + auth.getToken() + '/';
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
                else if(filters.filterBy == 'Most Recent') {
                    var d = new Date();
                    var day = d.getDate();
                    d.setDate(day-1);
                    var date = d.toJSON();

                    url = url + '{"time":{"$gt":"' + date + '"}}';
                }
                console.log(url);
		        loading.start();
		        $http.get(url)
		        .success(function(data){
		        	_callback(true, data);
                        console.log(url);
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