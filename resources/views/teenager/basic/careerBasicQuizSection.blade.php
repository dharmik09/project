<div class="quiz_view">
    <div class="clearfix time_noti_view">
    	<span class="time_type pull-left">
    		<i class="icon-alarm"></i>
    		<span class="basic-time-tag">0:0</span>
    	</span>
    	<span class="help_noti pull-right">
    		<span class="pull-right close">
    			<i class="icon-close"></i>
    		</span>
    	</span>
    </div>
    <div class="quiz-que">
        <p class="que">
        	<i class="icon-arrow-simple"></i>Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor?
        </p>
        <div class="quiz-ans">
            <div class="radio">
            	<label>
            		<input type="radio" name="gender">
            		<span class="checker"></span>
            		<em>Lorem ipsum dolor sit amet</em>
            	</label>
            	<label>
            		<input type="radio" name="gender">
            		<span class="checker"></span>
            		<em>Lorem ipsum dolor sit amet</em>
            	</label>
            	<label>
            		<input type="radio" name="gender">
            		<span class="checker"></span>
            		<em>Lorem ipsum dolor sit amet</em>
            	</label>
            </div>
            <div class="clearfix">
            	<a href="#" class="next-que pull-right"><i class="icon-hand"></i></a>
            </div>
        </div>
    </div>
</div>
<span title="Play" class="btn-play btn btn-basic">Play</span>

<script type="text/javascript">
	var basicCount = {{ isset($timer) ? $timer : 0}};
</script>
