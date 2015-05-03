// Tests here

describe('List Controller', function() {
  beforeEach(module('NightOwl'));

  var $controller;

  beforeEach(inject(function(_$controller_){
    // The injector unwraps the underscores (_) from around the parameter names when matching
    $controller = _$controller_;
  }));

  describe('$scope.region', function() {
    var $scope, controller;

    beforeEach(function() {
      $scope = {};
      controller = $controller('ListController', { $scope: $scope });
    });

    it('should be west', function() {
      expect($scope.region).toEqual('west');
    });

  });

});