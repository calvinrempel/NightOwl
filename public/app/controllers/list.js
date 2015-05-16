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

        

	});

})();
