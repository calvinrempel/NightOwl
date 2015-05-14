(function(){
app.controller('ListController', function($scope, $http) {
		
		// Create mode is off
		$scope.createMode = false;
		$scope.editMode = [];
		$scope.newCode = {};

		// Gets the data center, prefix and filter type + expression
		$scope.sort = {
			column : 'key'
		};
		
		// Should filter the code results based on the selected prefix
		$scope.reloadCodes = function(){
			console.log($scope.filters);
			API_HELPER.loadCodes($scope.populateCodes, $scope.getFilters());
		};

		// Toggles inputs for given code between enabled and disabled
		$scope.editModeOn = function(index){
			$scope.editMode[index] = true;
		}

		$scope.editModeOff = function(index){
			$scope.editMode[index] = false;
		}
		$scope.inEditMode = function(index){
			return ($scope.editMode[index] !== undefined && $scope.editMode[index] != false);
		}

		// TODO: Save the code using the API
		$scope.saveCode = function(code){ 
			console.log(code);
			API_HELPER.saveCode( code, $scope.populateCodes, $scope.getFilters() );
		}

		// TODO: Delete the code using the API (MAKE SURE TO CONFIRM FIRST)
		$scope.deleteCode = function(code){ 
			if( window.confirm("Are you sure you wish to delete\n" + $scope.filters.prefix + "/" + code.key + "?" ) )
				API_HELPER.deleteCode( code, $scope.populateCodes, $scope.getFilters() ); 
		}

		// TODO: Discard the changes made to the code
		$scope.discardChanges = function(code){
			$scope.reloadCodes();
		}

		$scope.createCode = function(){
			var key, restriction, value, description, availableToJS;

			key = $scope.filters.prefix + "/" + $scope.newCode.key,

			restriction = $scope.newCode.restriction || 'boolean';

			value = $scope.newCode.value || "false";

			description = $scope.newCode.description;

			if($scope.newCode.availableToJS){
				availableToJS = 'true';
			}else{
				availableToJS = 'false';
			}

			var code = {
				key :  key,
				restriction : restriction,
				value : value,
				description : description,
				availableToJS : availableToJS
			};

			API_HELPER.saveCode( code, $scope.populateCodes, $scope.getFilters() );
			$scope.createMode = false;
			$scope.newCode = {};
		}

		$scope.toggleJS = function(code){
			console.log(code);
			if(code.availableToJS == "false"){
				code.availableToJS = "true";
			}else{
				code.availableToJS = "false";
			}
		}

	});
})();
