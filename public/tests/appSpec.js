// Tests here

describe('Night Owl', function() {

  // Angular Controller service needed to instantiate a controller
  var $controller;

  // Load the module
  beforeEach(module('NightOwl'));

  beforeEach(inject(function(_$controller_){
    // The injector unwraps the underscores (_) from around the parameter names when matching
    $controller = _$controller_;
  }));



  describe('Launch code list controller', function() {

    describe('Data center', function() {
      var $scope, controller;

      beforeEach(function() {
        $scope = {};
        controller = $controller('ListController', { $scope: $scope });
      });

      it('should default to west', function() {
        expect($scope.region).toEqual('west');
      });

    });

  });

    describe('Launch code audit controller', function() {

        describe('Data center', function() {
            var $scope, controller;

            beforeEach(function() {
                $scope = {};
                controller = $controller('ListController', { $scope: $scope });
            });

            it('should default to west', function() {
                expect($scope.region).toEqual('west');
            });

        });

    });

    describe('Launch code login controller', function() {

        describe('Login should succeed with username "McBuppy" and password "test"', function() {
            var $scope, controller, auth;

            beforeEach(function() {
                $scope = {};
                controller = $controller('LoginController', { $scope: $scope });
            });

            it('should succeed', function(name, pw) {
                expect(auth.login(name,pw, $scope.loginHandler)).toEqual('west');
            });

        });

    });
});