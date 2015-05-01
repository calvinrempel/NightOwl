(function(){
	var  app = angular.module('NightOwl', []);

	app.controller('MainController', function($scope, $http) {
		// Start on the login page
		$scope.selected = 'login';

		// Load configuration data
		loadConfig(function(data){
			$scope.config = data;
			loadCodes(function(data){
				$scope.launchCodes = data;
			}, $scope.config.API_URL, null);
		});

	});

	app.controller('LoginController', function($scope, $http) {
		//ADD LOGIN FUNCTIONALITY HERE
	});

	app.controller('ListController', function($scope, $http) {
		// Default region is west
		$scope.region = "west";

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

			}, $scope.config.API_URL, $scope.getFilters());
		};

		$scope.getFilters = function(){
			var filters = {
				dc : $scope.dataCenter,
				prefix : $scope.prefix,
				filterBy : $scope.filterBy,
				filter : $scope.filter,
			}
			return filters;
		}

	});

	app.controller('AuditController', function($scope, $http) {
		$scope.msg = "LIST THE AUDITS";
	});

})();

function loadConfig(_callback){
	$.getJSON("js/config.json", function(json, textStatus) {
		console.log(json)
		_callback(json);
	});
}

function loadCodes(_callback, baseURL, filters){
	if( !filters ){
		filters = { dc : "DC1" };
	}
	var url = makeURL(baseURL, filters);
	$.getJSON(url, function(json, textStatus) {
		console.log(json);
		_callback(json);
	});
}

function makeURL(baseURL, filters){
	var url = baseURL + "/codes/" + getToken() + "/" + filters.dc;
	if( filters.prefix ){
		url = url + "/" + filters.prefix;
	}
	if( filters.filterBy ){
		url = url + "/" + filters.filterBy + "/" + filters.filter;
	}
	return url;
}

function getToken(){
	return "boobs";
}
