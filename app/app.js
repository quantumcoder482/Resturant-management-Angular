angular.module('App', [ 'ngMaterial', 'ngRoute', 'ngMessages', 'ngCookies', 'ngSanitize','textAngular', 'angularjs-datetime-picker']);

angular.module('App').config( 
  function($mdThemingProvider) {
    $mdThemingProvider.theme('default')
    .primaryPalette('deep-orange')
    .accentPalette('orange');
  }
);

angular.module('App').config(['$routeProvider', function($routeProvider) {
    $routeProvider.
	
	   when('/home', {
        templateUrl : 'templates/page/home/home.html',
        controller  : 'HomeController'
      }).
	   when('/pos', {
        templateUrl : 'templates/page/pos/pos.html',
        controller  : 'PosController'
      }).
	   when('/report', {
        templateUrl : 'templates/page/report/report.html',
        controller  : 'ReportController'
      }).
	  when('/product/:page', {
        templateUrl : 'templates/page/product/product.html',
        controller  : 'ProductController'
      }).
	   
	   when('/product_add/', {
        templateUrl : 'templates/page/product/product_new.html',
        controller  : 'ProductAddController'
      }).
	 
	  when('/product_edit/:id', {
        templateUrl : 'templates/page/product/product_edit.html',
        controller  : 'ProductEditController'
      }).
      
     when('/consumption/:id', {
        templateUrl : 'templates/page/consumption/consumption.html',
        controller  : 'ConsumptionController'
      }).
	  
	  when('/category', {
        templateUrl : 'templates/page/category/category.html',
        controller  : 'CategoryController'
      }).
	  
	  when('/ingredient', {
        templateUrl : 'templates/page/ingredients/ingredients.html',
        controller  : 'IngredientController'
      }).
	  
	   when('/subcategory', {
        templateUrl : 'templates/page/subcategory/subcategory.html',
        controller  : 'SubcategoryController'
      }).
	   when('/stock/:id', {
        templateUrl : 'templates/page/stock/stock.html',
        controller  : 'StockController'
      }).
     when('/stockhistory', {
        templateUrl : 'templates/page/stock/stockhistory.html',
        controller  : 'StockhistoryController'
      }).
     when('/stockreport', {
        templateUrl : 'templates/page/stock/stockreport.html',
        controller  : 'StockreportController'
      }).
	   when('/subcategory/:cat', {
        templateUrl : 'templates/page/subcategory/subcategory.html',
        controller  : 'SubcategoryController'
      }).
	  
	  when('/setting', {
        templateUrl : 'templates/page/setting/setting.html',
        controller  : 'SettingController'
      }). 
	   
	  when('/user', {
        templateUrl : 'templates/page/user/user.html',
        controller  : 'UserController'
      }).  
	  
	  when('/login', {
        templateUrl : 'templates/page/login/login.html',
        controller  : 'LoginController'
      }).
	    when('/myorder', {
        templateUrl : 'templates/page/myorder/myorder.html',
        controller  : 'RorderController'
      }).
	 
	  otherwise({
        redirectTo  : '/login'
      });
}]);

angular.module('App').run(function($location, $rootScope) {
  $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
    $rootScope.title = current.$$route.title;
  });
});
