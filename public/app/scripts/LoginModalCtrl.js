/**
 * Created by caseyy on 15-05-04.
 */
app.controller('LoginModalCtrl', function($scope, UsersApi) {

    this.cancel = $scope.$dismiss;

    this.submit = function(email, password) {
        UsersApi.login(email, password).then(function (user) {
            $scope.$close(user);
        });
    };
});