(function(){
	var  app = angular.module('NightOwl', []);

	app.controller('MainController', function($scope, $http) {

		$scope.selected = 'login';
		$.getJSON('js/config.json', function(json, textStatus) {
			$scope.config = json;
			console.log($scope.config.trees);
		});
	});

	app.controller('LoginController', function($scope, $http, $compile) {
		//ADD LOGIN FUNCTIONALITY HERE
	});

	app.controller('ListController', function($scope, $http) {
		$scope.depth = 0;
		$.getJSON('js/codes.json', function(json, textStatus) {
			$scope.launchCodes = json;
		});

		$scope.region = "west";

	});

	app.controller('AuditController', function($scope, $http) {
		$scope.msg = "LIST THE AUDITS";
	});

})();

