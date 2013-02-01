<?php 
/*
 * @auther lion
 * @data 2013-1-30
 */
require_once './class/log_service.class.php';
$log_service=new LogService();
if(isset($_GET['action'])){
	$action=$_GET['action'];
	switch ($action){
		case 'ls':
			require_once './user_view_ls.php';
			break;
		case 'user_validate':
		    if(!imageCodeCheck()){
	    	$_SESSION['image_code_error']=true;
	    	$url="./login.php";
	   		}else{
	   		require_once 'class/user.class.php';
	  		require_once 'class/user_service.class.php';	
	  		$username=trim($_POST['username']);
			$pwd=md5(trim($_POST['pwd']));
			$user=new User(null,null,null,null,$username,$pwd,null,null,null,null);
			$user_service=new UserService();
			$rs=$user_service->validateUser($user);
			
			if(!$rs){
				$url="./login.php";
				
				
				$_SESSION['error']=true;
			}else{
				$url="./index.php";
				$user=$user_service->getUserByUsername($username);
				$_SESSION['username']=$username;
				$_SESSION['logined_user']=serialize($user);
				
			if(isset($_POST['auto_login'])){ 
					$auto_login=$_POST['auto_login'];
  				  	setcookie("username",$username,time()+36000);
    		    	setcookie("pwd",$pwd,time()+36000);
				}
				}
	  	 	 }
    	 	header("Location: $url");
			break;
		case 'user_register':
			require_once 'class/user.class.php';
	  		require_once 'class/user_service.class.php';
	  		$name=trim($_POST["name"]);
	  		$category_name_id=trim($_POST["category_name_id"]);
	  		$username=trim($_POST["username"]);
	  		$pwd=md5(trim($_POST["pwd"]));
	  		$telephone=trim($_POST["telephone"]);
	  		$email=trim($_POST["email"]);
	  		$user=new User(null, $name, $category_name_id, null, $username, $pwd, $telephone, $email, null, null);
	  		$user_service=new UserService();
	  		$id=$user_service->addUser($user);

			$_SESSION['register']=true;
			
			$log_service->addLog("insert","人员",$id);
			
			header("Location: ./login.php");
			break;
	
		case 'edit_get':
			if(isset($_GET['eid'])){
			$id=$_GET['eid'];
			if(is_numeric($id)){
			require_once 'class/user.class.php';
	  		require_once 'class/user_service.class.php';
			$user_service=new UserService();
			$user=$user_service->getUserById($id);
			$user=serialize($user);
			$_SESSION['user']=$user;
			require_once './user_view_edit_form.php';
			}else{
				header("Location: ./index.php?mod=user&action=ls");
			}
			}else{
				header("Location: ./index.php?mod=user&action=ls");
			}
			break;
		case 'edit_post':
			if(isset($_SESSION["id"])){
				require_once 'class/user.class.php';
	  			require_once 'class/user_service.class.php';			
				$id=$_SESSION["id"];
				unset($_SESSION["id"]);
				$name=trim($_POST["name"]);
	  			$category_name_id=trim($_POST["category_name_id"]);
	  			$username=trim($_POST["username"]);
	  			$telephone=trim($_POST["telephone"]);
	  			$email=trim($_POST["email"]);
	  			$state=trim($_POST["state"]);
	  			$note=trim($_POST["note"]);
	  			$user=new User($id, $name, $category_name_id, null, $username, null, $telephone, $email, $state, $note);
	  			$user_service=new UserService();
	  			$user_service->updateUser($user);
	  			
				$_SESSION['operation']=true;
				$_SESSION['operation_msg']="修改人员:".$username."成功";		

				$log_service->addLog("update","人员",$id);
			}else{
				$_SESSION['operation']=false;
				$_SESSION['operation_msg']="修改人员:".$username."失败";
				
			}
			header("Location: ./index.php?mod=user&action=ls");
			break;
		case 'detail':
			if(isset($_GET['did'])){
			$id=$_GET['did'];
			if(is_numeric($id)){
			require_once 'class/user.class.php';
	  		require_once 'class/user_service.class.php';
			$user_service=new UserService();
			$user=$user_service->getUserById($id);
			$user=serialize($user);
			$_SESSION['user']=$user;
			require_once './user_view_detail.php';
			}else{
				header("Location: ./index.php?mod=user&action=ls");
			}
			}else{
				header("Location: ./index.php?mod=user&action=ls");
			}
			break;
		case 'logout':
			if ($_SESSION['logined_user']){
			unset($_SESSION['logined_user']);
			}
			if(isset($_SESSION['username'])){
				unset($_SESSION['username']);
			}
			setcookie("name");
			setcookie("pwd");
			header("Location: ./login.php");
			break;
		case 'pwd_change_get':
			require_once './user_view_pwd_change.php';
			break;
		case 'pwd_change_post':
			require_once 'class/user.class.php';
	  		require_once 'class/user_service.class.php';	
	  		$username=trim($_POST['username']);
			$pwd=md5(trim($_POST['pwd']));
			$user=new User(null,null,null,null,$username,$pwd,null,null,null,null);
			$user_service=new UserService();
			$rs=$user_service->validateUser($user);
			if($rs){
				$user=$user_service->getUserByUsername($username);
				$user->pwd=md5($_POST["new_pwd"]);
				print_r($user);
				$user_service->updateUser($user);
				
				$_SESSION['operation']=true;
				$_SESSION['operation_msg']="修改密码:".$username."成功";	
				
				$log_service->addLog("update","用户修改密码",$id);
				
				header("Location: ./index.php?mod=home");
				
			}else {
				
				$_SESSION['operation']=false;
				$_SESSION['operation_msg']="密码错误，请重新输入";	
				
				header("Location: index.php?mod=user&action=pwd_change_get");
			}
			break;
		default:
			break;		
	}
}else{
	header("Location: ./index.php?mod=user&action=ls");
}

function imageCodeCheck(){
	$input_image_code=$_POST['input_image_code'];
	$image_code=$_SESSION['$image_code'];
	if(strcasecmp($input_image_code , $image_code)==0){
		return true;
	}else return false;
}
?>