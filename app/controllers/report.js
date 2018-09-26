var app=angular.module('App').controller('ReportController', 
function($rootScope, $scope, $http, $mdToast, $cookies, $mdDialog, $route, services){
  if($cookies.session_uid == 'null' || $cookies.session_uid == null){ $scope.Expire_Session(); }

	$rootScope.pagetitle = 'Sales Report';
	var self             = $scope;
  var root             = $rootScope;
  root.toolbar_menu = null;
  
  self.loading         = true;
	var today_report = {start_date : toISOLocal(new Date()).split("T")[0], end_date : toISOLocal(new Date()).split("T")[0], type : 5};
	self.dates = angular.copy(today_report);
	self.daytable = false;
	self.original = [];
	var daydata = [];
	var type_arr = ['DineIn','CarryOut','Delivery'];
	//console.log(toISOLocal(new Date()).split("T")[0]);
	services.getSalesReport(toISOLocal(new Date()).split("T")[0],toISOLocal(new Date()).split("T")[0], 5).then(function(data){
	   if (!data.data[0].hasOwnProperty('type')) {
			data.data[0].type = 'All';
		}else if (data.data[0].type == null) {
			data.data[0].type = '';
		}else {
			data.data[0].type = type_arr[data.data[0].type];
		}
		data.data[0].gst = Number((data.data[0].cgst+data.data[0].sgst).toFixed(2));
		data.data[0].total = Number(data.data[0].total.toFixed(2));
		self.report = data.data;
		services.getDaySalesReport(toISOLocal(new Date()).split("T")[0]).then(function(data){
			console.log(data.data);
			if(data.data != ''){
				var day_report;
				for (var i=0; i<data.data.length; i++){
					var itemarr = data.data[i].items.split(',');
					console.log(itemarr);
					for(var j=0; j<itemarr.length-1; j++){
						day_report = {item: itemarr[j], item_name: data.data[i].item_names.split(',%,%')[j], type: type_arr[data.data[i].type], price: data.data[i].prices.split(',')[j], quantity: data.data[i].quantity.split(',')[j], sgst: Number(data.data[i].sgst.split(',')[j]), cgst: Number(data.data[i].cgst.split(',')[j]), bill_amount: data.data[i].bill_amount, payable_amount: data.data[i].payable_amount, order_cgst: data.data[i].order_cgst, order_sgst: data.data[i].order_sgst, contact_address: data.data[i].contact_address, contact_name: data.data[i].contact_name, contact_number: data.data[i].contact_number, custom_amount: data.data[i].custom_amount, date: data.data[i].date.slice(11), discount: data.data[i].discount, order_comment: data.data[i].order_comment};
						daydata.push(day_report);
					}
				}
				self.daytable = true;
				self.daydatas = daydata;
				daydata = [];
			}
		});
	});
	
	self.get_sales_report = function(dates) {
		self.daytable = false;
		services.getSalesReport(dates.start_date,dates.end_date, dates.type).then(function(data){
		   if (!data.data[0].hasOwnProperty('type')) {
				data.data[0].type = 'All';
			}else if (data.data[0].type == null) {
				data.data[0].type = '';
			}else {
				data.data[0].type = type_arr[data.data[0].type];
			}
			data.data[0].gst = Number((data.data[0].cgst+data.data[0].sgst).toFixed(2));
			data.data[0].total = Number(data.data[0].total.toFixed(2));
			self.report = data.data;
			//alert(JSON.stringify(self.report[0].total, null, 4));
			if(dates.start_date == dates.end_date){
				services.getDaySalesReport(dates.start_date).then(function(data){
					console.log(data.data);
					if(data.data != ''){
						var day_report;
						for (var i=0; i<data.data.length; i++){
							var itemarr = data.data[i].items.split(',');
							console.log(itemarr);
							for(var j=0; j<itemarr.length-1; j++){
								day_report = {item: itemarr[j], item_name: data.data[i].item_names.split(',%,%')[j], type: type_arr[data.data[i].type], price: data.data[i].prices.split(',')[j], quantity: data.data[i].quantity.split(',')[j], sgst: Number(data.data[i].sgst.split(',')[j]), cgst: Number(data.data[i].cgst.split(',')[j]), bill_amount: data.data[i].bill_amount, payable_amount: data.data[i].payable_amount, order_cgst: data.data[i].order_cgst, order_sgst: data.data[i].order_sgst, contact_address: data.data[i].contact_address, contact_name: data.data[i].contact_name, contact_number: data.data[i].contact_number, custom_amount: data.data[i].custom_amount, date: data.data[i].date.slice(11), discount: data.data[i].discount, order_comment: data.data[i].order_comment};
								daydata.push(day_report);
							}
						}
						self.daytable = true;
						self.daydatas = daydata;
						daydata = [];
					}
				});
			}
		});
		
	}
	
    
   
     self.UpdateStatus = function(ev, c) {
	   var confirm = $mdDialog.confirm().title('Confirmation?')
      .content('Are you sure want to update Status?')
      .targetEvent(ev)
      .ok('OK').cancel('CANCEL');

      $mdDialog.show(confirm).then(function() {
        $mdToast.show($mdToast.simple().content("Process...").position('bottom right'));
        if(c.status==1){
		  c.status=0;
		 } else {
		 c.status=1;	 
		 }
        $mdToast.show($mdToast.simple().content("Process...").position('bottom right'));
         services.updateCategory(c.id, c).then(function(resp){
			 self.afterSubmit(resp);
         });
       }, function() {
     });
  };  
  
	
  self.deleteCategory = function(ev, c) {
    var confirm = $mdDialog.confirm().title('Delete Confirmation')
      .content('Deleting category not allowed?')
      .targetEvent(ev)
      .ok('OK').cancel('CANCEL');
      $mdDialog.show(confirm).then(function() {
      /*  services.deleteCategory(c.id).then(function(res){
        if(res.status == 'success'){
          $mdToast.show($mdToast.simple().hideDelay(1000).content('Delete Category '+c.title+' Success!').position('bottom right'))
          .then(function() {
            window.location.reload();
          });
        }else{
          $mdToast.show(
            $mdToast.simple().hideDelay(6000).action('CLOSE').content('Opps , Failed delete category '+c.title).position('bottom right')
          ).then(function(response){

          });
        }
       });*/ 
      }, function() {
    });
  };  
  
  
  
  self.addCategory = function(ev) {
    $mdDialog.show({
      controller          : CategoryControllerDialog,
      templateUrl         : 'templates/page/category/create.html',
      parent              : angular.element(document.body),
      targetEvent         : ev,
      clickOutsideToClose : false,
      category            : null
    })
  };

  self.editCategory = function(ev, c) {
    $mdDialog.show({
      controller          : CategoryControllerDialog,
      templateUrl         : 'templates/page/category/create.html',
      parent              : angular.element(document.body),
      targetEvent         : ev,
      clickOutsideToClose : false,
      category            : c
    })
  };   

  self.viewBanner = function(ev, c){
    $mdDialog.show({
      controller          : CategoryViewBannerControllerDialog,
      template            : '<md-dialog ng-cloak >' +
                            '  <md-dialog-content style="max-width:800px;max-height:810px;" >' +
                            '   <img style="margin: auto; max-width: 100%; max-height= 100%;" ng-src="uploads/category/{{category.icon}}">' +
                            '  </md-dialog-content>' +
                            '</md-dialog>',
      parent              : angular.element(document.body),
      targetEvent         : ev,
      clickOutsideToClose : true,
      category            : c
    })
  };   
});

