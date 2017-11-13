<div class="form-group">
    <label for="age" class="col-sm-2 control-label">Age</label>
    <div class="col-sm-2">
        <select id="age" name="age" class="form-control">
            <option value="">Select Age</option>
            <option <?php if($age == '13'){echo 'selected="selected"';} ?> value="13">Less than 13</option>
            <option <?php if($age == '13-14'){echo 'selected="selected"';} ?> value="13-14">13-14</option>
            <option <?php if($age == '14-15'){echo 'selected="selected"';} ?> value="14-15">14-15</option>
            <option <?php if($age == '15-16'){echo 'selected="selected"';} ?> value="15-16">15-16</option>
            <option <?php if($age == '16-17'){echo 'selected="selected"';} ?> value="16-17">16-17</option>
            <option <?php if($age == '17-18'){echo 'selected="selected"';} ?> value="17-18">17-18</option>
            <option <?php if($age == '18-19'){echo 'selected="selected"';} ?> value="18-19">18-19</option>
            <option <?php if($age == '19-20'){echo 'selected="selected"';} ?> value="19-20">19-20</option>
            <option <?php if($age == '20'){echo 'selected="selected"';} ?> value="20">above 20</option>
        </select>
    </div>
</div>