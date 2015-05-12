
(function(){
app.controller('AuditController', function($scope, $http) {
    $scope.auditList = [];

	$scope.auditFilters = {
        filterBy : $scope.config.auditFilters[0],
        filter : ''
    };

    $scope.filterAudits = function(){
        $scope.auditList = [];
        API_HELPER.loadAudits($scope.populateAudits, $scope.auditFilters);
        $scope.apply;
    };


});
})();