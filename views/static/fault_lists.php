<?php
/**
 * Created by PhpStorm.
 * User: luoguangwei
 * Date: 2017/7/26
 * Time: 13:57
 */
?>

<style>
    #fault-lists{
        margin-top: 30px;
        border: 1px solid #efefef;
    }
</style>


<div class="row">
    <table id="fault-lists" class="table table-striped">
        <tr>
            <td>#</td>
            <td>清单名</td>
            <td>创建日期</td>
            <td>操作</td>
        </tr>
        <?php
        $i=1;
        foreach($lists as $list){?>
            <tr list_id="<?=$list['fault_list_id']?>">
                <td><?=$i++?></td>
                <td>
                    <a href="index.php?r=static/get-detail&fault_list_id=<?=$list['fault_list_id']?>">
                        <?=$list['fault_list_name']?>
                    </a>
                </td>
                <td><?=$list['create_date']?></td>
                <td><a href="index.php?r=static/execute&fault_list_id=<?=$list['fault_list_id']?>">执行下载</a></td>
            </tr>
        <?php }
        ?>
    </table>

</div>
