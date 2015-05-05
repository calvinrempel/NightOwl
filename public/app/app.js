(function(){
	var  app = angular.module('NightOwl', []);

	app.controller('MainController', function($scope, $http) {
		// Start on the login page
		$scope.selected = 'login';

		$scope.config = config;

		API_HELPER.loadCodes(function(data){
			$scope.launchCodes = data;
			}, null);
		};

	});

	app.controller('LoginController', function($scope, $http) {
		//ADD LOGIN FUNCTIONALITY HERE
	});

	app.controller('ListController', function($scope, $http) {
		// Default region is west
		$scope.region = "west";

		// Create mode is off
		$scope.createMode = false;

		// TODO: Filter results
		$scope.filterResults = function(){
	
		};

		// Gets the data center, prefix and filter type + expression
		$scope.getFilters = function(){
			var filters = {
				dc : $scope.dataCenter,
				prefix : $scope.prefix,
				filterBy : $scope.filterBy,
				filter : $scope.filter,
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

    app.controller('BackgroundController', function() {

    });

    //LoginModal functions
    app.run(function ($rootScope) {

        $rootScope.$on('$stateChangeStart', function (event, toState, toParams) {
            var requireLogin = toState.data.requireLogin;

            if (requireLogin && typeof $rootScope.currentUser === 'undefined') {
                event.preventDefault();

                loginModal()
                    .then(function () {
                        return $state.go(toState.name. toParams);
                    })
                    .catch(function () {
                        return $state.go('/');
                    });
            }
        });
    });


})();