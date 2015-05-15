(function(){
app.controller('LoginController', function($scope, $http, auth) {


    $scope.loginHandler = function(success){
      if(success){
        $scope.selectTab("list")
      }
      else {
          alert("Invalid Login");
      }
    };

    $scope.login = function(user, pw) {
        auth.login(user,pw, $scope.loginHandler)
    };


});
})();
