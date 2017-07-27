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
    <table id="fault-lists" class="table table-striped table-condensed">
        <tr>
            <td>#</td>
            <td>故障单号</td>
            <td>故障标题</td>
            <td>故障种类</td>
            <td>故障内容</td>
            <td>故障状态</td>
            <td>故障原因</td>
            <td>发生时间</td>
            <td>故障修复时间</td>
            <td>故障统计初始时间</td>
            <td>故障统计结束时间</td>
        </tr>
        <?php
        $i=1;
        foreach($lists as $list){?>
            <tr list_id="<?=$list['fault_list_id']?>">
                <td><?=$i++?></td>
                <td><?=$list['fault_code']?></td>
                <td><?=$list['fault_title']?></td>
                <td><?=$list['fault_type']?></td>
                <td><?=$list['fault_content']?></td>
                <td><?=$list['fault_state']?></td>
                <td><?=$list['fault_reason']?></td>
                <td><?=$list['create_date']?></td>
                <td><?=$list['repair_date']?></td>
                <td><?=$list['start_date']?></td>
                <td><?=$list['end_date']?></td>
            </tr>
        <?php }
        ?>
    </table>

</div>
