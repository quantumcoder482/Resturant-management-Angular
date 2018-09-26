var app=angular.module('App').controller('StockreportController', 
function($rootScope, $scope, $http, $mdToast, $cookies, $mdDialog, $route, services ){
  if($cookies.session_uid == 'null' || $cookies.session_uid == null){ $scope.Expire_Session(); }
   
    var self             = $scope;
	var root             = $rootScope;
    self.loading         = true;
	 
	root.toolbar_menu=null;
	
	$rootScope.pagetitle = 'Stock Report';
	
	self.parfrom_day = toISOLocal(new Date()).split("T")[0];
	self.parto_day = toISOLocal(new Date()).split("T")[0];
	services.getDayStockReport(self.parfrom_day, self.parto_day).then(function(data){
		self.thedaystock = data.data;
	});
	
	self.get_stock_report = function(from, to) {
		services.getDayStockReport(from, to).then(function(data){
			self.thedaystock = data.data;	
		});
	}

});