(function(){
app.controller('ListController', function($scope, $http, codes, auth) {
		
		init();

		function init(){
			$scope.createMode = false;
			$scope.editMode = [];
			$scope.newCode = {};
			$scope.sort = {};
			$scope.prefixes = [];
			$scope.dc = {};
			$scope.filters = {};
			$scope.sortOptions = [];
			setDataCenter($scope.config.dataCenters[0]);
		}


		$scope.setDataCenter = function(dataCenter){
			setDataCenter(dataCenter)
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
		$scope.saveCode = function(code){ 
			saveCode(code);
		}
		$scope.deleteCode = function(code){ 
			if( window.confirm("Are you sure you wish to delete\n" + $scope.filters.prefix + "/" + code.key + "?" ) )
				deleteCode(code)
		}
		$scope.createCode = function(){
			createCode($scope.newCode);
		}

		$scope.discardChanges = function(index){
			loadCodes();
			$scope.editModeOff(index);
		}

		$scope.reloadCodes = function(){
			loadCodes();
		}

		$scope.toggleJS = function(code){
			console.log(code);
			if(code.availableToJS == "false"){
				code.availableToJS = "true";
			}else{
				code.availableToJS = "false";
			}
		} 

        $scope.resetFilters = function(){
            if($scope.dc !== undefined)
                setDataCenter( $scope.dc );
            else
                setDataCenter( $scope.config.dataCenters[0] );
        }
        
        function setDataCenter(dataCenter){
        	$scope.dc = dataCenter;
            $scope.prefixes = buildList(dataCenter.prefixes);
            $scope.filters = {
                dataCenter : dataCenter.value,
                filterBy : $scope.config.filters[0],
                prefix: $scope.prefixes[0],
                filter : ''
            };
            loadCodes();
        }

        function loadCodes(){
        	codes.load($scope.filters, function(success, data){
        		if(success){
	        		$scope.selectTab("list");
	        		$scope.launchCodes = trimKeys( data.codes );
	        		$scope.sortOptions = Object.keys(data.codes);
	        		$scope.sort.field = $scope.sortOptions[0];
	        		$scope.sort.desc = true;
        		}else if( data === 401 ){
                    auth.logout()
                }
        	});
        }

        function deleteCode( code ){
        	codes.remove( code, $scope.filters, function(success, data){
        		if(success){
        			console.log("Code Deleted!");
        			loadCodes();
        		}else if( data === 401 ){
                    auth.logout()
                }
        	}); 
        }

        function createCode( code ){
        	code.key = $scope.filters.prefix + "/" + code.key
        	codes.save(code, $scope.filters, function(success, data){
        		if(success){
        			console.log("Code Created!");
        			$scope.createMode = false;
        			loadCodes();
        		}else if( data === 401 ){
                    auth.logout()
                }
        	});
        }

        function saveCode( code ){
        	codes.save(code, $scope.filters, function(success, data){
        		if(success){
        			console.log("Code Saved!");
        		}else if( data === 401 ){
                    auth.logout()
                }
        	});
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
            console.log($scope.filters.prefix);
            for (var i = 0; i < codes.length; i++) {
                codes[i].key = codes[i].key.replace($scope.filters.prefix + "/", "");
            }
            return codes;
        }
        

	});
})();
