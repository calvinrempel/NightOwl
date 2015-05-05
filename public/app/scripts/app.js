(function(){
	var  app = angular.module('NightOwl', []);

	app.controller('MainController', function($scope, $http) {
		// Start on the login page
		$scope.selected = 'login';

        API_HELPER.loadCodes(function(data){
			$scope.launchCodes = data;
		}, null);
	

        $scope.isSelected = function(val) {
            return $scope.selected === val;
        }

        $scope.selectTab = function(val) {
            $scope.selected = val;
        }

	});

	app.controller('LoginController', function($scope, $http, $location) {
        console.log($scope.selected);
        $scope.login = function(user, pw) {
            $http.get('/login/' + user + '/' + pw).
                success(function(data) {
                    console.log('login success');
                    $scope.selected = 'list';
                    console.log($scope.selected);
                    $.getJSON('/login/' + user + '/' + pw, function(result) {
                        localStorage.setItem("key", result.key);
                        console.log(result.key);
                    })
                }).
                error(function (data, status, headers, config) {
                    console.log('login error');
            });
        }
	});

	app.controller('ListController', function($scope, $http) {
		// Default region is west
		$scope.region = "west";

		// Create mode is off
		$scope.createMode = false;

		// Should filter the code results based on the selected prefix
		$scope.filterResults = function(){

		};

		// Gets the data center, prefix and filter type + expression
		$scope.getFilters = function(){
			var filters = {
				dc : $scope.dataCenter,
				prefix : $scope.prefix,
				filterBy : $scope.filterBy,
				filter : $scope.filter
			}
			return filters;
		}

		// Toggles inputs for given code between enabled and disabled
		$scope.toggleEditMode = function(parent, child){
			var inputs = $scope.getInputs(parent, child);
			inputs.prop('disabled', !inputs.prop('disabled'));
		}

		// Returns true if the current code is editable
		$scope.inEditMode = function(parent, child){
			var input = $scope.getInputs(parent, child).first()
			return !input.prop('disabled');
		}

		// Gets the inputs, select box, and textarea associated with the code
		$scope.getInputs = function(parent, child){
			var selector = "tr#code-" + parent + "-" + child;
			return $(selector).find("td input, td select, td textarea");
		}


		// TODO: Save the code using the API		
		$scope.saveCode = function(code){ API_HELPER.saveCode( code ); }
			
		// TODO: Delete the code using the API (MAKE SURE TO CONFIRM FIRST)
		$scope.deleteCode = function(code){ API_HELPER.deleteCode( code ); }

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
				restriction : restriction,
				value : value,
				description : description,
				availableToJS : availableToJS
			};

			API_HELPER.saveCode( newCode );
		}
	});

	app.controller('AuditController', function($scope, $http) {
		$scope.msg = "LIST THE AUDITS";
	});

})();

