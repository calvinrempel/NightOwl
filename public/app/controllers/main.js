(function(){
	app.controller('MainController', function($scope, $http) {
		// Start on the login page
		$scope.selected = 'login';
		$scope.config = NIGHTOWL_CONFIG;

        $scope.dataCenter = $scope.config.dataCenters[0];
        $scope.prefix = $scope.config.prefixes[0];
        $scope.filterBy = $scope.config.filters[0];
        $scope.filter = 'hello';
        
		$scope.populateCodes = function( codes ){
        	$scope.launchCodes = codes
            $scope.selectTab('list');
            $("ul.sidebar").slideDown(400);
        	$scope.$apply();
        }

        $scope.isSelected = function(val) {
            return $scope.selected === val;
        }

        $scope.selectTab = function(val) {
            var oldElem = $("#" + $scope.selected);
            var newElem = $("#" + val);
            oldElem.slideUp(400, function(){
                newElem.slideDown(400, function() {
                    $scope.selected = val;
                });
            });
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

        $scope.login = function(user, pw) {
            API_HELPER.login(user, pw, function(token){
                API_HELPER.saveToken(token);
                API_HELPER.loadCodes($scope.populateCodes, $scope.invalidLogin, $scope.getFilters());                
            });
        }
        $scope.logout = function(){
            API_HELPER.deleteToken();
            location.reload(true);
        }

        API_HELPER.loadCodes($scope.populateCodes, $scope.getFilters());
	});

})();