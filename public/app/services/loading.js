(function(){
	app.factory('loading', function(API_CONFIG){
		var id = API_CONFIG.loadingID

		var loading = {
			
			start: function(){
				$(id).fadeIn(400);
				$(".data").css({
					"opacity": '0.5',
					"pointer-events": 'none'
				});
			},

			stop: function(){
				$(id).fadeOut(400);
				$(".data").css({
					"opacity": '1',
					"pointer-events": 'all'
				});
			}
		};

		
		return loading;
	});
}());