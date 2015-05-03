(function(){
	var  app = angular.module('NightOwl', []);

	app.controller('MainController', function($scope, $http) {
		// Start on the login page
		$scope.selected = 'login';

		// Load configuration data
		loadConfig(function(data){
			$scope.config = data;
			loadCodes(function(data){
				$scope.launchCodes = data;
			}, $scope.config.API_URL, null);
		});

	});

	app.controller('LoginController', function($scope, $http) {
		//ADD LOGIN FUNCTIONALITY HERE
	});

	app.controller('ListController', function($scope, $http) {
		// Default region is west
		$scope.region = "west";

		// Should filter the code results based on the selected prefix
		$scope.filterResults = function(){
			loadCodes(function(data){
				$scope.launchCodes = data;
				
				var filter = 	
					( $scope.tree ? $scope.tree.name : "" ) + "/" +
					( $scope.subtree ? $scope.subtree : "" );

				for( tree in $scope.launchCodes ){
					if(tree.indexOf(filter) == -1){
						delete $scope.launchCodes[tree];
						console.log($scope.launchCodes);
					}
				}

			}, $scope.config.API_URL, $scope.getFilters());
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

		// Switches inputs between enabled and disabled
		$scope.switchMode = function(parent, child){
			var inputs = $scope.getInputs(parent, child);
			inputs.prop('disabled', !inputs.prop('disabled'));
		}

		// Returns true if the current row is editable
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
		$scope.saveCode = function(code){
			console.log("SAVE THE CODE")
		}

		// TODO: Delete the code using the API (MAKE SURE TO CONFIRM FIRST)
		$scope.deleteCode = function(code){
			console.log("DELETE THE CODE")
		}

		// TODO: Discard the changes made to the code
		$scope.discardChanges = function(code){
			console.log("DISCARD THE CHANGES")
		}
	});

	app.controller('AuditController', function($scope, $http) {
		$scope.msg = "LIST THE AUDITS";
	});

    app.controller('BackgroundContoller', function() {

    });

})();

// Load the configuration file
function loadConfig(_callback){
	$.getJSON("js/app/config.json", function(json, textStatus) {
		_callback(json);
	});
}

// Load the launch codes
function loadCodes(_callback, baseURL, filters){
	if( !filters ){
		filters = { dc : "DC1" };
	}

	//var url = makeURL(baseURL, filters);
	var url = "js/app/codes.json";

	$.getJSON(url, function(json, textStatus) {
		_callback(json);
	});
}

// Make the API URL
function makeURL(baseURL, filters){
	var url = baseURL + "/codes/" + getToken() + "/" + filters.dc;
	if( filters.prefix ){
		url = url + "/" + filters.prefix;
	}
	if( filters.filterBy ){
		url = url + "/" + filters.filterBy + "/" + filters.filter;
	}
	return url;
}

// TODO: get security token
function getToken(){
	return "boobs";
}
