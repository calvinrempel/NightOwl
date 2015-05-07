(function(){
	app.controller('MainController', function($scope, $http) {
		// Start on the login page
		$scope.selected = 'login';
		$scope.config = NIGHTOWL_CONFIG;

        $scope.dataCenter = $scope.config.dataCenters[0];
        $scope.prefix = $scope.config.prefixes[0];
        
		$scope.populateCodes = function( codes ){
            console.log(codes);
        	$scope.launchCodes = codes
        	$scope.$apply();
        }

        $scope.isSelected = function(val) {
            return $scope.selected === val;
        }

        $scope.selectTab = function(val) {
            $scope.selected = val;
        }

        $scope.getFilters = function(){
            var filters = {
                dc : $scope.dataCenter,
                prefix : $scope.prefix,
                filterBy : $scope.filterBy,
                filter : $scope.filter
            }
            return filters;
        }

	});
})();