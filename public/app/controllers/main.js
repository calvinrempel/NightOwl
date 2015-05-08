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
            $scope.selectTab('list');
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

        $scope.invalidLogin = function(){
            $scope.selectTab('login');
        }

        $scope.validLogin = function(result){
            API_HELPER.saveToken(result);
        }

        $scope.login = function(user, pw) {
            API_HELPER.loadCodes($scope.populateCodes, $scope.invalidLogin, user, pw)
        }

        API_HELPER.loadCodes($scope.populateCodes, $scope.invalidLogin, $scope.getFilters());
	});

})();