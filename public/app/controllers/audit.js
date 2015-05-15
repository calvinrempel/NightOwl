
(function(){
app.controller('AuditController', function($scope, $http, audits) {
	$scope.auditFilters = {
        filterBy : $scope.config.auditFilters[0],
        filter : ''
    };

    $scope.filterAudits = function(){
        loadAudits();
    };

    function loadAudits(){
        audits.load($scope.auditFilters, function(success, data){
            if(success){
                $scope.auditList = data;
            }
        });
    }

    loadAudits();


});
})();