<?php
session_start();
@define('_source', '../sources/');
@define('_lib', '../lib/');

include_once _lib . "config.php";
include_once _lib . "constant.php";
include_once _lib . "functions.php";
include_once _lib . "library.php";
include_once _lib . "class.database.php";


$d = new database($config['database']);
$act=$_POST["act"];
switch($act){
	case 'giale':
		giale();
		break;
	case 'giakm':
		giakm();
		break;
	case 'update_stt':
		update_stt();
		break;
	case 'update_stt_ab':
		update_stt_ab();
		break;
	case 'load_level':
		load_level();
		break;
	case 'load_level_sp':
		load_level_sp();
		break;
	case 'load_option':
		load_option();
		break;
	case 'load_option_select':
		load_option_select();
		break;
	case 'load_brand':
		load_brand();
		break;	
}

function giale(){
	global $d;
	$val=$_POST["val"];
	$id=$_POST["id"];
	$d->reset();
	$data["gia"]=$val;
	$d->setTable("product");
	$d->setWhere("id",$id);
	$d->update($data);

}

function giakm(){
	global $d;
	$val=$_POST["val"];
	$id=$_POST["id"];
	$d->reset();
	$data["giakm"]=$val;
	$d->setTable("product");
	$d->setWhere("id",$id);
	$d->update($data);

}
function update_stt(){
	global $d;
	$val=$_POST["val"];
	$id=$_POST["id"];
	
	$d->reset();
	$data["stt"]=$val;
	$d->setTable("product_list");
	$d->setWhere("id",$id);
	$d->update($data);

}
function update_stt_ab(){
	global $d;
	$val=$_POST["val"];
	$id=$_POST["id"];
	$table=$_POST["table"];
	
	$d->reset();
	$data["stt"]=$val;
	$d->setTable($table);
	$d->setWhere("id",$id);
	$d->update($data);

}

function load_brand(){
	global $d;

		$id=$_POST["id"];
		$d->reset();
		$sql="select * from #_product_thuonghieu where id_parent='".$id."' order by stt desc";
		$d->query($sql);
		$rs=$d->result_array();
		
		$str ='<option value="">Chọn thương hiệu</option>';
		foreach($rs as $v){
			$str.='<option value="'.$v["id"].'" '; $str.='> '.$v["ten_vi"].'</option>';
		}
		echo $str;
}


