
(function(){
app.controller('AuditController', function($scope, $http, audits) {
	

    $scope.filterAudits = function(){
        $scope.loadAudits();
    };

});
})();
