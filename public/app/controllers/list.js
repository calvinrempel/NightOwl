(function(){
app.controller('ListController', function($scope, $http, codes, auth, loading) {

		

		function init(){
			$scope.createMode = false;
			$scope.editMode = [];
			$scope.newCode = {};
			$scope.sort = {};
			$scope.prefixes = [];
			$scope.dc = {};
			$scope.filters = {};
			$scope.sortOptions = [];
			$scope.setDataCenter($scope.config.dataCenters[0]);
		}


		

        // EDIT MODE FUNCTIONS
		$scope.editModeOn = function(index){
			$scope.editMode[index] = true;
		}
		$scope.editModeOff = function(index){
			$scope.editMode[index] = false;
		}
		$scope.inEditMode = function(index){
			return ($scope.editMode[index] !== undefined && $scope.editMode[index] != false);
		}

		// CODE CRUD FUNCTIONS
        $scope.loadCodes = function(){
            loading.start();
            codes.load($scope.filters)
            .success(function(data){
                listCodes( data.codes );
            })
            .error(function(data, status){
                if(status == 401 && $scope.selected !== "login")
                    location.reload();
            })
            .finally(function(){
                loading.stop()
            });
        }

		$scope.saveCode = function(code){
            loading.start();

			codes.save(code, $scope.filters)
            .success(function(data){
                console.log("Code Saved!");
            })
            .error(function(data, status){
                if(status == 401 && $scope.selected !== "login")
                    location.reload();
            })
            .finally(function(){
                loading.stop();
            });
		}

		$scope.deleteCode = function(code){
            var prompt = "Are you sure you wish to delete\n" + $scope.filters.prefix + "/" + code.key + "?";
			if( window.confirm( prompt ) ){
				loading.start();
                codes.delete()
                .success(function(){
                    console.log("Code Deleted!");
                })
                .error(function(data, status){
                    if(status == 401 && $scope.selected !== "login")
                        location.reload();
                })
                .finally(function(){
                    loading.stop()
                });
            }
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
            $scope.loadCodes();
        }



		$scope.discardChanges = function(index){
			$scope.loadCodes();
			$scope.editModeOff(index);
		}

		$scope.toggleJS = function(code){
			if(code.availableToJS == "false"){
				code.availableToJS = "true";
			}else{
				code.availableToJS = "false";
			}
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
            $scope.selectTab("list");
            $scope.launchCodes = trimKeys( codes );
            $scope.sortOptions = Object.keys( codes );
            $scope.sort.field = $scope.sortOptions[0];
            $scope.sort.desc = true;
        }


	});

})();
