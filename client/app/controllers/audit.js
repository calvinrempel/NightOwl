(function(){
app.controller('AuditController', function($scope, $http) {
	$scope.msg = "LIST THE AUDITS";

    // Gets the data center, prefix and filter type + expression
    $scope.getSearches = function(){
        var searches = {
            owner : $scope.owner,
            code : $scope.code,
            message : $scope.message
        }
        return searches;
    }

    // Gets the data center, prefix and filter type + expression
    $scope.getFilters = function(){
        var filters = {
            filterBy : $scope.filterBy,
            filter : $scope.filter
        }
        return filters;
    };

    // Should filter the code results based on the selected prefix
    $scope.reloadAudits = function(){
        API_HELPER.loadCodes($scope.populateAudits, $scope.getSearches());
    };

    $scope.login = function(user, pw) {
        var url = $scope.config.API_URL + '/audit/' + API_HELPER.getToken() + '/';

        //if()


    };

});
})();
