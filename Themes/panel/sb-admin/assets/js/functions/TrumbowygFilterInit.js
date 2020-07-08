var TrumbowygFilterInit = {

    init: function(element) {
        this.element = element;
        this.parents = '.trumbowyg-box';
        this.textarea = '.trumbowyg-textarea';
    },

    appenWysiwygOrigin: function() {
        $(this.element).each(function() {
            $video = $(this).filter('video');
            $audio = $(this).filter('audio');
            $img = $(this).filter('img');

            if ($video.length > 0) {
                $src = $video.attr('poster');
                if ($src.includes(location.origin) == false) {
                    $nsrc = new URL($src, location.origin);
                    $video.attr('poster', $nsrc);
                }
            }

            if ($video.find('source').length > 0) {
                $source = $video.find('source');
                $src = $source.attr('src');
                if ($src.includes(location.origin) == false) {
                    $nsrc = new URL($src, location.origin);
                    $source.attr('src', $nsrc.href);
                }
            }

            if ($audio.find('source').length > 0) {
                $source = $audio.find('source');
                $src = $source.attr('src');
                if ($src.includes(location.origin) == false) {
                    $nsrc = new URL($src, location.origin);
                    $source.attr('src', $nsrc.href);
                }
            }

            if ($img.length > 0) {
                $src = $img.attr('src');
                if ($src.includes(location.origin) == false) {
                    $nsrc = new URL($src, location.origin);
                    $img.attr('src', $nsrc.href);
                }
            }

        });
    },

    removeTextareaOrigin: function() {
        $textarea = $(this.element).parents(this.parents).find(this.textarea);
        $textarea.val(this.replaceAll($textarea.val(), location.origin, ''));
        $textarea.text(this.replaceAll($textarea.val(), location.origin, ''));
    },

    replaceAll: function(target, search, replacement) {
        if (isset(target)) {
            return target.replace(new RegExp(search, 'g'), replacement);
        }
    },

};