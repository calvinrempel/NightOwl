
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
        API_HELPER.loadAudits($scope.populateCodes, $scope.getSearches());
    };

    $scope.doSearch = function(val) {
        var opt = document.getElementById("searchOption");
        var str = document.getElementById("searchText");

        var url = $scope.config.API_URL + '/audit/' + API_HELPER.getToken() + '/';

        if(opt == 'Owner') {
            url = url + '{"owner":{"$regex":"' + str + '"}}';
            $.getJSON( url, function(result) {
                API_HELPER.loadAudits($scope.populateAudits, url);
            })
        }
        else if(opt == 'Code') {
            url = url + '{"code":{"$regex":"' + str + '"}}';
            $.getJSON( url, function(result) {
                API_HELPER.loadAudits($scope.populateAudits, url);
            })
        }
        else if(opt == 'Message') {
            url = url + '{"message":{"$regex":"' + str + '"}}';
            $.getJSON( url, function(result) {
                API_HELPER.loadAudits($scope.populateAudits, url);
            })
        }
        else {
            url = url + '{}';
            $.getJSON( url, function(result) {
                API_HELPER.loadAudits($scope.populateAudits, url);
            })
        }
    };

});
})();