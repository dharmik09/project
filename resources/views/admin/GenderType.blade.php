<div class="form-group">
    <label for="gen" class="col-sm-2 control-label">Gender</label>
    <div class="col-sm-2">
        <select id="gen" name="gen" class="form-control">
            <option value="">Select Gender</option>
            <option <?php if($gender == 1){echo 'selected="selected"';} ?> value="1">Male</option>
            <option <?php if($gender == 2){echo 'selected="selected"';} ?> value="2">Female</option>
        </select>
    </div>
</div>