function CategoryViewBannerControllerDialog($scope, $mdDialog, $mdToast, category) {
  var self = $scope;
  self.category = category;
 }

function CategoryControllerDialog($scope, $mdDialog, services, $mdToast, $route, category) {
  var self = $scope;
  var isNew = (category == null) ? true : false;
  var original ;
  //self.dir    = "../../uploads/category/";
  
  self.title      = (isNew) ? 'Add Category' : 'Edit Category';
  self.buttonText = (isNew) ? 'SAVE' : 'UPDATE';
 
  if (isNew) {
    //self.bannerInvalid = true;
    original = { title :null, priority:1, status:1 };
    self.category = angular.copy(original);
	
	   services.GetLastPriority('category').then(function(resp){
		   self.category.priority=resp.data[0].priority+1;
		   //alert(self.category.priority);
        });             
     
  } else {
    //self.bannerInvalid = false;
    original = category;
    self.category = angular.copy(original);
  }
  

    
  self.isClean = function() {
    return angular.equals(original, self.category);
  }

  self.hide = function() {
    $mdDialog.hide();
  };

  self.cancel = function() {
    $mdDialog.cancel();
  };


   self.submit = function(c) {
	  self.loader = true;
	  
	 $mdToast.show($mdToast.simple().content("Process...").position('bottom right'));
     if(isNew){
           services.insertCategory(c).then(function(resp){
              self.afterSubmit(resp);
          });
    }else {
        services.updateCategory(c.id, c).then(function(resp){
          self.afterSubmit(resp);
        });             
     }
  };

  self.afterSubmit = function(resp) {
    if(resp.status == 'success'){
      $mdToast.show($mdToast.simple().hideDelay(1000).content(resp.msg).position('bottom right'))
      .then(function() {
        $mdDialog.hide();
        window.location.reload();    
      });  
    }else{
      $mdToast.show($mdToast.simple().hideDelay(3000).content(resp.msg).position('bottom right'))
    }    
  };
}
function toISOLocal(d) {
  var z = n => (n<10? '0':'')+n;
  var off = d.getTimezoneOffset();
  var sign = off < 0? '+' : '-';
  off = Math.abs(off);

  return d.getFullYear() + '-' + z(d.getMonth()+1) + '-' +
         z(d.getDate()) + 'T' + z(d.getHours()) + ':'  + z(d.getMinutes()) + 
         ':' + z(d.getSeconds()) + sign + z(off/60|0) + z(off%60); 
}
