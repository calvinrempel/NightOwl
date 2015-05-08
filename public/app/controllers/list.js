(function(){
app.controller('ListController', function($scope, $http) {
		
		// Create mode is off
		$scope.createMode = false;

		// Gets the data center, prefix and filter type + expression
		

		// Should filter the code results based on the selected prefix
		$scope.reloadCodes = function(){
			API_HELPER.loadCodes($scope.populateCodes, $scope.getFilters());
		};

		// Toggles inputs for given code between enabled and disabled
		$scope.toggleEditMode = function(index){
			var inputs = $scope.getInputs(index);
			inputs.prop('disabled', !inputs.prop('disabled'));
		}

		// Returns true if the current code is editable
		$scope.inEditMode = function(index){
			var input = $scope.getInputs(index).first()
			return !input.prop('disabled');
		}

		// Gets the inputs, select box, and textarea associated with the code
		$scope.getInputs = function(index){
			var selector = "tr#code-" + index;
			return $(selector).find("td input, td select, td textarea");
		}


		// TODO: Save the code using the API
		$scope.saveCode = function(code){ 
			API_HELPER.saveCode( code, $scope.populateCodes, $scope.getFilters() );
		}

		// TODO: Delete the code using the API (MAKE SURE TO CONFIRM FIRST)
		$scope.deleteCode = function(code){ 
			API_HELPER.deleteCode( code, $scope.populateCodes, $scope.getFilters() ); 
		}

		// TODO: Discard the changes made to the code
		$scope.discardChanges = function(code){
			console.log("DISCARD THE CHANGES")
		}

		$scope.createCode = function(code){
			var key, restriction, value, description, availableToJS;

			key = $scope.filters.prefix + "/" + code.key,

			restriction = code.restriction || 'boolean';

			value = code.value || "false";

			description = code.description;

			if(code.availableToJS){
				availableToJS = 'true';
			}else{
				availableToJS = 'false';
			}

			var newCode = {
				key :  key,
				restriction : restriction,
				value : value,
				description : description,
				availableToJS : availableToJS
			};
			console.log(newCode);
			API_HELPER.saveCode( newCode, $scope.populateCodes, $scope.getFilters() );
		}


	});
})();
