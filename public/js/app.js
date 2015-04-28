(function(){
	var  app = angular.module('NightOwl', []);

	app.controller('MainController', function($scope, $http) {
		$scope.selected = 'login';
	});

	app.controller('LoginController', function($scope, $http) {
		$scope.msg = "SHOW LOGIN"
	});

	app.controller('ListController', function($scope, $http) {
		$scope.region = "west";
		$scope.msg = "LIST THE CODES";
	});

	app.controller('AuditController', function($scope, $http) {
		$scope.msg = "LIST THE AUDITS";
	});



})();

