(function(){
	app.controller('MainController', function($scope, $http) {
		// Start on the login page
		$scope.selected = 'login';
		$scope.config = NIGHTOWL_CONFIG;

        
		$scope.populateCodes = function( codes ){
        	$scope.launchCodes = codes
        	$scope.$apply();
        }
        
        API_HELPER.loadCodes($scope.populateCodes, null);

        $scope.isSelected = function(val) {
            return $scope.selected === val;
        }

        $scope.selectTab = function(val) {
            $scope.selected = val;
        }

	});
})();