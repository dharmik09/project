function readURL(input_file, new_id) {
    if (input_file.files && input_file.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var fileType = input_file.files[0];
            if (fileType.type == 'image/jpeg' || fileType.type == 'image/jpg' || fileType.type == 'image/png' || fileType.type == 'image/bmp' || fileType.type == 'image/gif') {
                if (input_file.files[0].size > 6000000) {
                    alert("File size is too large. Maximum 3MB allowed");
                    $(this).val('');
                } else {
                    //$(input_file).siblings('.placeholder_image').find('img').attr('src', e.target.result);
                     $(new_id).attr('src', e.target.result);
                }
            } else {
                alert("This file type is not allowed.");
                $(input_file).val('');
            }           
        };
        reader.readAsDataURL(input_file.files[0]);
    }
}
$('body').on("change", ".img_select", function() {
    var new_id = $(this).data('imgsel');
    readURL(this, new_id);
});
$('body').on("click", ".img_replace", function() {
    $(this).siblings('.img_select').trigger('click');
});