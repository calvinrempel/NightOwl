(function(){
	app.factory('loading', function(API_CONFIG){
		var id = API_CONFIG.loadingID

		var loading = {
			
			start: function(){
				$(id).fadeIn(400);
			},

			stop: function(){
				$(id).fadeOut(400);
			}
		};

		
		return loading;
	});
}());