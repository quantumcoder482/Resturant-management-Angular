angular.module('App').factory("services", function($http) {
  var serviceBase = 'app/services/'
  var obj = {};
  
  // Admin Login
  obj.doLogin = function (userdata) {
    return $http.post(serviceBase + 'login', userdata).then(function (results) {
	
      return results;
    });
  }; 
  
  obj.getUsers = function(id){
    return $http.get(serviceBase + 'users?id=' + id);
  }
  
  obj.updateUsers = function (id, profile) {
    return $http.post(serviceBase + 'updateUsers', {id:id, profile:profile}).then(function (status) {
        return status.data;
    });
  }; 
  
  //////// COMMOM TRANSACTION /////////////
  
   obj.Inline_Update = function(table, col, value, id){
    return $http.get(serviceBase + 'inline_update?table=' + table + ' && col=' + col+ ' && value='+ value + ' && id='+ id);
  }
  
  obj.StockUpdate = function(id,value){
    return $http.get(serviceBase + 'Stock_Update?value='+ value + ' && id='+ id);
  }
  
   obj.GetLastPriority = function(table){
    return $http.get(serviceBase + 'get_last_priority?table=' + table);
  }
  
  obj.GetCounter = function(){
    return $http.get(serviceBase + 'GetCounter');
  } 
  
  obj.generateInvoice = function (build) {
    return $http.post('api/build_order.php', build).then(function (status) {
        return status.data;
    });
  };
 
    obj.generateQuotation = function (build) {
    return $http.post('api/build_quotation.php', build).then(function (status) {
		alert(JSON.stringify(status, null, 4));
	
        return status.data;
    });
  };
	
  
  
  // Stock Transaction
  
  obj.getSalesReport = function(sdate, edate, type){
	   // alert(JSON.stringify(sdate, null, 4));
    return $http.get(serviceBase + 'get_Sales_Report?sdate=' + sdate + ' && edate='+ edate +'&& type='+ type);
  }

  obj.getDaySalesReport = function(theday){
	   // alert(JSON.stringify(sdate, null, 4));
    return $http.get(serviceBase + 'get_Day_Sales_Report?theday=' + theday);
  }

  obj.getDayStockReport = function(from, to){
	   // alert(JSON.stringify(sdate, null, 4));
    return $http.get(serviceBase + 'get_Day_Stock_Report?from=' + from + '&to=' + to);
  }
  
  obj.getIngredientStockHistory = function(id){
    return $http.get(serviceBase + 'get_ingredient_stock_history?id=' + id);
  }

  obj.getIdealStockHistory = function(){
    return $http.get(serviceBase + 'get_ideal_stockhistory');
  }

  obj.getconsumption = function(){
    return $http.get(serviceBase + 'get_consumption');
  }

  obj.updateopening = function(){
    return $http.get(serviceBase + 'updateopening');
  }

  obj.getIngredientmyoneStockHistory = function(){
    return $http.get(serviceBase + 'get_ingredient_myone_stock_history');
  }

  obj.insertStock = function (stock) {
    return $http.post(serviceBase + 'insert_stock', stock).then(function (status) {
      return status.data;
    });
  };
  
   obj.updateStock = function (id, stock) {
    return $http.post(serviceBase + 'update_stock', {id:id, stock:stock}).then(function (status) {
        return status.data;
    });
  };

   // Order Transaction
   
    obj.getMyorders = function(){
    return $http.get(serviceBase + 'my_orders');
  }
  
   obj.getLastOrderId = function(){
    return $http.get(serviceBase + 'last_order_id');
  }
  
   obj.getOrderByID = function(id){
    return $http.get(serviceBase + 'order_by_id?id=' + id);
  }


   obj.insertOrder = function (myorder,orderNoReset) {
    return $http.post(serviceBase + 'insert_order', {order:myorder,orderNoReset:orderNoReset}).then(function (status) {
		//alert(JSON.stringify(status, null, 4));
	
      return status.data;
    });
  };

  obj.deleteOrder = function (id) {
    return $http.delete(serviceBase + 'deleteOrder?id=' + id).then(function (status) {
        return status.data;
    });
  };

  obj.deleteAllOrder = function () {
    return $http.delete(serviceBase + 'deleteAllOrder').then(function (status) {
        return status.data;
    });
  };

   obj.insertStockhistory = function (stock) {
    return $http.post(serviceBase + 'insert_stockhistory', stock).then(function (status) {
    //alert(JSON.stringify(status, null, 4));
  
      return status.data;
    });
  }; 
  // Ingredient Transaction
  
  obj.getIngredients = function(){
    return $http.get(serviceBase + 'ingredients');
  }
  
  obj.getIngredientsByID = function(id){
    return $http.get(serviceBase + 'ingredients_by_id?id=' + id);
  }
  
  
  obj.insertIngredient = function (ingredients) {
    return $http.post(serviceBase + 'insert_ingredients', ingredients).then(function (status) {
		//alert(JSON.stringify(status, null, 4));
      return status.data;
    });
  };

  obj.updateIngredient = function (id, ingredients) {
    return $http.post(serviceBase + 'update_ingredients', {id:id, ingredients:ingredients}).then(function (status) {
        return status.data;
    });
  };

  // Topping Transaction

  obj.getToppings = function () {
    return $http.get(serviceBase + 'toppings');
  }

  obj.getToppingByID = function (id) {
    return $http.get(serviceBase + 'toppings_by_id?id=' + id);
  }


  obj.insertTopping = function (toppings) {
    return $http.post(serviceBase + 'insert_toppings', toppings).then(function (status) {
      //alert(JSON.stringify(status, null, 4));
      return status.data;
    });
  };

  obj.updateTopping = function (id, toppings) {
    return $http.post(serviceBase + 'update_toppings', {
      id: id,
      toppings: toppings
    }).then(function (status) {
      return status.data;
    });
  };


  // update ingredient
  
obj.updateIngredientByTopping=function (id,amount) {
  return $http.post(serviceBase + 'updateIngredientBytopping',{id:id, amount:amount}).then(function (status) {
    return status.data;
  });
}


 // Product Ingredient Consumption Transaction
  
  obj.ProductIngredientConsumption = function(product){
    return $http.get(serviceBase + 'ingredient_consumption_by_product?product=' + product);
  } 
   
  obj.ActiveProductIngredientConsumption = function(product,quantity,iteration){
    return $http.get(serviceBase + 'active_ingredient_consumption_by_product?product=' + product +'&quantity=' + quantity +'&iteration=' + iteration);
  } 

 
  obj.insertConsumption = function (consumption) {
    return $http.post(serviceBase + 'insert_consumption', consumption).then(function (status) {
      return status.data;
    });
  };

  obj.updateConsumption = function (id, consumption) {
    return $http.post(serviceBase + 'update_consumption', {id:id, consumption:consumption}).then(function (status) {
        return status.data;
    });
  };


  // Category Transaction
  
  obj.getCategories = function(){
    return $http.get(serviceBase + 'categories');
  }
  
  obj.CategoryTitleById = function(id){
    return $http.get(serviceBase + 'category_title?id=' + id);
  }
  
   obj.ActiveCategories = function(){
    return $http.get(serviceBase + 'active_categories');
  }
  
    obj.ProductCategories = function(id){
    return $http.get(serviceBase + 'ProductCategories?id=' + id);
  }
  
  
  obj.insertCategory = function (category) {
    return $http.post(serviceBase + 'insertCategory', category).then(function (status) {
      return status.data;
    });
  };

  obj.updateCategory = function (id, category) {
    return $http.post(serviceBase + 'updateCategory', {id:id, category:category}).then(function (status) {
        return status.data;
    });
  };

  obj.deleteCategory = function (id) {
    return $http.delete(serviceBase + 'deleteCategory?id=' + id).then(function (status) {
        return status.data;
    });
  };
  
   
   // Subcategory Transaction
    
  obj.getSubcategory = function(){
    return $http.get(serviceBase + 'subcategory');
  }
  
  obj.SubCategoryTitleById = function(id){
    return $http.get(serviceBase + 'subcategory_title?id=' + id);
  }

  obj.getSubcategory_by_cat = function(cat){
    return $http.get(serviceBase + 'subcat_by_cat?cat=' + cat);
  }
  
  obj.getSubcategory_by_cat_multiple = function(cat){
    return $http.get(serviceBase + 'subcat_by_cat_mul?cat=' + cat);
  }
  
    obj.getSubcategory_by_id = function(ids){
    return $http.get(serviceBase + 'subcat_by_ids?ids=' + ids);
  }


  obj.insertSubcat = function (subcat) {
    return $http.post(serviceBase + 'insertSubcat', subcat).then(function (results) {
        return results.data;
    });
  };

	obj.updateSubcat = function (id, subcategory) {
    return $http.post(serviceBase + 'updateSubcat', {id:id, subcategory:subcategory}).then(function (status) {
	//	alert(JSON.stringify(status.data, null, 4));
        return status.data;
		
    });
	};

	obj.deleteSubcat = function (id) {
    return $http.delete(serviceBase + 'deleteSubcat?id=' + id).then(function (status) {
        return status.data;
    });
	};
	
	
	// Product transaction 
	
  obj.getProducts = function(){
    return $http.get(serviceBase + 'products');
  }  
  
  obj.ProductTitleById = function(id){
    return $http.get(serviceBase + 'product_title?id=' + id);
  }
  
  obj.ProductById = function(id){
    return $http.get(serviceBase + 'product_by_id?id=' + id);
  }
  
  obj.GetProducts_by_keyword = function(keyword){
    return $http.get(serviceBase + 'Product_by_key?key=' + keyword);
  }  
  
  obj.Get_Stock_Products_by_keyword = function(keyword){
    return $http.get(serviceBase + 'available_Product_by_key?key=' + keyword);
  }  
  
  obj.getProduct_by_Category = function(category){
    return $http.get(serviceBase + 'Product_by_category?category=' + category);
  }
  
  obj.getProduct_by_subcat = function(subcat){
    return $http.get(serviceBase + 'Product_by_subcat?subcat=' + subcat);
  }

  obj.getOrderProduct = function(id){
	   // alert(JSON.stringify(id, null, 4));
    return $http.get(serviceBase + 'getOrderProduct?id=' + id);
  }

  obj.insertProduct = function (product) {
    return $http.post(serviceBase + 'insertProduct', product).then(function (results) {
        return results.data;
    });
  };

	obj.updateProduct = function (id, product) {
    return $http.post(serviceBase + 'updateProduct', {id:id, product:product}).then(function (status) {
        return status.data;
    });
	};

	obj.deleteProduct = function (id) {
    return $http.delete(serviceBase + 'deleteProduct?id=' + id).then(function (status) {
        return status.data;
    });
	};
	
  	
	// Users
    obj.getCustomers = function(){
    return $http.get(serviceBase + 'customers');
  }
    
	
	  obj.getCustomer_by_id = function(id){
    return $http.get(serviceBase + 'customer_by_id?id=' + id);
  }
	
	 obj.insertCustomer = function (u) {
    return $http.post(serviceBase + 'insertCustomer', u).then(function (results) {
        return results.data;
    });
  };

	 obj.updateCustomer = function (id, user) {
		 // alert(JSON.stringify(user, null, 4));
    return $http.post(serviceBase + 'updateCustomer', {id:id, user:user}).then(function (status) {
        return status.data;
    });
	};

    obj.deleteUser = function (id) {
     return $http.delete(serviceBase + 'deleteUser?id=' + id).then(function (status) {
        return status.data;
    });
	};

    
 	
	
  
  obj.uploadFileToUrl = function(f, dir, name){
    var fd = new FormData();
    fd.append("file", f);
    fd.append("target_dir", dir);
    fd.append("file_name", name);
    var request = {
        method  : 'POST',
        url     : 'app/uploader/uploader.php',
        data    : fd,
        headers : { 'Content-Type': undefined }
    };

    // SEND THE FILES.
    return $http(request).then(function (resp) {
      //console.log(JSON.stringify(resp.data));
      return resp.data;
    });
  };  

  obj.getBase64 = function(f){
    return $http.post(serviceBase + 'getBase64', f).then(function (status) {
        //console.log(JSON.stringify(status.data));
        return status.data;
    });
  };  

  return obj;   
});