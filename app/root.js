 angular.module('App').controller('RootCtrl', 
 function($scope, $mdSidenav, $mdToast, $mdDialog, services, $cookies,$interval) {

  var self = $scope;

  $scope.bgColor = '#d9d9d9';
  $scope.black = '#000000'; 
  
      self.currentPage =0;
	  self.pageSize=20;
	  
  
  self.data = {
    user: {
      name: $cookies.session_name,
      email: $cookies.session_email,
      token: $cookies.session_token,
	  icon: 'face'
    }
  };
  
   
  self.toggleSidenav = function() {
    $mdSidenav('left').toggle();
  };

  self.doLogout = function(ev){
    var confirm = $mdDialog.confirm().title('Logout Confirmation')
      .content('Are you sure want to logout from user : '+$cookies.session_name+' ?')
      .targetEvent(ev)
      .ok('OK').cancel('CANCEL');
    $mdDialog.show(confirm).then(function() {
      // clear session
      $cookies.session_uid = null;
      $cookies.session_name = null;
      $cookies.session_email = null;
      window.location.href = '#login';
      $mdToast.show($mdToast.simple().content('Logout Success').position('bottom right'));
    });
  }; 
  
  
   self.Expire_Session = function(ev){
      // clear session
      $cookies.session_uid = null;
      $cookies.session_name = null;
      $cookies.session_email = null;
	   window.location.href = '#login';
      $mdToast.show($mdToast.simple().content('Session Expired Please Login').position('bottom right'));
  };

  self.toast_click = function(message) {
    var toast = $mdToast.simple().content('You clicked ' + message).position('bottom right');
    $mdToast.show(toast);
  };

  self.toast = function(message) {
    var toast = $mdToast.simple().content(message).position('bottom right');
    $mdToast.show(toast);
  };

  self.toastList = function(message) {
    var toast = $mdToast.simple().content('You clicked ' + message + ' having selected ' + $scope.selected.length + ' item(s)').position('bottom right');
    $mdToast.show(toast);
  };
  
  self.selected = [];
  self.toggle = function(item, list) {
    var idx = list.indexOf(item);
    if (idx > -1) list.splice(idx, 1);
    else list.push(item);
  };

  self.directHref= function(href){
    self.toggleSidenav();
    window.location.href = href;
  };

	 
  self.sidenav = {
    actions: [ {
        name: 'HOME',
        icon: 'store_mall_directory',
        link: '#home'
      },{
        name: 'POS',
        icon: 'note_add',
        link: '#pos'
      },{
        name: 'REPORT',
        icon: 'note_add',
        link: '#report'
      },  {
        name: 'CATEGORY',
        icon: 'dns',
        link: '#category'
      }, {
        name: 'SUB-CATEGORY',
        icon: 'toc',
        link: '#subcategory/0'
      }, {
        name: 'TOPPING',
          icon: 'toc',
          link: '#topping'
      }, {
        name: 'PRODUCTS',
        icon: 'restaurant_menu',
        link: '#product/0'
      },{
        name: 'INGREDIENTS',
        icon: 'dns',
        link: '#ingredient'
      }, {
        name: 'STOCK HISTORY',
        icon: 'storage',
        link: '#stockhistory'
      }, {
        name: 'STOCK REPORT',
        icon: 'history',
        link: '#stockreport'
      },{
        name: 'PROFILE',
        icon: 'settings',
        link: '#setting'
      },{
        name: 'USERS',
        icon: 'group',
        link: '#user'
      },{
        name: 'ORDERS',
        icon: 'shopping_cart',
        link: '#myorder'
      }/*,{
        name: 'QUOTATION',
        icon: 'reorder',
        link: '#quotation'
      }*/]
 }
});