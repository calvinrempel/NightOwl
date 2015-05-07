(function(){
app.controller('LoginController', function($scope, $http, $location) {
    console.log($scope.selected);
    $scope.login = function(user, pw) {
        $http.get( $scope.config.API_URL + '/login/' + user + '/' + pw).
        success(function(data) {
           $scope.selectTab("list");
           $.getJSON( $scope.config.API_URL + '/login/' + user + '/' + pw, function(result) {
            API_HELPER.saveToken(result);
        })
       }).
        error(function (data, status, headers, config) {
            console.log('login error');
        });
    }
});
})();