 function readURL(input, img_id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('img[img-id="'+img_id+'"]').attr('src', e.target.result).show();
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }