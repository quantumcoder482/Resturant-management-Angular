<?php
    require_once("Rest.inc.php");
	class API extends REST {
	
		public $data = "";
		

//		const DB_SERVER = "localhost";
//		const DB_USER = "root";                
//		const DB_PASSWORD = "";
//		const DB = "buzzbe6_dominee";


		const DB_SERVER = "localhost";
		const DB_USER = "buzzbtfx_buzzbee";                
		const DB_PASSWORD = "buzzbee123";
		const DB = "buzzbe6_dominee"; 


		private $db = NULL;
		private $mysqli = NULL;
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
		}
		
		/*
		 *  Connect to Database
		 */
		 
		   private function dbConnect(){
             $this->mysqli = new mysqli(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD, self::DB);
		 }
		
		
		/*
		 * Dynmically call the method based on the query string
		 */
		 
		 public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404); // If the method not exist with in this class "Page not found".
		 }
				
		/*
		 * ADMIN TRANSACTION
		 */
		 		
		private function login(){
			
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			} 
		
			$vendor = json_decode(file_get_contents("php://input"),true);
			$username = $vendor["username"];
			$password = $vendor["password"];
			if(!empty($username) and !empty($password)){ // empty checker
				$query="SELECT * FROM profile WHERE username = '$username' AND password = '".md5($password)."' LIMIT 1";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();
					$this->response($this->json($result), 200);
				}
				$this->response('', 204);	// If no records "No Content" status
			}
			$error = array('status' => "Failed", "msg" => "Invalid username or Password");
			$this->response($this->json($error), 400);
					}  
		
		
		private function users(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			$query="SELECT * FROM profile WHERE id=$id";
			$this->get_one($query);
		}

		  private function updateUsers(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$users = json_decode(file_get_contents("php://input"),true);
		
			$id = (int)$users['id'];
			$password = $users['profile']['password'];
			$users['profile']['title']=mysqli_real_escape_string($this->mysqli, $users['profile']['title']);
			$users['profile']['delivery_disclaimer']=mysqli_real_escape_string($this->mysqli, $users['profile']['delivery_disclaimer']);
			$users['profile']['product_disclaimer']=mysqli_real_escape_string($this->mysqli, $users['profile']['product_disclaimer']);
			
			if($password == '*****'){
				$column_names = array('id', 'title','owner','contact','email','address','product_disclaimer','delivery_disclaimer');
			}else{
				$users['profile']['password'] = md5($password);
				$column_names = array('id', 'title','owner','contact','email','address','product_disclaimer','delivery_disclaimer','password');
			}
			$table_name = 'profile';
			$this->post_update($id, $users, $column_names, $table_name);
		} 
		
		/*
		 * COMMON TRANSACTION
		 */
		   
	   private function Inline_Update(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$table =$this->_request['table'];
			$col =$this->_request['col'];
			$value =$this->_request['value'];
			$id =$this->_request['id'];
			
			$query="UPDATE $table SET $col= '$value' WHERE id=$id";
			$this->update_one($query);
		}
		
		
		private function Stock_Update(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id =$this->_request['id'];
			$value =$this->_request['value'];
			
			$query="UPDATE ingredients SET stock=stock - '$value' WHERE id=$id";
			$this->update_one($query);
		}
		
		
		 private function get_last_priority(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$table =$this->_request['table'];
			
			$query="select priority from $table order by priority DESC limit 1";
			$this->get_list($query);
		}
		
		
		   
		  private function GetCounter(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$query=" SELECT (SELECT COUNT(*) FROM category ) as table1Count, 
			(SELECT COUNT(*) FROM subcategory) as table2Count, 
			(SELECT COUNT(*) FROM product) as table3Count, 
			(SELECT COUNT(*) FROM user ) as table4Count"; 
			$this->get_list($query);
		   }
		
		   
		
		/*
		 * STOCK TRANSACTION
		 */		
		 
		 
		  private function get_Sales_Report(){	
		
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}	
			$sdate = $this->_request['sdate'].' 00:00:00';
			$edate = $this->_request['edate'].' 23:59:59';
			$type = $this->_request['type'];
			if($type == 5)
				$query="SELECT SUM(payable_amount) as total,MIN(payable_amount) as min, MAX(payable_amount) as max, COUNT(id) as count, SUM(order_cgst) as cgst, SUM(order_sgst) as sgst from myorder WHERE date >='$sdate' and date <= '$edate'";
			else 
				$query="SELECT SUM(payable_amount) as total,MIN(payable_amount) as min, MAX(payable_amount) as max, COUNT(id) as count, type, SUM(order_cgst) as cgst, SUM(order_sgst) as sgst from myorder WHERE date >='$sdate' and date <= '$edate' and type = '$type'";
			$this->get_list($query);
		}
		
		  private function get_Day_Sales_Report(){	
		
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}	
			$sdate = $this->_request['theday'].' 00:00:00';
			$edate = $this->_request['theday'].' 23:59:59';
			$query="SELECT * from myorder WHERE date >='$sdate' and date <= '$edate' ORDER BY date DESC";
			$this->get_list($query);
		}

		  private function get_Day_Stock_Report(){
		
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}	
			$sdate = $this->_request['from'].' 00:00:00';
			$edate = $this->_request['to'].' 23:59:59';
			$query="SELECT `stock`.*, `ingredients`.title, `ingredients`.stock, `ingredients`.unit FROM `stock` LEFT JOIN `ingredients` ON `stock`.ingredient = `ingredients`.id WHERE datetim >='$sdate' AND datetim <= '$edate' ORDER BY datetim DESC";
			$this->get_list($query);
		}		
		
		 private function get_ingredient_stock_history(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$id = (int)$this->_request['id'];
			$query="select s.*, i.title, i.stock, i.unit from stock s, ingredients i where s.ingredient='$id' and s.ingredient=i.id order by datetime DESC";
			$this->get_list($query);
		}		

		 private function get_ingredient_myone_stock_history(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$query="SELECT a.*, MAX(b.datetime) AS lasttime, b.quantity, b.stock_previous, b.remarks FROM `ingredients` a LEFT JOIN `stock` b ON a.id = b.ingredient GROUP BY a.id ORDER BY id ASC ";
			$this->get_list($query);
		}	

		 private function get_ideal_stockhistory(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$query="SELECT a.items AS product_id, a.quantity FROM `myorder` a WHERE DATE >= CAST(CURRENT_TIMESTAMP AS DATE)";// today order list
			$this->get_list($query);
		}	

		 private function get_consumption(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$query="SELECT * FROM `consumption`";// consumption
			$this->get_list($query);
		}

		private function insert_stock(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$stock = json_decode(file_get_contents("php://input"),true);
			$stock['remarks']=mysqli_real_escape_string($this->mysqli, $stock['remarks']);
			$column_names = array('ingredient', 'quantity', 'remarks','stock_previous','stock_actual');
			$table_name = 'stock';
			$this->post_one($stock, $column_names, $table_name);
		}

		private function updateopening(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$sql = "UPDATE `ingredients`, ( SELECT t.quantity, t.ingredient, t.stock_previous FROM (SELECT * , MAX(datetim) AS sss FROM `stock` GROUP BY ingredient ) r INNER JOIN `stock` t ON r.ingredient = t.ingredient AND r.sss = t.datetim) v SET `ingredients`.stock = v.quantity WHERE `ingredients`.id = v.ingredient";
			if ($this->mysqli->query($sql)) {	
				$msg 	= " Opening updated successfully";
				$resp = array('status' => 'success', "msg" => $msg, "data" => "");
				$this->response($this->json($resp), 200);
			} else {
				$this->response('',204);
			}
		}

		private function insert_stockhistory(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$stock = json_decode(file_get_contents("php://input"),true);

			$insert_Array = array();
			$column_names = array('ingredient', 'quantity', 'datetim', 'stock_previous', 'remarks', 'deliver', 'available', 'actual', 'ideal', 'varian');
			$table_name = 'stock';

			foreach ($stock as $item){
				if (array_key_exists("deliver",$item) || array_key_exists("ending",$item)){					
					$trr = array();
					$trr['ideal'] = $item['ideal'];					
					$trr['ingredient'] = $item['id'];
					if(!array_key_exists("deliver",$item)){
						$trr['available'] = $item['stock'];
						$trr['quentity'] = $item['stock'];
						$trr['deliver'] = 0;
					}else {						
						$trr['deliver'] = $item['deliver'];
						$trr['available'] = $item['deliver'] + $item['stock'];
						$trr['quantity'] = $item['deliver']+$item['stock'];
					}
					if(!array_key_exists("ending",$item)) {
						$trr['actual'] = $item['stock'];
						$trr['varian'] = $item['ideal'] - $item['stock'];
						$trr['quantity'] = $item['quantity'];
					}else {
						$trr['actual'] = $item['stock'] - $item['ending'];
						$trr['varian'] = $item['ideal'] - $item['stock'] + $item['ending'];
						$trr['quantity'] = $item['ending'];
					}
					$trr['stock_previous'] = $item['stock'];
					$trr['remarks']=mysqli_real_escape_string($this->mysqli, $item['remarks']);
					$trr['datetim'] = date("Y-m-d h:i:s");
					$insert_Array[] = $trr;
				}
			}
			$this->post_multi($insert_Array, $column_names, $table_name);
		}

		private function post_multi($objects, $column_names, $table_name){
			if(!empty($objects)){
				$count = 0;
				foreach($objects as $obj){
					$keys 		= array_keys($obj);
					$columns 	= '';
					$values 	= '';
					foreach($column_names as $desired_key){ // Check the recipe received. If blank insert blank into the array.
					  if(!in_array($desired_key, $keys)) {
					   	$$desired_key = '';
						}else{
							$$desired_key = $obj[$desired_key];
						}
						$columns 	= $columns.$desired_key.',';
						$values 	= $values."'".$$desired_key."',";
					}
					$query = "INSERT INTO ".$table_name." (".trim($columns,',').") VALUES(".trim($values,',').")";
					if ($this->mysqli->query($query)) {

					} else {
						$count ++;
					}
				}
				if($count==0){	
						$msg 	= $table_name." inserted successfully";
						$resp = array('status' => 'success', "msg" => $msg, "data" => $objects);
						$this->response($this->json($resp), 200);
				}
			} else {
				$this->response('',204);	//"No update" status
			}
		}

		private function update_stock(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$stock = json_decode(file_get_contents("php://input"),true);
			$id = (int)$stock['id'];
			$stock['stock']['remarks']=mysqli_real_escape_string($this->mysqli, $stock['stock']['remarks']);
			$column_names = array('ingredient', 'quantity', 'remarks','stock_previous');
			$table_name = 'stock';
			$this->post_update($id, $stock, $column_names, $table_name);
		}
		
		
		
	    /*
		 * ORDER TRANSACTION
		 */		
		 
		  
		   private function my_orders(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$query="SELECT * from myorder"; 
			$this->get_list($query);
		 }
		 
		 
		 private function last_order_id(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$query="SELECT id from myorder order by id DESC limit 1"; 
			$this->get_list($query);
		 }
		  
		  	private function order_by_id(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			$query="SELECT * FROM myorder where id='$id'";
			
			$this->get_list($query);
		}
		
			private function insert_order(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$myorder = json_decode(file_get_contents("php://input"),true);
			//print_r($myorder);
			$myorder['user_id'] = intval($myorder['user_id']);
			$myorder['contact_name']=mysqli_real_escape_string($this->mysqli, $myorder['contact_name']);
			$myorder['contact_address']=mysqli_real_escape_string($this->mysqli, $myorder['contact_address']);
			$myorder['order_comment']=mysqli_real_escape_string($this->mysqli, $myorder['order_comment']);
			$myorder['item_names']=mysqli_real_escape_string($this->mysqli, $myorder['item_names']);
			
			$column_names = array('items', 'quantity', 'prices','item_names','user_id','contact_name','contact_number','contact_address','bill_amount','payable_amount','order_comment','discount','cgst','sgst','order_cgst','order_sgst','type');
			$table_name = 'myorder';
			$this->post_one($myorder, $column_names, $table_name);
		}	

		private function deleteOrder(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			$table_name = 'myorder';
			$this->delete_one($id, $table_name);
		}

		private function deleteAllOrder(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$table_name = 'myorder';
			$this->delete_all($table_name);
		}	
        
	    /*
		 *  INGREDIENTS TRANSACTION
		 */		
		 
		 private function ingredients(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT * from ingredients";
			$this->get_list($query);
		} 
		
		private function ingredients_by_id(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			
			$query="SELECT * from ingredients where id='$id'";
			$this->get_list($query);
		}
		
		private function insert_ingredients(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$ingredients = json_decode(file_get_contents("php://input"),true);
			$ingredients['title']=mysqli_real_escape_string($this->mysqli, $ingredients['title']);
			$ingredients['unit']=mysqli_real_escape_string($this->mysqli, $ingredients['unit']);
			$column_names = array('title','unit','status');
			$table_name = 'ingredients';
			$this->post_one($ingredients, $column_names, $table_name);
		}		

		private function update_ingredients(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$ingredients = json_decode(file_get_contents("php://input"),true);
			$id = (int)$ingredients['id'];
			$ingredients['ingredients']['title']=mysqli_real_escape_string($this->mysqli, $ingredients['ingredients']['title']);
			$ingredients['ingredients']['unit']=mysqli_real_escape_string($this->mysqli, $ingredients['ingredients']['unit']);
			$column_names = array('title','unit','status','stock');
			$table_name = 'ingredients';
			$this->post_update($id, $ingredients, $column_names, $table_name);
		}

		
		/*
		 * INGREDIENTS CONSUMPTION TRANSACTION
		 */		
		 
		 private function ingredient_consumption_by_product(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$product = $this->_request['product'];
			$query="SELECT c.*,i.title,i.unit FROM consumption c, ingredients i where c.ingredient=i.id and c.product='$product'";
			$this->get_list($query);
		} 
		
		
		private function active_ingredient_consumption_by_product(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$product = $this->_request['product'];
			$quantity = $this->_request['quantity'];
			$iteration = $this->_request['iteration'];
			$query="SELECT i.*,c.consumption, '$quantity' as quantity,'$iteration' as iteration FROM ingredients i, consumption c where i.id=c.ingredient and c.product='$product'";
			$this->get_list($query);
		} 
		
        private function insert_consumption(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$consumption = json_decode(file_get_contents("php://input"),true);
			$column_names = array('product','ingredient','consumption','status');
			$table_name = 'consumption';
			$this->post_one($consumption, $column_names, $table_name);
		}		

		private function update_consumption(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$consumption = json_decode(file_get_contents("php://input"),true);
			$id = (int)$consumption['id'];
			$column_names = array('product','ingredient','consumption','status');
			$table_name = 'consumption';
			$this->post_update($id, $consumption, $column_names, $table_name);
		}


		
	    /*
		 * CATEGORY TRANSACTION
		 */		
		 
		 private function categories(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT DISTINCT c.id, c.title, c.priority, c.status FROM category c ORDER BY c.priority ASC";
			$this->get_list($query);
		} 
		
		
		 private function category_title(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$id = $this->_request['id'];
			$query="SELECT id,title FROM category where id='$id'";
			$this->get_list($query);
		} 
		
		
		private function active_categories(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT * from category where status='1'";
			$this->get_list($query);
		}  
		
		private function ProductCategories(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = $this->_request['id'];
			$query="SELECT distinct c.id, c.c_title, c.c_icon, c.priority, c.is_banner, c.c_status FROM category c WHERE c.id IN ($id) ORDER BY FIELD(id,$id)";
			$this->get_list($query);
		}
		
		
		private function insertCategory(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$category = json_decode(file_get_contents("php://input"),true);
			$category['title']=mysqli_real_escape_string($this->mysqli, $category['title']);
			$column_names = array('title','priority','status');
			$table_name = 'category';
			$this->post_one($category, $column_names, $table_name);
		}		

		private function updateCategory(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$category = json_decode(file_get_contents("php://input"),true);
			$id = (int)$category['id'];
			$category['category']['title']=mysqli_real_escape_string($this->mysqli, $category['category']['title']);
			$column_names = array('title','priority','status');
			$table_name = 'category';
			$this->post_update($id, $category, $column_names, $table_name);
		}

		private function deleteCategory(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			$table_name = 'category';
			$this->delete_one($id, $table_name);
		}
		
		
		/*
		 * SUBCATEGORY TRANSACTION
		 */		
		
		
		private function subcategory(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct s.id, s.title, s.priority, s.status, s.category, c.title as category_name FROM subcategory s, category c WHERE s.category=c.id order by s.priority ASC";
			$this->get_list($query);
		}  
		
		 private function subcategory_title(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$id = $this->_request['id'];
			$query="SELECT title FROM subcategory where id='$id'";
			$this->get_list($query);
		} 
		
		
		private function subcat_by_cat(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$cat =$this->_request['cat'];
		     $query="select * from subcategory where category='$cat'";
			
			$this->get_list($query);
		}
		
		
		private function subcat_by_cat_mul(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$cat =$this->_request['cat'];
			$query="SELECT distinct s.id, s.title,s.status FROM subcategory s WHERE s.category IN ($cat)";
			$this->get_list($query);
		}
		
		
		private function subcat_by_ids(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$ids =$this->_request['ids'];
			$query="SELECT distinct s.id, s.title FROM sub_category s WHERE s.id IN ($ids)";
			$this->get_list($query);
		}


		private function insertSubcat(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$subcat = json_decode(file_get_contents("php://input"),true);
			$subcat['title']=mysqli_real_escape_string($this->mysqli, $subcat['title']);
			$column_names = array('title', 'category', 'priority', 'status');
			$table_name = 'subcategory';
			$this->post_one($subcat, $column_names, $table_name);
		}
		

		private function updateSubcat(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$subcat = json_decode(file_get_contents("php://input"),true);
			$id = (int)$subcat['id'];
			$subcat['subcategory']['title']=mysqli_real_escape_string($this->mysqli, $subcat['subcategory']['title']);
			$column_names = array('title', 'category', 'priority', 'status');
			$table_name = 'subcategory';
			$this->post_update($id, $subcat, $column_names, $table_name);
		}
		

		private function deleteSubcat(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			$table_name = 'sub_category';
			$this->delete_one($id, $table_name);
		}
		
		
		/*
		 * Product TRANSACTION
		 */		
		 
		private function products(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT * from product";
			$this->get_list($query);
		}
		
		
		 private function product_title(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$id = $this->_request['id'];
			$query="SELECT title FROM product where id='$id'";
			$this->get_list($query);
		} 
       
	    
		private function product_by_id(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$id = $this->_request['id'];
			$query="SELECT * FROM product where id='$id'";
			$this->get_list($query);
		} 
        
		private function Product_by_key(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$key = $this->_request['key'];
			$query="SELECT distinct r.id, r.title, r.price,r.cgst,r.sgst FROM product r WHERE r.title LIKE '%$key%'";
			$this->get_list($query);
		}
		
		private function available_Product_by_key(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$key = $this->_request['key'];
			$query="SELECT distinct r.id, r.title, r.price,r.cgst,r.sgst,r.gst_price FROM product r WHERE r.status=1 and r.title LIKE '%$key%'";
			$this->get_list($query);
		}

		private function getOrderProduct(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = $this->_request['id'];
			$query="SELECT * FROM product  WHERE id IN ($id) ORDER BY FIELD(id,$id)";
			$this->get_list($query);
		}
		
		private function Product_by_category(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$category = (int)$this->_request['category'];
			$query="SELECT * FROM product WHERE category IN ($category)";
			$this->get_list($query);
		}
		
		
	  private function Product_by_subcat(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$subcat = (int)$this->_request['subcat'];
			$query="SELECT * FROM product WHERE FIND_IN_SET($subcat, subcat)";
			$this->get_list($query);
		}
		
		private function insertProduct(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$product = json_decode(file_get_contents("php://input"),true);
			$product['title']=mysqli_real_escape_string($this->mysqli, $product['title']);
			$product['quantity']=mysqli_real_escape_string($this->mysqli, $product['quantity']);
			$column_names = array('category', 'subcat', 'title', 'quantity','price','status','cgst','sgst','gst_price');
			$table_name = 'product';
			$this->post_one($product, $column_names, $table_name);
		}

		private function updateProduct(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$product= json_decode(file_get_contents("php://input"),true);
			$id = (int)$product['id'];
			$product['product']['title']=mysqli_real_escape_string($this->mysqli, $product['product']['title']);
			$product['product']['quantity']=mysqli_real_escape_string($this->mysqli, $product['product']['quantity']);
		    $column_names = array('category', 'subcat', 'title', 'quantity','price','status','cgst','sgst','gst_price');
			$table_name = 'product';
			$this->post_update($id, $product, $column_names, $table_name);
		}

		private function deleteProduct(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			$table_name = 'product';
			$this->delete_one($id, $table_name);
		}
    
		/*
		 * Customer TRANSACTION
		 */		
		private function customers(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT * FROM user  order by name ASC";
			$this->get_list($query);
		  }
		   
		  
		 private function customer_by_id(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			$query="SELECT distinct u.id, u.name, u.contact,  u.address FROM user u where u.id='$id'";
			
			$this->get_list($query);
		}

	     
		 	private function insertCustomer(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$cust = json_decode(file_get_contents("php://input"),true);
			$column_names = array('name', 'contact', 'email','address');
			$table_name = 'user';
			$this->post_one($cust, $column_names, $table_name);
		}
		
		
		  private function updateCustomer(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$cust = json_decode(file_get_contents("php://input"),true);
			$id = (int)$cust['id'];
			$column_names = array('name', 'contact', 'email','address');
		    $table_name = 'user';
			$this->post_update($id, $cust, $column_names, $table_name);
		}


         private function deleteUser(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			$table_name = 'user';
			$this->delete_one($id, $table_name);
		}
		
		
 		
		/*
		 * FILE TRANSACTION
		 */	
		 	
		 private function getBase64(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$data = file_get_contents("php://input");
			$type = 'jpg';
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			if(!empty($data)){
				$success = array('status' => "Success", "msg" => "Successfully.", "data" => $base64);
				$this->response($this->json($success), 200);
			}else{
				$this->response('',204);	// "No Content" status
			}
		}		

		private function uploadFileToUrl(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			//$file_name = $_POST['file_name'];
			print_r($_POST);
			$values = array_values($_POST);
			//$file_name = $_FILES['file_contents'];

			//$file_name = $this->_request['file_name'];

			$success = array('status' => "Success", "msg" => "Successfully.", "data" => null);
			$this->response($this->json($success), 200);
		}						
		
		/*
		 * ===================== API utilities # DO NOT EDIT ==============
		 */
		private function get_list($query){
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			
			$this->response('',204);	// If no records "No Content" status
		}
		
		private function get_one($query){
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			if($r->num_rows > 0) {
				$result = $r->fetch_assoc();	
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		
		private function update_one($query){
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			
			
			$success = array('status' => "Success", "msg" => "Successfully.", "data" => null);
			$this->response($this->json($success), 200);
		}

		private function post_one($obj, $column_names, $table_name){
			$keys 		= array_keys($obj);
			$columns 	= '';
			$values 	= '';
			foreach($column_names as $desired_key){ // Check the recipe received. If blank insert blank into the array.
			  if(!in_array($desired_key, $keys)) {
			   	$$desired_key = '';
				}else{
					$$desired_key = $obj[$desired_key];
				}
				$columns 	= $columns.$desired_key.',';
				$values 	= $values."'".$$desired_key."',";
			}
			$query = "INSERT INTO ".$table_name."(".trim($columns,',').") VALUES(".trim($values,',').")";
			//echo "QUERY : ".$query;
			if(!empty($obj)){
				//$r = $this->mysqli->query($query) or trigger_error($this->mysqli->error.__LINE__);
				if ($this->mysqli->query($query)) {
					$status = "success";	
			    	$msg 	= $table_name." created successfully";
			    	$insert_id = $this->mysqli->insert_id;
				} else {
					$status = "failed";	
			    	$msg 	= $this->mysqli->error.__LINE__;
				}
				$resp = array('status' => $status, "msg" => $msg, "data" => $obj, "insert_id" => $insert_id);
				$this->response($this->json($resp),200);
			}else{
				$this->response('',204);	//"No Content" status
			}
		}

		private function post_update($id, $obj, $column_names, $table_name){
		
			$keys = array_keys($obj[$table_name]);
			
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the recipe received. If key does not exist, insert blank into the array.
			  if(!in_array($desired_key, $keys)) {
			   	$$desired_key = '';
				}else{
					$$desired_key = $obj[$table_name][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
			$query = "UPDATE ".$table_name." SET ".trim($columns,',')." WHERE id=$id";
			
			
			if(!empty($obj)){
				// $r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if ($this->mysqli->query($query)) {
					$status = "success";	
			    $msg 		= $table_name." update successfully";
				} else {
					$status = "failed";	
			    $msg 		= $this->mysqli->error.__LINE__;
				}
				$resp = array('status' => $status, "msg" => $msg, "data" => $obj);
				$this->response($this->json($resp),200);
			}else{
				$this->response('',204);	// "No Content" status
			}
		}		

		private function delete_one($id, $table_name){
			if($id > 0){				
				$query="DELETE FROM ".$table_name." WHERE id = $id";
				if ($this->mysqli->query($query)) {
					$status = "success";	
			    $msg 		= "One record " .$table_name." successfully deleted";
				} else {
					$status = "failed";	
			    $msg 		= $this->mysqli->error.__LINE__;
				}
				$resp = array('status' => $status, "msg" => $msg);
				$this->response($this->json($resp),200);
			}else{
				$this->response('',204);	// If no records "No Content" status
			}
		}	

		private function delete_all($table_name){				
				$query="truncate table ".$table_name;
				if ($this->mysqli->query($query)) {
					$status = "success";	
			        $msg = "All record " .$table_name." successfully deleted";
				} else {
					$status = "failed";	
			    	$msg = $this->mysqli->error.__LINE__;
				}
				$resp = array('status' => $status, "msg" => $msg);
				$this->response($this->json($resp),200);
		}

		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data, JSON_NUMERIC_CHECK);
			}
		}
	}
	
	// Initiiate Library
	
	$api = new API;
	$api->processApi();
?>