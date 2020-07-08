var ClipboardHelper = {

    copyElement: function ($element){
       this.copyText($element.text())
    },
    copyText:function(text){
        var $tempInput =  $("<textarea>");
        $("body").append($tempInput);
        $tempInput.val(text).select();
        document.execCommand("copy");
        $tempInput.remove();
    },
    copyTextElement:function(text, element){
        var $tempInput =  $("<textarea>");
        $(element).append($tempInput);
        $tempInput.val(text).select();
        document.execCommand("copy");
        $tempInput.remove();
    }
};
