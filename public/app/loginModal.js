/**
 * Created by caseyy on 15-05-04.
 */
app.service('loginModal', function($modal, $rootScope) {

    function assignCurrentUser(user) {
        $rootScope.currentUser = user;
        return user;
    }

    return function() {
        var instance = $modal.open({
            templateUrl: 'view/application/index/loginModal.html',
            controller: 'LoginModalCtrl',
            controllerAs: 'LoginModalCtrl'
        })

        return instance.result.then(assignCurrentUser);
    };
});