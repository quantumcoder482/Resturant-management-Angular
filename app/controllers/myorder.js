var app = angular.module('App').controller('RorderController', 
function($rootScope, $scope, $http, $mdToast, $cookies, $mdDialog, $route, services){
  if($cookies.session_uid == 'null' || $cookies.session_uid == null){ $scope.Expire_Session(); }

  $rootScope.pagetitle = 'Order History';
	var self             = $scope;
	var root             = $rootScope;
  self.loading         = true;
     root.toolbar_menu = null;

	
    services.getMyorders().then(function(data){
    self.rorder = data.data;
    self.loading = false;
	
  });  
  
  
    $scope.numberOfPages=function(){
        return Math.ceil(self.rorder.length/$scope.pageSize);                
    }
   
  
     $scope.sort = function(keyname){
		$scope.sortKey = keyname;   //set the sortKey to the param passed
		$scope.reverse = !$scope.reverse; //if true make it false and vice versa
	}
	
	
	  self.ViewInvoice = function(ev, id) {
	       window.open("invoice/"+id+ ".pdf");
	     };
	 
	  self.EditOrder = function(ev, id) {
	       window.open("#edit/"+id);
	     };
	 
  self.deleteAllOrder = function(ev) {

  } 

  self.deleteOrder = function(ev, id) {
    services.deleteOrder(id).then(function(res){
        if(res.status == 'success'){
          $mdToast.show($mdToast.simple().hideDelay(1000).content('Deleted Order Success!').position('bottom right'))
          .then(function() {
            window.location.reload();
          });
        }else{
          $mdToast.show(
            $mdToast.simple().hideDelay(6000).action('CLOSE').content('Failed delete Order').position('bottom right')
          ).then(function(response){

          });
        }
    });    
  }

  self.deleteAllOrder = function(ev) {
    var confirm = $mdDialog.confirm().title('Delete All Order Confirmation')
      .content('Are you sure want to delete all order?')
      .targetEvent(ev)
      .ok('OK').cancel('CANCEL');

    $mdDialog.show(confirm).then(function() {
    services.deleteAllOrder().then(function(res){
        if(res.status == 'success'){
          $mdToast.show($mdToast.simple().hideDelay(1000).content('Deleted All Order Success!').position('bottom right'))
          .then(function() {
            window.location.reload();
          });
        }else{
          $mdToast.show(
            $mdToast.simple().hideDelay(6000).action('CLOSE').content('Failed delete All Order').position('bottom right')
          ).then(function(response){

          });
        }
    });
  });  
  }

  self.deleteRecipe = function(ev, r) {
    var confirm = $mdDialog.confirm().title('Delete Confirmation')
      .content('Are you sure want to delete Product : '+r.p_title+' ?')
      .targetEvent(ev)
      .ok('OK').cancel('CANCEL');

    $mdDialog.show(confirm).then(function() {
      services.deleteRecipe(r.id).then(function(res){
        //console.log(JSON.stringify(res));
        if(res.status == 'success'){
          $mdToast.show($mdToast.simple().hideDelay(1000).content('Delete product '+r.p_title+' Success!').position('bottom right'))
          .then(function() {
            window.location.reload();
          });
        }else{
          $mdToast.show(
            $mdToast.simple().hideDelay(6000).action('CLOSE').content('Opps , Failed delete Product '+r.p_title).position('bottom right')
          ).then(function(response){

          });
        }        
      });
    }, function() {

    });

  };  

  self.editOrder = function(ev, ro) {
    $mdDialog.show({
      controller          : RorderControllerDialog,
      templateUrl         : 'templates/page/myorder/create.html',
      parent              : angular.element(document.body),
      targetEvent         : ev,
      clickOutsideToClose : false,
      rorder            	: ro
    })
  };
 });
   


function RorderControllerDialog($scope, $mdDialog, services, $mdToast, $route, rorder) {
  var self = $scope;
  var original ;

  self.title      = 'Order Details';
  self.buttonText = 'UPDATE';
  
     original = rorder;
     self.rorder = angular.copy(original);
	 
	  
	
	    qnt = rorder.quantity.split(",");
	     self.qnt=qnt;
		 
	   
	   price = rorder.prices.split(",");
	   self.price=price;
	   
	   item_names = rorder.item_names.split(",%,%");
	   self.item_names=item_names;
	  
	   
		 
    self.hide = function() {
    $mdDialog.hide();
  };

  self.cancel = function() {
    $mdDialog.cancel();
  };

  } 



app.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
});