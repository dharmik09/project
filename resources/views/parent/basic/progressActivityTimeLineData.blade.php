<table>
<?php
    
    $classArray = array('alpha', 'beta', 'gamma', 'delta');
    ?>
    @if(isset($timeLine) && !empty($timeLine))
    <?php $flag = 0; ?>
    @foreach($timeLine as $line=>$date)

    <tr class="{{$classArray[$flag]}}">
        <td class="timeline_icon">
            <span class="box"></span>
        </td>
        <td class="timeline_date">{{date('d, F Y',strtotime($date))}}</td>
        <td class="timeline_detail">{{$line}}</td>
    </tr>
    <?php
    $flag++;
    if ($flag > 3) {
        $flag = 0;
    }
    ?>
    @endforeach
    @endif
</table>