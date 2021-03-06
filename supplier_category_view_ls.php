<?php
/*
 * @auther udzhou
 * @date 2013-1-31
 */
require_once 'class/supplier_category_service.class.php';
require_once 'class/supplier_category.class.php';
require_once 'class/sub_pages.class.php';

if(isset($_GET["p"]))
	$pageCurrent=$_GET["p"];  
else
  	$pageCurrent=1; 
//每页显示的条数  
$page_size=10;   	
$supplier_category_service=new SupplierCategoryService();
//总条目数 

//每次显示的页数  
$sub_pages=10; 

$url="index.php?mod=supplier_category&action=ls&p=";
if(isset($_GET["keywords"])){
		$keywords=$_GET["keywords"];
		$array_supplier_category=$supplier_category_service->listAllByKeywords($pageCurrent,$page_size,$keywords);
		$nums=$supplier_category_service->getListRowsByKeywords($keywords);
		if(!empty($keywords)){
			$url="index.php?mod=supplier_category&action=ls&keywords=$keywords&p=";
		}
  }else{
  $array_supplier_category=$supplier_category_service->listAll($pageCurrent,$page_size);
  $nums=$supplier_category_service->getListRows();
}
?>


<div class="search-form">
<form class="form-inline" action="./index.php" method="get">
   
  <input type="hidden" class="search-query" name="mod" value="supplier_category">
  <input type="hidden" class="search-query" name="action" value="ls">
  <input type="text" class="search-query" name="keywords" id="keywords">
  <button type="submit" class="btn">搜索</button>
</form>
</div>


<div class="datatable">

<ul class="nav nav-tabs">
  <li class="active">
    <a href="#">供应商分类管理</a>
   
  </li>
  <a type='button' class='btn btn-primary' href='./index.php?mod=supplier_category&action=add_get' style="float: right;">新增供应商分类</a>
</ul>

<table class="table table-bordered table-striped  table-hover" id="table">
  <thead>
      <tr>
        <th>类别</th>        
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($array_supplier_category as $supplier_category){
    	echo "<tr> 
    	<td>$supplier_category->supplier_category_name</td>        
       <td><a class='btn btn-primary' href='./index.php?mod=supplier_category&action=edit_get&eid=".$supplier_category->id."'>修改</a></td>
      </tr>";
    }
    
    
    ?>
      
    </tbody>
  </table>
  <?php
	  $subPages=new SubPages($page_size,$nums,$pageCurrent,$sub_pages,$url);
  ?>
</div>

<script>
  $(function () {
  				$('#keywords').val("<?php echo $_GET["keywords"]?>");
                     } );
</script>
