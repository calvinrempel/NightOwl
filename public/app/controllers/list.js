(function(){
app.controller('ListController', function($scope, loading, codes) {
		

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
            
            if( code.key !== undefined ){
            	loading.start();
				codes.save(code, $scope.filters)
	            .success(function(data){
	                alert("Code Saved!");
	                $scope.loadCodes();
	            })
	            .error(function(data, status){
	                if(status == 401 && $scope.selected !== "login")
	                    location.reload();
	            })
	            .finally(function(){
	                loading.stop();
	            });
           }else
           		alert("The code must have a key!");
		}

		$scope.deleteCode = function(code){
            var prompt = "Are you sure you wish to delete\n" + $scope.filters.prefix + "/" + code.key + "?";
			if( window.confirm( prompt ) ){
				loading.start();
                codes.remove(code, $scope.filters)
                .success(function(){
                    console.log("Code Deleted!");
                    $scope.loadCodes();
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

        



		$scope.discardChanges = function(index){
			$scope.loadCodes();
			$scope.editModeOff(index);
		}

		$scope.toggleJS = function(code){
			if(code.availableToJS == "false" || code.availableToJS === undefined){
				code.availableToJS = "true";
			}else{
				code.availableToJS = "false";
			}
		}

        

	});

})();
