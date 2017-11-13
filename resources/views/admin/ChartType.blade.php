<div class="form-group"> 
    <label for="chart" class="col-sm-2 control-label">Chart</label>
    <div class="col-sm-2">
        <select id="chart" name="chart" class="form-control">                
            <option <?php if($chart == 'column'){echo 'selected="selected"';} ?> value="column">Column</option>
            <option <?php if($chart == 'bar'){echo 'selected="selected"';} ?> value="bar">Bar</option>
            <option <?php if($chart == 'pie'){echo 'selected="selected"';} ?> value="pie">Pie</option>
        </select>
    </div>
</div>