(function(){
	app.controller('MainController', function($scope, $http, API_CONFIG, auth) {
		// Start on the login page
		$scope.selected = 'login';
		$scope.config = API_CONFIG;

        $scope.populateAudits = function( audits ){
            console.log(audits);
            $scope.auditList = audits;
            $scope.$apply();
        }

        $scope.isSelected = function(val) {
            return $scope.selected === val;
        }

        $scope.selectTab = function(val) {
            if( $scope.selected != val ){
                var oldElem = $("#" + $scope.selected);
                var newElem = $("#" + val);
                oldElem.slideUp(400, function(){
                    newElem.slideDown(400, function() {
                        $scope.selected = val;
                        if(val != "login"){
                            $("#wrapper").removeClass("toggled");
                        }
                        $scope.$apply();
                    });
                });
            }
        }

		$scope.logout = function(){
	    	auth.logout();
	    };


	});

})();
