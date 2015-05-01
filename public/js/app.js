(function(){
	var  app = angular.module('NightOwl', []);

	app.controller('MainController', function($scope, $http) {
		// Start on the login page
		$scope.selected = 'login';

		// Load configuration data
		loadConfig(function(data){
			$scope.config = data;
		});

	});

	app.controller('LoginController', function($scope, $http) {
		//ADD LOGIN FUNCTIONALITY HERE
	});

	app.controller('ListController', function($scope, $http) {
		// Default region is west
		$scope.region = "west";

		// Load the launch codes
		loadCodes(function(data){
			$scope.launchCodes = data;
		});

		$scope.filterResults = function(){
			loadCodes(function(data){
				$scope.launchCodes = data;
				
				var filter = 	
					( $scope.tree ? $scope.tree.name : "" ) + "/" +
					( $scope.subtree ? $scope.subtree : "" );

				for( tree in $scope.launchCodes ){
					if(tree.indexOf(filter) == -1){
						delete $scope.launchCodes[tree];
						console.log($scope.launchCodes);
					}
				}

			});


		};

	});

	app.controller('AuditController', function($scope, $http) {
		$scope.msg = "LIST THE AUDITS";
	});

})();

function loadConfig(_callback){
	var url = makeURL("config");
	$.getJSON(url, function(json, textStatus) {
		_callback(json);
	});
}

function loadCodes(_callback){
	var url = makeURL("codes");
	$.getJSON(url, function(json, textStatus) {
		_callback(json);
	});
}

function makeURL(string){
	return "js/" + string + ".json";
}

