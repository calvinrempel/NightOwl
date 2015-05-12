(function(){
	app.controller('MainController', function($scope, $http, API_CONFIG) {
		// Start on the login page
		$scope.selected = 'login';
		$scope.config = API_CONFIG;

        $scope.dc = {};
        $scope.filters = {};

        $scope.getFilters = function(){
            return $scope.filters;
        }

        $scope.setDataCenter = function(dataCenter){
            $scope.dc = dataCenter;
            $scope.prefixes = buildList(dataCenter.prefixes);
            $scope.filters = {
                dataCenter : dataCenter.value,
                filterBy : $scope.config.filters[0],
                prefix: $scope.prefixes[0],
                filter : ''
            };
        }

        $scope.populateCodes = function( codes ){
            trimKeys(codes);
            $scope.launchCodes = codes;
            if( !$scope.isSelected('list') )
                $scope.selectTab('list');
            $scope.$apply();
        }

        $scope.resetFilters = function(){
            if($.isUndefined($scope.dc))
                $scope.setDataCenter( $scope.dc );
            else
                $scope.setDataCenter( $scope.config.dataCenters[0] );
            API_HELPER.loadCodes($scope.populateCodes, $scope.getFilters());
        }

        $scope.resetFilters();

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
                    if(val != "login"){
                        $("#wrapper").removeClass("toggled");
                    }
                    $scope.$apply();
                });
            });

        }

        $scope.login = function(user, pw) {
            API_HELPER.login(user, pw, function(token){
                API_HELPER.saveToken(token);
                API_HELPER.loadCodes($scope.populateCodes, $scope.getFilters());
            });
        }
        $scope.logout = function(){
            API_HELPER.deleteToken();
            location.reload(true);
        }

        API_HELPER.loadCodes($scope.populateCodes, $scope.getFilters());

        function buildList(object, branch){
            var array = [];
            for(key in object){
                if(!branch){
                    array.push(key)
                    if(!$.isEmptyObject(object[key]))
                        array = array.concat(buildList(object[key], key));
                }else{
                    array.push(branch + "/" + key);
                    if(!$.isEmptyObject(object[key]))
                        array = array.concat(buildList(object[key], branch + "/" + key));
                }
                    
            }
            return array;
        }

        function trimKeys( codes ){
            console.log($scope.filters.prefix);
            for (var i = 0; i < codes.length; i++) {
                codes[i].key = codes[i].key.replace($scope.filters.prefix + "/", "");
            };
        }
	});

})();