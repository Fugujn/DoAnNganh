<?php

if (!defined('_source'))
    die("Error");

$act = (isset($_REQUEST['act'])) ? addslashes($_REQUEST['act']) : "";

switch ($act) {

    #===================================================	
    case "man_cat":
        get_cats();
        guitin();
        $template = "dknhantin/cats";
        break;
    case "guitin":
        guitin();
        $template = "dknhantin/cats";
        break;

    case "add_cat":
        $template = "dknhantin/cat_add";
        break;
    case "edit_cat":
        get_cat();
        $template = "dknhantin/cat_add";
        break;
    case "save_cat":
        save_cat();
        break;
    case "delete_cat":
        delete_cat();
        break;

    default:
        $template = "index";
}

function fns_Rand_digit($min, $max, $num) {
    $result = '';
    for ($i = 0; $i < $num; $i++) {
        $result.=rand($min, $max);
    }
    return $result;
}

//===========================================================
function get_cats() {
    global $d, $items, $paging;



    $sql = "select * from #_dknhantin where type = 1  order by id desc";
    $d->query($sql);
    $items = $d->result_array();

    $curPage = isset($_GET['curPage']) ? $_GET['curPage'] : 1;
    $url = "index.php?com=dknhantin&act=man_cat";
    $maxR = 10;
    $maxP = 4;
    $paging = paging($items, $url, $curPage, $maxR, $maxP);
    $items = $paging['source'];
}

function get_cat() {
    global $d, $item;
    $id = isset($_GET['id']) ? themdau($_GET['id']) : "";
    
    
    if ($_REQUEST['id'] != '') {
        $id_up = $_REQUEST['id'];
        $sql_sp = "SELECT id,hienthi FROM table_dknhantin where id='" . $id_up . "' ";
        $d->query($sql_sp);
        $cats = $d->result_array();
        $hienthi = $cats[0]['hienthi'];
        if ($hienthi == 0) {
            $dataht['hienthi'] = 1; 
            $d->setTable('dknhantin');
            $d->setWhere('id', $id_up);
            if ($d->update($dataht)){}
            /*$sqlUPDATE_ORDER = "UPDATE table_dknhantin SET hienthi =1 WHERE  id = " . $id_up . "";
            $resultUPDATE_ORDER = mysql_query($sqlUPDATE_ORDER) or die("Not query sqlUPDATE_ORDER");*/
        } 
    }
    
    
    if (!$id)
        transfer("Không nhận được dữ liệu", "index.php?com=dknhantin&act=man_cat");

    $sql = "select * from #_dknhantin where id='" . $id . "'";
    $d->query($sql);
    if ($d->num_rows() == 0)
        transfer("Dữ liệu không có thực", "index.php?com=dknhantin&act=man_cat");
    $item = $d->fetch_array();
}

function save_cat() {
    global $d;
    $file_name_item = fns_Rand_digit(0, 9, 5);
    if (empty($_POST))
        transfer("Không nhận được dữ liệu", "index.php?com=dknhantin&act=man_cat");
    $id = isset($_POST['id']) ? themdau($_POST['id']) : "";
    if ($id) {
        $data['email'] = $_POST['ten_vi'];
        $data['ten'] = $_POST['ten'];
        $data['dienthoai'] = $_POST['dienthoai'];
        $data['ten_en'] = $_POST['ten_en'];
        //$data['id_item'] = $_POST['id_item'];
        $data['tenkhongdau'] = changeTitle($_POST['ten_vi']);
        //$data['keywords'] = $_POST['keywords'];	
        //$data['description'] = $_POST['description'];
        $data['stt'] = $_POST['stt'];
        $data['hienthi'] = isset($_POST['hienthi']) ? 1 : 0;
        $data['ngaysua'] = time();
        $d->setTable('dknhantin');
        $d->setWhere('id', $id);
        if ($d->update($data))
            redirect("index.php?com=dknhantin&act=man_cat&curPage=" . $_REQUEST['curPage'] . "");
        else
            transfer("Cập nhật dữ liệu bị lỗi", "index.php?com=dknhantin&act=man_cat");
    }else {
        $data['email'] = $_POST['ten_vi'];
        $data['ten_en'] = $_POST['ten_en'];
        $data['ten'] = $_POST['ten'];
        $data['dienthoai'] = $_POST['dienthoai'];
        $data['tenkhongdau'] = changeTitle($_POST['ten_vi']);
        $data['stt'] = $_POST['stt'];
        //$data['keywords'] = $_POST['keywords'];	
        //$data['description'] = $_POST['description'];
        $data['hienthi'] = isset($_POST['hienthi']) ? 1 : 0;
        $data['ngaytao'] = time();

        $d->setTable('dknhantin');
        if ($d->insert($data))
            redirect("index.php?com=dknhantin&act=man_cat");
        else
            transfer("Lưu dữ liệu bị lỗi", "index.php?com=dknhantin&act=man_cat");
    }
}

