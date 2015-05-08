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
			var restriction, value, description, availableToJS;

			restriction = code.restriction || 'boolean';

			value = code.value || "false";

			description = code.description;

			if(code.availableToJS){
				availableToJS = 'true';
			}else{
				availableToJS = 'false';
			}

			var newCode = {
				key :  encodeURIComponent($scope.prefix + "/" + code.key),
				restriction : restriction,
				value : value,
				description : description,
				availableToJS : availableToJS
			};
			console.log(newCode);
			API_HELPER.saveCode( newCode, $scope.populateCodes, $scope.getFilters() );
		}


	});

app.animation('.slide', [function() {
  return {
    // make note that other events (like addClass/removeClass)
    // have different function input parameters
    enter: function(element, doneFn) {
      setTimeout(function() {jQuery(element).slideDown(100, doneFn);},1000);

      // remember to call doneFn so that angular
      // knows that the animation has concluded
    },

    leave: function(element, doneFn) {
      jQuery(element).slideUp(1000, doneFn);
    }
  }
}]);

})();
