$.fn.positionPicker = function(options) {
    var self = this;
        
    if (typeof($.positionPicker) == 'undefined') {
        $.positionPicker = {
            'ID': 1
        }
    }
    
    $('<img />').attr('src', xn.helper.dataUrl(options.map));
        
    options.id = $.positionPicker.ID ++;
    options.parent = this.wrap('<div id="position-' + options.id + '" class="position-container" />').parent();
        
    options.button = $('<a href="#data_' + options.id + '" class="position-hidden" >a</a>');
    options.picker = $('<div style="display:none">' +
        '<div id="data_' + options.id + '" data-map="' + options.map + '" style="overflow:hidden">' +
        '<img id="position-picker_' + options.id + '" />' +
        '<i class="position-marker"></i>' +
        '</div>' +
        '</div>');
    options.parent.append(options.button);
    options.parent.append(options.picker);
    
    $('.position-hidden', options.parent).click(function() {
        console.log('click');
    });
        
    $('.position-hidden', options.parent).fancybox({
        'transitionIn'      : 'fade',
        'transitionOut'     : 'fade',
        'autoDimensions'    : true,
        'autoScale'         : false,
        'centerOnScroll'    : true,
        'overlayOpacity'    : 0.5,
        'overlayColor'      : '#000',
        'showNavArrows'     : false,
        //            'width'		: 'auto',
        //            'height'		: 'auto',
        //            'padding'		: 20,
        'modal'             : true,
        'onStart'           : function() {
            console.log('start fancy');
            $('#fancybox-close').show();
                
            var dataMap = $('#data_' + options.id).attr('data-map');
            if (dataMap == '') {
                return false;
            }
                
            var pos = $(self).val();
            if (pos != '') {
                pos = pos.split(',');
                $('.position-marker', options.parent).css({
                    'left': pos[0] + 'px',
                    'top': pos[1] + 'px'
                }).show();
            }
            
            $('#position-picker_' + options.id).attr('src', xn.helper.dataUrl(dataMap)).load(function() {
                console.log('src');
                var c = $('#data_' + options.id);
                
                //                console.log();
                c.width(this.width);
                c.height(this.height);
                
                $.fancybox.resize();
            }).click(function(evt) {
                var offset = $(this).offset();
                $(self).val((evt.pageX - offset.left)  + ',' + (evt.pageY - offset.top));
                $.fancybox.close();
            });
        }
    });
        
    this.focus(function() {
        $('.position-hidden', options.parent).trigger('click');
    });
        
    return this;
}