function check($x) {
    echo "<pre>";
    print_r($x);
    echo '</pre>';
}

function delete_cat() {
    global $d;
    if (isset($_GET['id'])) {
        $id = themdau($_GET['id']);
        $d->reset();
        $sql = "delete from #_dknhantin where id='" . $id . "'";
        if ($d->query($sql))
            redirect("index.php?com=dknhantin&act=man_cat");
        else
            transfer("Xóa dữ liệu bị lỗi", "index.php?com=dknhantin&act=man_cat");
    }elseif (isset($_GET['listid']) == true) {
        $listid = explode(",", $_GET['listid']);
        for ($i = 0; $i < count($listid); $i++) {
            $idTin = $listid[$i];
            $id = themdau($idTin);
            $d->reset();
            $sql = "select * from #_dknhantin where id='" . $id . "'";
            $d->query($sql);
            if ($d->num_rows() > 0) {
                $sql = "delete from #_dknhantin where id='" . $id . "'";
                $d->query($sql);
            }
        } redirect("index.php?com=dknhantin&act=man_cat");

    } else
        transfer("Không nhận được dữ liệu", "index.php?com=dknhantin&act=man_cat");
}

function guitin() {
    global $d, $host, $userhost, $passhost;
    $d->reset();
    $sql1 = "select email from table_setting";
    $d->query($sql1);
    $row_setting = $d->fetch_array();
    $title_bar .= _lienhe . " - ";
    if (!empty($_POST)) {
        include_once _lib . "C_email.php";
        $data['tieude'] = $_POST['tieude1'];
        $data['noidung'] = $_POST['noidung'];

        $subject = "Thư liên hệ từ " . $row_setting['title'];
        $body = '<table>';
        $body .= '
				<tr>
					<th colspan="2">&nbsp;</th>
				</tr>
				<tr>
					<th colspan="2">Thư liên hệ từ website ' . $row_setting['website'] . '</th>
				</tr>
				<tr>
					<th colspan="2">&nbsp;</th>
				</tr>
				<tr>
					<th>Tiêu đề :</th><td>' . $_POST['tieude1'] . '</td>
				</tr>
				<tr>
					<th>Nội dung :</th><td>' . $_POST['noidung'] . '</td>
				</tr>';
        $body .= '</table>';

        include_once "../phpmailer/class.phpmailer.php";
//Khởi tạo đối tượng
        $mail = new PHPMailer();
//Thiet lap thong tin nguoi gui va email nguoi gui
        $mail->IsSMTP(); // Gọi đến class xử lý SMTP
        $mail->SMTPAuth = true;                  // Sử dụng đăng nhập vào account
        $mail->Host = $host;     // Thiết lập thông tin của SMPT
        $mail->Username = $userhost; // SMTP account username
        $mail->Password = $passhost;            // SMTP account password
        $mail->SetFrom($userhost, $row_setting['ten']);
//Thiết lập thông tin người nhận

        foreach ($_POST['chon'] as $arr => $id) {

            $d->reset();
            $sql = "select email from table_dknhantin where id='" . $id . "'";
            $d->query($sql);
            $itemsss = $d->result_array();
            $mail->AddAddress($itemsss[0]['email'], $itemsss[0]['email']);
        }
//Thiết lập email nhận email hồi đáp
//nếu người nhận nhấn nút Reply
        $mail->AddReplyTo($row_setting['email'], $row_setting['ten']);
        /* =====================================
         * THIET LAP NOI DUNG EMAIL
         * ===================================== */

//Thiết lập tiêu đề
        $mail->Subject = "Thông tin liên hệ";

//Thiết lập định dạng font chữ
        $mail->CharSet = "utf-8";

        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";

//Thiết lập nội dung chính của email
        $mail->MsgHTML($body);

        if (!$mail->Send()) {
            transfer("Có lỗi xảy ra!", "index.php?com=dknhantin&act=man_cat");
        } else {

            transfer("Gửi mail thành công!<br/>", "index.php?com=dknhantin&act=man_cat");
        }
    }
}

?>