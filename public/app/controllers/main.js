(function(){
	app.controller('MainController', function($scope, $http, API_CONFIG, auth, codes, audits, loading) {
		// Start on the login page
		$scope.selected = 'login';

        function init(){
            $scope.config = API_CONFIG;
            $scope.createMode = false;
            $scope.editMode = [];
            $scope.newCode = {};
            $scope.prefixes = [];
            $scope.dc = {};
            $scope.filters = {};
            $scope.sort = {};
            $scope.setDataCenter($scope.config.dataCenters[0]);
            $scope.auditFilters = {
                filterBy : $scope.config.auditFilters[0],
                filter : ''
            };
            $scope.loadAudits();
        }

        $scope.loadCodes = function(){
            loading.start();
            codes.load( $scope.filters )
            .success(function(data){
                listCodes(data.codes);
            })
            .error(function(data, status){
                if(status == 401 && $scope.selected !== "login")
                    location.reload();
            })
            .finally(function(){
                loading.stop()
            });
        }

        $scope.loadAudits = function(){
            audits.load($scope.auditFilters, function(success, data){
                if(success){
                    $scope.auditList = data;
                } else if( data === 401 ){
                    if ($scope.selected !== 'login')
                        location.reload();
                }
            });
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
                        }if( val == "audit" ){
                            $scope.loadAudits();
                        }if( val == "list" ){
                            $scope.loadCodes();
                        }
                        $scope.$apply();
                    });
                });
            }
        }

		$scope.logout = function(){
	    	auth.logout();
	    };

        $scope.setDataCenter = function(dataCenter){
            $scope.dc = dataCenter;
            $scope.prefixes = buildList(dataCenter.prefixes);
            $scope.filters = {
                dataCenter : dataCenter.value,
                filterBy : $scope.config.filters[0],
                prefix: $scope.prefixes[0],
                filter : ''
            };
            $scope.loadCodes();
        }

        $scope.resetFilters = function(){
            if($scope.dc !== undefined)
                $scope.setDataCenter( $scope.dc );
            else
                $scope.setDataCenter( $scope.config.dataCenters[0] );
        }



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
            for (var i = 0; i < codes.length; i++) {
                codes[i].key = codes[i].key.replace($scope.filters.prefix + "/", "");
            }
            return codes;
        }

        function listCodes( codes ){

            $scope.sort.keys = Object.keys( codes[0] );
            $scope.sort.descending = "false";
            $scope.sort.type = $scope.sort.keys[0];
            $scope.launchCodes = trimKeys( codes );
            $scope.selectTab("list");
        }

        


        init();


	});

})();
