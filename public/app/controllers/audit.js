
(function(){
app.controller('AuditController', function($scope, $http) {
    $scope.auditList = [];

	$scope.auditFilters = {
        filterBy : $scope.config.auditFilters[0],
        filter : ''
    };

    $scope.filterAudits = function(){
        API_HELPER.loadAudits($scope.populateAudits, $scope.auditFilters);
    };


});
})();