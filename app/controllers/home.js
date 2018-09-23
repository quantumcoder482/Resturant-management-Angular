angular.module('App').controller('HomeController', 
function($rootScope, $scope, $http, $mdToast, $cookies, $mdDialog, $route, services){
  if($cookies.session_uid == 'null' || $cookies.session_uid == null){ $scope.Expire_Session(); }

    $rootScope.pagetitle = 'Home';
	var self             = $scope;
	var root             = $rootScope;
    self.loading         = true;
  
   services.GetCounter().then(function(data){
   self.cspucount = data.data;
  });   
  
    
});

