<?php 
    $cn = new mysqli("localhost","root","","php_2");
    $title = $_POST['txt-title'];
    $photo = $_POST['txt-photo'];
    $des = $_POST['txt-des'];
    $des = str_replace("\n","<br>",$des);
    $des = $cn->real_escape_string($des);
    $lang = $_POST['txt-lang'];
    $status = $_POST['txt-status'];
    $edit_id = $_POST['txt-edit-id'];
    $msg['edit']=false;
    $msg['dpl']=false;
    //check dpl data
    $sql = "SELECT title FROM tbl_city WHERE title = '$title' && id != $edit_id ";
    $rs = $cn->query($sql);
    if($rs->num_rows>0){
        $msg['dpl']=true;
    }else{
        if($edit_id == 0){
            $sql = "INSERT INTO tbl_city VALUES(null,'$title','$photo','$des','$lang','$status')";
            $cn->query($sql);
            $msg['id'] = $cn->insert_id;
        }else{
            $sql = "UPDATE tbl_city SET title='$title',photo='$photo',des='$des',lang='$lang',status='$status' WHERE id = $edit_id";
            $cn->query($sql);
            $msg['edit']=true;
        }
    }
    echo json_encode($msg);

?>