function load_level(){
	global $d;
	$id_parent=explode("|",$_POST["id_parent"]);
	$level=$_POST["level"]+1;
	$type=$_POST["type"];
	$checktype=$_POST["checktype"];
	if($level<$type){
		$id=$_POST["id"];
		$d->reset();
		$sql="select * from #_product_list where com='".$level."' and id_parent='".$id."' and type = '".$checktype."' order by stt";
		$d->query($sql);
		$rs=$d->result_array();
		if($d->num_rows()>0){
		$str='<b class="mb-1 display-block">Choose Category Level '.$level.'</b>';
		$str.='<select name="id_parent[]" class="form-control input level'.$level.'" data-level="'.$level.'"  onchange="load_level($(this))"><option> Choose Category Level '.$level.' </option>';
		foreach($rs as $v){
			$str.='<option value="'.$v["id"].'" '; if($v["id"]==$id_parent[$level-1]) $str.="selected"; $str.='> '.$v["ten_vi"].'</option>';
		}
		$str.='</select><br /><div id="level'.($level+1).'" ></div>
		<script type="text/javascript">
		$(document).ready(function(){
			load_level($(".level'.$level.'"));
		})
		</script>';
		echo $str;
		}
	}
}
function load_level_sp(){
	global $d;
	$id_parent=explode(",",$_POST["id_parent"]);
	$max_level=$_POST["max_level"];
	$type1=$_POST["type1"];
	$level=$_POST["level"]+1;
	if($level<=$max_level){
		$id=$_POST["id"];
		$id_sp=$_POST["id_sp"];
		$d->reset();
		$sql="select * from #_product_list where com='".$level."' and id_parent='".$id."' and type = '".$type1."' order by stt";
		$d->query($sql);
		$rs=$d->result_array();
		if($d->num_rows()>0){
		$str='<b class="mb-1 display-block">Chọn danh mục cấp '.$level.'</b>';
		$str.='<select name="id_parent[]" class="form-control input level level'.$level.'" data-level="'.$level.'"  onchange="load_level($(this))"><option value=""> Chọn danh mục cấp '.$level.' </option>';
		foreach($rs as $v){
			$str.='<option value="'.$v["id"].'" '; if($v["id"]==$id_parent[$level-1]) $str.="selected"; $str.='> '.$v["ten_vi"].'</option>';
		}
		$str.='</select><br /><div id="level'.($level+1).'" ></div>
		<script type="text/javascript">
		$(document).ready(function(){
			load_level($(".level'.$level.'"));
			load_option($(".level'.$level.'"));
			$(".level'.$level.'").change(function(){
				$id=$(this).val();
				if($id!=0){
				$.ajax({
					type:"POST",
					url:"ajax/ajax.php",
					data:{id:$id, act: "load_option_select",id_sp:"'.$id_sp.'"},
					success: function(data){
						$("#option").html(data);
						$(".option").click();
					}
				})
				}
			})
		})
		</script>';
		echo $str;
		}
	}
}
function load_option(){
	global $d;
	
	$id=$_POST["id"];
	$id_sp=$_POST["id_sp"];
	$str='';
	
	$d->reset();
	$sql="select * from #_search_item where id_list='".$id."' order by id";
	$d->query($sql);
	$rs=$d->result_array();
	
	if($d->num_rows()>0){
		foreach($rs as $v){
		$str.='<div class="col-md-4"><b><strong style="color:#F00;">'.$v["ten"].'</strong></b><br />';
		$d->reset();
		$sql="select * from #_search where id_list='".$v["id"]."' order by id";
		$d->query($sql);
		$rs_option=$d->result_array();
			if($d->num_rows()>0){
				foreach($rs_option as $k){
					$str.='<label><input type="checkbox" '.checked($id_sp,$k["id"]).' name="option[]" value="'.$k["id"].'" /> '.$k["ten"].'</label><br />';
				}
			}
		$str.="</div>";
		}
	echo $str;
	}
}
function load_option_select(){
	global $d;
	
	$str='';
	
	$id=$_POST["id"];
	$id_sp=$_POST["id_sp"];
	
	$d->reset();
	$sql="select id_parent,id,set_level from #_product_list where id='".$id."' order by id";
	$d->query($sql);
	$rs_id=$d->fetch_array();

	if($rs_id["set_level"]!=''){
		if(preg_match("/|/",$rs_id["set_level"])){
			$arr=explode("|",$rs_id["set_level"]);
		}else{
			$arr=$rs_id["set_level"];
		}
		
		foreach($arr as $h){

			option($h,$str,$id_sp);
		
		}
		option($rs_id["id"],$str,$id_sp);
	}
	else{
		option($rs_id["id"],$str,$id_sp);
	}
	echo $str;
}
function option($h,$str, $id_sp){
	global $d;
	$d->reset();
	$sql="select * from #_search_item where id_list='".$h."' order by id";
	$d->query($sql);
	$rs=$d->result_array();
	
	if($d->num_rows()>0){
		foreach($rs as $v){
		$str.='<div class="col-md-4"><b><strong style="color:#F00;">'.$v["ten"].'</strong></b><br />';
		$d->reset();
		$sql="select * from #_search where id_list='".$v["id"]."' order by id";
		$d->query($sql);
		$rs_option=$d->result_array();
			if($d->num_rows()>0){
				foreach($rs_option as $k){
					$str.='<label><input type="checkbox" '.checked($id_sp,$k["id"]).' name="option[]" value="'.$k["id"].'" /> '.$k["ten"].'</label><br />';
				}
			}
		$str.="</div>";
		}
	
	}
	echo  $str;
}
function checked($id_sp,$id){
	global $d;
	$d->reset();
	$sql="select option_search from #_product where id='".$id_sp."' and find_in_set('".$id."',option_search)>0";
	$d->query($sql);
	$rs=$d->result_array();
	if($d->num_rows()>0){
		$kq= "checked";
	}
	return $kq;
}
?>