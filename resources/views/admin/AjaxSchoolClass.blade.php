<?php if(isset($result) && !empty($result)){ ?>
	<option value="all">All</option>
<?php foreach($result as $key => $class) { ?>                    
    <option value="{{$class->t_class}}"> {{$class->t_class}} </option>               
<?php } }else{?>
	<option value="">No class available</option>        
<?php }?>            
