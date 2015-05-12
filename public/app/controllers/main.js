(function(){
	app.controller('MainController', function($scope, $http, API_CONFIG) {
		// Start on the login page
		$scope.selected = 'login';
		$scope.config = API_CONFIG;

        $scope.filters = {
            dataCenter : $scope.config.dataCenters[0].value,
            prefix : $scope.config.prefixes[0],
            filterBy : $scope.config.filters[0],
            filter : ''
        };
        
		$scope.populateCodes = function( codes ){
            console.log(codes);
        	$scope.launchCodes = codes;
            if( !$scope.isSelected('list') )
                $scope.selectTab('list');
            $("ul.sidebar").slideDown(400);
        	$scope.$apply();
        }

        $scope.populateAudits = function( audits ){
            console.log(audits);
            $scope.auditList = audits;
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
            return $scope.filters;
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