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
});