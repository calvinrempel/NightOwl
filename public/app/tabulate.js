app.directive('tabulate', function() {

  return {
    restrict: 'E',
    require: 'tabulate',
    scope: {
      metadata: '=?',
      data: '=',
      deleteFn: '=?delete',
      editFn: '=?update',
      createFn: '=?create'
    },
    controller: function($scope){
  		var getInputs = function(index){
  			var selector = "tr#code-" + index;
  			return $(selector).find("td input, td select, td textarea");
  		}

  		// Toggles inputs for given code between enabled and disabled
  		$scope.toggleEditMode = function(index){
  			var inputs = getInputs(index);
  			inputs.prop('disabled', !inputs.prop('disabled'));
  		}

  		// Returns true if the current code is editable
  		$scope.inEditMode = function(index){
  			var input = getInputs(index).first()
  			return !input.prop('disabled');
  		}
    },

    templateUrl: 'app/views/tabulate.html'
    
  };
});