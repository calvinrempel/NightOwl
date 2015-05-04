/**
 * Created by caseyy on 15-05-04.
 */

app.config(function ($stateProvider) {

    $stateProvider
        .state('/', {
            url: '/',
            data: {
                requireLogin: false
            }
        })
        .state('app', {
            abstract: true,
            data: {
                requireLogin: true
            }
        })
});