
(function(){
app.controller('AuditController', function($scope, $http) {
	$scope.auditFilters = {
        filterBy : $scope.config.auditFilters[0],
        filter : ''
    };

    // Should filter the code results based on the selected prefix
    $scope.populateAudits = function( audits ){
        $scope.auditList = audits;
        $scope.$apply();
    };

    $scope.filterAudits = function(){
        API_HELPER.loadAudits($scope.populateAudits, $scope.auditFilters);
    };


});
})();