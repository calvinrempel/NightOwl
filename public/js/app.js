(function(){
	var  app = angular.module('NightOwl', []);

	app.controller('MainController', function($scope, $http) {

		$scope.selected = 'login';
		$.getJSON('js/config.json', function(json, textStatus) {
				$scope.config = json;
		});
	});

	app.controller('LoginController', function($scope, $http) {
		$scope.msg = "SHOW LOGIN";
	});

	app.controller('ListController', function($scope, $http) {
		$scope.region = "west";
		$.getJSON('js/codes.json', function(json, textStatus) {
			$scope.trees = json;
		});

		$scope.echo = function(){
			console.log("HELLO");
			console.log($scope.trees);
		};
	});

	app.controller('AuditController', function($scope, $http) {
		$scope.msg = "LIST THE AUDITS";
	});

})();

