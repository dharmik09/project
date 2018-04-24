<table class="sponsor_table table_ckbx nobopd" id="table1">
    <tr class="cst_status">
        <th>Sr. No</th>
        <th>Question</th>
        <th>Question Type</th>
        <th>{{trans('labels.activityblheadpoints')}}</th>
        <th>Answer Choices</th>
        <th>{{trans('labels.activityblheadaction')}}</th>
    </tr>
    <?php $serialno = 1; ?>
    @forelse($level2activities as $level2activity)
    <tr>
        <td>
            <?php echo $serialno; ?>
        </td>
        <td>
            {{$level2activity->l2ac_text}}
        </td>
        <td>
            <?php
                if(isset($level2activity->l2ac_apptitude_type) && !empty($level2activity->l2ac_apptitude_type) && $level2activity->l2ac_apptitude_type != '' )
                {
                    ?> <div>{{$level2activity->apt_name}}</div> <?php
                }
                
                if(isset($level2activity->l2ac_personality_type) && !empty($level2activity->l2ac_personality_type) && $level2activity->l2ac_personality_type != '' )
                {
                    ?> <div>{{$level2activity->pt_name}}</div> <?php
                }
                
                if(isset($level2activity->l2ac_mi_type) && !empty($level2activity->l2ac_mi_type) && $level2activity->l2ac_mi_type != '' )
                {
                    ?> <div>{{$level2activity->mit_name}}</div> <?php
                }
                
                if(isset($level2activity->l2ac_interest) && !empty($level2activity->l2ac_interest) && $level2activity->l2ac_interest != '' )
                {
                   ?> <div>{{$level2activity->it_name}}</div> <?php
                }
                ?>
        </td>
        <td>
            {{$level2activity->l2ac_points}}
        </td>
        <td>
            <?php 
            $explodeOption = explode(',', $level2activity->l2op_option);
            $explodeFraction = explode(',', $level2activity->l2op_fraction);
            foreach($explodeOption as $key => $option_name)
            {
                if (count($explodeFraction) > 0 && $explodeFraction[$key] == 1) { ?> 
                    <strong><span class="font-blue"> 
                    <?php
                        echo $option_name."<br/>"; ?>
                    </span></strong>
                <?php } else { 
                    echo $option_name."<br/>";
                }
            }
            ?>
        </td>
        <td>
            <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
            <a href="{{ url('/school/edit-level2-questions') }}/{{$level2activity->id}}{{$page}}"><i class="fa fa-edit"></i> &nbsp;&nbsp;</a>
            <a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/school/delete-level2-questions') }}/{{$level2activity->id}}"><i class="i_delete fa fa-trash"></i></a>
        </td>
    </tr>
    <?php $serialno++; ?>
    @empty
    <tr>
        <td colspan="6"><center>{{trans('labels.norecordfound')}}</center></td>
    </tr>
    @endforelse
    <tr>
        <td colspan="6" class="sub-button">
            @if (isset($level2activities) && count($level2activities) > 0)
            <div class="pull-right">
                <?php echo $level2activities->render(); ?>
            </div>
            @endif
        </td>
    </tr>
</table>