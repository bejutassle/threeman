$.sum = function(arr) {
    var r = 0;
    $.each(arr, function(i, v) {
        r += +v;
    });
    return r;
}