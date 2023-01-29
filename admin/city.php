<?php 
    $cn = new mysqli("localhost","root","","php_2");
    $id = 1;
    //get auto id
    $sql = "SELECT id FROM tbl_city ORDER BY id DESC";
    $rs = $cn->query($sql);
    $num = $rs->num_rows;
    if($num > 0 ){
        $row = $rs->fetch_array();
        $id = $row[0] + 1;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
</head>
<body>
    <form class="upl">
        <div class="frm">
            <input type="text" name="txt-edit-id" id="txt-edit-id" value="0">
            <label for="">ID</label>
            <input type="text" name="txt-id" id="txt-id" class="frm-control" value="<?php echo $id; ?>" readonly>
            
            <label for="">Lang</label>
            <select name="txt-lang" id="txt-lang" class="frm-control">
                <option value="en">English</option>
                <option value="kh">Khmer</option>
            </select>

            <label for="">Title</label>
            <input type="text" name="txt-title" id="txt-title" class="frm-control">
            <label for="">Description</label>
            <textarea name="txt-des" id="txt-des" cols="30" rows="10" class="frm-control"></textarea>
            <label for="">Photo</label>


            <div class="img-box">
                <input type="file" name="txt-file" id="txt-file" class="txt-file">
                <input type="text" name="txt-photo" id="txt-photo">
            </div>



            <label for="">Status(1=Show, 2=None)</label>
            <select name="txt-status" id="txt-status" class="frm-control">
                <option value="1">1</option>
                <option value="2">2</option>
            </select>
            <div class="btn-save">
                <i class="fa-solid fa-floppy-disk"></i> Save
            </div>
        </div>
    </form>    
    <table id="tblData">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Photo</th>
            <!-- <th>Description</th> -->
            <th>Lang</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php 
            $sql = "SELECT * FROM tbl_city ORDER BY id DESC";
            $rs = $cn->query($sql);
            while($row = $rs->fetch_array()){
                ?>
                    <tr>
                        <td><?php echo $row[0]; ?></td>
                        <td><?php echo $row[1]; ?></td>
                        <td><?php echo $row[2]; ?></td>
                        <td class="hide"><?php echo $row[3]; ?></td>
                        <td><?php echo $row[4]; ?></td>
                        <td><?php echo $row[5]; ?></td>
                        <td>
                            <input type="button" value="Edit" class="btnEdit">
                        </td>
                    </tr>
                <?php 
            }
        ?>
    </table>
</body>
<script>
    $(document).ready(function(){
        var tbl = $("#tblData");
        var trInd;
        var loading = `<div class="loading"></div>`;
        var imgBox = $(".img-box");
        var delImg = ` <div class="del-img"></div>`;
         // upload image
         $(".txt-file").change(function(){
            var eThis = $(this);
            var frm = eThis.closest('form.upl');
            var frm_data = new FormData(frm[0]);
            $.ajax({
                url: 'action/upl-img.php',
                type: 'POST',
                data: frm_data,
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    //work before success    
                    imgBox.append(loading);
                },
                success: function(data) {
                    alert(data.error);
                    //work after success 
                    $("#txt-photo").val(data.img_name);
                    imgBox.css({"background-image":"url(img/"+data.img_name+")"});
                    imgBox.find(".loading").remove();
                    imgBox.append(delImg);
                }
            });
        });
        //save data
        $(".btn-save").click(function(){
            var eThis = $(this);
            var Parent = eThis.parents('.frm');
            var id = Parent.find("#txt-id");
            var lang = Parent.find("#txt-lang");
            var title = Parent.find("#txt-title");
            var des = Parent.find("#txt-des");
            var photo = Parent.find("#txt-photo");
            var status = Parent.find("#txt-status");
            if( title.val() == "" ){
                alert("Please Input Title");
                title.focus();
                return;
            }else if(des.val() == ""){
                alert("Please Input Description.");
                des.focus();
                return;
            }

            var frm = eThis.closest('form.upl');
            var frm_data = new FormData(frm[0]);
            $.ajax({
                url:'action/city.php',
                type:'POST',
                data:frm_data,
                contentType:false,
                cache:false,
                processData:false,
                dataType:"json",
                beforeSend:function(){
                    //work before success    
                },
                success:function(data){   
                    //work after success
                    if(data.dpl==true){
                        alert("Duplicated Title.");
                        title.focus();
                        return;
                    }else if(data.edit==true){
                       tbl.find( 'tr:eq('+trInd+') td:eq(1)' ).text(title.val());
                       tbl.find( 'tr:eq('+trInd+') td:eq(3)' ).text(des.val());
                       tbl.find( 'tr:eq('+trInd+') td:eq(4)' ).text(lang.val());
                       tbl.find( 'tr:eq('+trInd+') td:eq(5)' ).text(status.val()); 
                       
                    }else{
                        var tr=`
                            <tr>
                                <td>${id.val()}</td>
                                <td>${title.val()}</td>
                                <td>${photo.val()}</td>
                                <td class="hide">${des.val()}</td>
                                <td>${lang.val()}</td>
                                <td>${status.val()}</td>
                                <td>
                                    <input type="button" value="Edit" class="btnEdit">
                                </td>
                            </tr>
                            `;      
                        // tbl.append(tr);
                        tbl.find("tr:eq(0)").after(tr);
                        title.val(" ");
                        des.val(" ");
                        title.focus();
                        id.val(data.id + 1);  
                    }
                }        
            });
        });
        //get edit data
        $("#tblData").on("click","tr td .btnEdit",function(){
            var eThis = $(this);
            var tr = eThis.parents("tr");
            var id = tr.find("td").eq(0).text();
            var title = tr.find("td").eq(1).text();
            var des = tr.find("td").eq(3).text();
            var lang = tr.find("td").eq(4).text();
            var status = tr.find("td").eq(5).text();
            $("#txt-id").val(id);
            $("#txt-title").val(title);
            $("#txt-des").val(des);
            $("#txt-lang").val(lang);
            $("#txt-status").val(status);
            $("#txt-edit-id").val(id);
            trInd = tr.index();

        });
    });
</script>
</html>