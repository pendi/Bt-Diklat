if (typeof(console) == 'undefined') {
    window.console = console = {
        'log': function() {},
        'info': function() {},
        'warn': function() {},
        'error': function() {}
    };
}

var xn = {};

xn.config = {
    'fancyOptions': {
        'transitionIn'      : 'fade',
        'transitionOut'     : 'fade',
        'autoDimensions'    : false,
        'autoScale'         : false,
        'centerOnScroll'    : true,
        'overlayColor'      : '#000',
        'overlayOpacity'    : 0.75,
        'overlayShow'       : true,
        'scrolling'         : 'no',
        'showNavArrows'     : false,
        'width'             : '600px',
        'height'            : 'auto',
        'padding'           : 20,
        'modal'             : true,
        'speedIn'           : 350,
        'speedOut'          : 350,
            
        'onStart'           : function(obj) {
            if ($(obj).attr('data-selected') === '') {
                return false;
            }
        },

        'onComplete'        : function() {
            $('#fancybox-close').show();
            xn.helper.stylize('#fancybox-inner');
            xn.helper.fancyformify('#fancybox-inner');
            $(document).unbind('keydown.fb').bind('keydown.fb', function(e) {
                if (e.keyCode == 27) {
                    e.preventDefault();
                    $.fancybox.close();

                } else if ((e.keyCode == 37 || e.keyCode == 39) && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    $.fancybox[ e.keyCode == 37 ? 'prev' : 'next']();
                }
            });
        },
            
        'onClosed'           : function(obj) {
            if ($(obj).attr('data-msgbox-refresh') != 'false') {
                location.href = location.href;
            }
        }
    }
};

xn.helper = {
        
    createUrl: function(uri) {
        return xn.config.baseUrl + 'index.php/' + uri;
    },
    
    dataUrl: function(uri) {
        return xn.config.baseUrl + 'data/' + uri;
    },
    
    themeUrl: function(uri) {
        return xn.config.baseUrl + 'themes/default/' + uri;
    },
    
    /* Move footer to always on bottom. ps: put this after stylize */
    resize: function(evt) {
        var windowHeight = $('body').innerHeight();
        var footer = $('#layout-footer');
        
        footer.css('height', 'auto');
        var layoutHeight = $('#layout-body').outerHeight();
        
        if (windowHeight > layoutHeight) {
            var footerHeight =  windowHeight - layoutHeight;
            footer.height(footerHeight);
        }

        $(".layout-flexible .fifths").css ("width", function () {
            return Math.floor($(this).parent().width() / 5.02);
        });
        $(".layout-flexible .quarter").css ("width", function () {
            return Math.floor($(this).parent().width() / 4.03);
        });
        $(".layout-flexible .thirds").css ("width", function () {
            return Math.floor($(this).parent().width() / 3.04);
        });
        $(".layout-flexible .half").css ("width", function () {
            return Math.floor($(this).parent().width() / 2.05);
        });
        $(".layout-flexible .auto").css ("width", function () {
            var allwidth = 0;
            $(this).siblings().each(function(i){
                allwidth += $(this).width ();
            });

            return Math.floor($(this).parent().width() - allwidth);
        });
    },

    stylize: function(selector) {
        if (typeof(selector) == 'undefined') {
            selector = 'body';
        }

        $(selector).find('.pull-right, .pull-left').addClass(function() {
            return $(this).hasClass('pull-right') ? 'right' : 'left';
        });
        $(selector).find('.clearfix').addClass('clear');

        $(selector).find('.btn').addClass('button');
        $(selector).find('.submenu .submenu-container').addClass('container');


        /* Add common odd, even, first, last and hover */
        $(selector).find("*").hover(function () {
            $(this).addClass("hover");
        }, function () {
            $(this).removeClass("hover");
            $(this).removeClass('pressed');
        }).focus(function() {
            $(this).addClass('focus');
        }).blur(function() {
            $(this).removeClass('focus');
        }).mousedown(function(){
            $(this).addClass('pressed');
        });

        $(selector).find(".tab a:first-child").addClass("first");
        $(selector).find(".tab a:last-child").addClass("last");

        $(selector).find("input").addClass(function () {
            return $(this).attr("type").toLowerCase();
        });
        $(selector).find("select").addClass("select");
        $(selector).find("select[multiple]").addClass("multiple");

        $(selector).find("table > tr:nth-child(odd),ul > li:nth-child(odd), ol > li:nth-child(odd), .list > .comment:nth-child(odd)").addClass("odd");
        $(selector).find("table > tr:nth-child(even),ul > li:nth-child(even), ol > li:nth-child(even), .list > .comment:nth-child(even)").addClass("even");
        $(selector).find("ul > li:first-child, ol > li:first-child, .list > .comment:first-child, div > .button:first-child").addClass("first");
        $(selector).find("ul > li:last-child, ol > li:last-child, .list > .comment:last-child, div > .button:last-child").addClass("last");

        $(selector).find("[class^=blocks_] > div.block:first-child").addClass("first");
        $(selector).find("[class^=blocks_] > div.block:last-child").addClass("last");

        $(selector).find("[class^=grid] tr").removeClass("first last even odd");
        $(selector).find("[class^=grid] tr:first-child").addClass ("first");
        $(selector).find("[class^=grid] tr:last-child").addClass ("last");
        $(selector).find("[class^=grid] tr:nth-child(odd)").addClass ("even");
        $(selector).find("[class^=grid] tr:nth-child(even)").addClass ("odd");

        $(selector).find("[class^=grid] tr:first-child td:first-child").addClass ("first");
        $(selector).find("[class^=grid] tr:first-child td:last-child").addClass ("last");
        $(selector).find("[class^=grid] tr:first-child td:nth-child(odd)").addClass ("even");
        $(selector).find("[class^=grid] tr:first-child td:nth-child(even)").addClass ("odd");

        $(selector).find("input[type='text'], input[type='password'], input[type='checkbox'], input[type='radio'], textarea").uniform();

        $(selector).find("a[title!=''], img[title!=''], div[title!='']").tooltip();
        
        /* Self add button class based on it's content */
        $(selector).find(".button").wrapInner ("<span />");
        $(selector).find(".button > span").addClass(function (){
            var clazz = $(this).parent().attr('data-class');
            return (clazz) ? clazz : '';
        });
        $(selector).find(".top-nav li").addClass(function (){
            var clazz = $(this).children("a").attr('data-class');
            return "icn" + ((clazz) ? " icn-" + clazz : "");
        });
        
        $(selector).find(".layout-flexible > div").find("script").remove().end().wrapInner("<div class='wrap' />");
        
        /* HTML5 Input's Placeholder for older browser */
        // $(selector).find('[placeholder]').focus(function() {
        //     var input = $(this);
        //     if (input.val() == input.attr('placeholder')) {
        //         input.val('');
        //         input.removeClass('placeholder');
        //     }
        // }).blur(function() {
        //     var input = $(this);
        //     if (input.val() === '' || input.val() == input.attr('placeholder')) {
        //         input.addClass('placeholder');
        //         input.val(input.attr('placeholder'));
        //     }
        // }).blur();
        // $(selector).find('[placeholder]').parents('form').submit(function() {
        //     $(this).find('[placeholder]').each(function() {
        //         var input = $(this);
        //         if (input.val() == input.attr('placeholder')) {
        //             input.val('');
        //         }
        //     });
        // });
        
        $(selector).find('input.number').keypress(function(evt, a) {
            if (!(evt.charCode >= 48 && evt.charCode <= 57) && evt.charCode != 13) {
                evt.preventDefault();
            }
        });
        
        $(selector).find('input.phone_number').keypress(function(evt, a) {
            if (!(evt.charCode >= 48 && evt.charCode <= 57) && evt.charCode != 13 && evt.charCode != 43) {
                evt.preventDefault();
            }
        });
        
        $(selector).find('input.regex-val').keypress(function(evt, a) {
            s = evt.which | evt.charCode | evt.keyCode;
            if (s == 8 || s == 46 || s == 37 || s == 38 || s == 39 || s == 40 || s == 9) {
                return true;
            }
            var str = $(this).val() + String.fromCharCode(evt.charCode);
            var reg = new RegExp($(this).attr('data-regex'));
            var s = str.search(reg);
            if (s == -1) {
                evt.preventDefault();
                return false;
            }
        });
        
        $(selector).find('.cancel').click(function(evt) {
            if ($(this).parents('#fancybox-inner').length > 0) {
                evt.preventDefault();
                $.fancybox.close();
                return false;
            }
        });
        
        $(selector).find('a.mass-action').each(function() {
            $(this).attr('data-href', $(this).attr('href'));
        }).click(function(evt) {
            var href = $(this).attr('data-href');
            
            var selectedList = [];
            $('table.grid').find('tr.grid_row *[checked]').parents('tr').each(function(index, node) {
                if (selectedList[0] != $(node).attr('data-ref')) {
                    selectedList.push($(node).attr('data-ref'));
                }
            });
            $(this).attr('data-selected', selectedList.join(','));
            $(this).attr('href', href + '/' + selectedList.join(','));
            if (selectedList.join(',') === '') {
                $.fancybox('<div class="error">No selected record!</div>');
                evt.preventDefault();
                return false;
            }
            
            return true;
        });

        $(selector).find("a.msgbox").fancybox (xn.config.fancyOptions);
        $(selector).find('a.popup-window').click(function(evt) {
            var width = $(this).attr('data-popup-width') || 640;
            var height = $(this).attr('data-popup-height') || 480;
            var title = $(this).attr('data-popup-title') || 'Popup Window';
            var popup = window.open($(this).attr('href'), title, "status=0,toolbar=0,width=" + width + ',height=' + height);
            return evt.preventDefault();
        });

        $.fancybox.resize ();
        $.fancybox.center ();

        xn.helper.levelize(selector);
    },

    levelize: function(selector) {
        if (typeof(selector) == 'undefined') {
            selector = 'body';
        }

        $('.layout-flexible', selector).each(function() {
            $(this).find('>div>div>fieldset').css('height', '100%');
            var h = 0;
            $(this).find('>div>div>fieldset').each(function() {
                if (h < $(this).height()) {
                    h = $(this).height();
                }
            });
            $(this).find('>div>div>fieldset').css('height', h);
        });
    },

    labelize: function(selector) {
        if (typeof(selector) == 'undefined') {
            selector = 'body';
        }

        var width = 0;

        $(selector).find('label').each(function() {
            if (width < $(this).width()) {
                width = $(this).width();
            }
        });
        $(selector).find('label').width(width);
    },

    fancyformify: function(selector) {
        if (typeof(selector) == 'undefined') {
            selector = 'body';
        }
        
        $(selector).find('form.ajaxform').ajaxForm ({
            target: '#fancybox-inner',
            beforeSubmit: function(arr, $form, options) {
            
            },
            success: function (value) {
                if (value === true || value == 'true') {
                    $.fancybox.close();
                } else {
                    xn.helper.stylize(selector);
                    xn.helper.fancyformify(selector);
                }
            },
            error: function() {
            
            }
        });
    }
};

$.tools.dateinput.localize("id", {
    months: 'Januari,Februari,Maret,April,May,June,July,August,September,October,November,December',
    shortMonths:  'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec',
    days:         'Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
    shortDays:    'Mgg,Sen,Sel,Rab,Kam,Jum,Sab'
});

$(function() {
    $('script[data-xn-config]').each(function() {
        var x = '[{' + $(this).attr('data-xn-config') + '}]';
        $.extend(xn.config, eval(x)[0]);
    });
    
    var loading = $('<div id="loading"></div>');
    $('body').prepend(loading);
    loading.bind("ajaxStart", function(){
        $(this).css({
            'left': ($(window).width() - loading.width()) /  2,
            'top': ($(window).height() - loading.height()) /  2
        }).show();
    }).bind("ajaxStop", function(){
        $(this).hide();
    });
    
    $('.submenu').width($('.submenu .container').width());
    
    $('.error, .warn, .info').addClass("pop-message").wrapInner('<div class="content"><div class="text"></div><a href="#" class="close">x</a></div>').each(function() {
        var $content = $(this).find('.content');
        if ($content.width() > 800) {
            $content.width(800);
        }
    });
    $('.close').click (function () {
        $(this).parent().fadeOut("fast");
        return false;
    });

    // Detect browser for "racist" animation :P
    var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
    var is_explorer = navigator.userAgent.indexOf('MSIE') > -1;
    var is_firefox = navigator.userAgent.indexOf('Firefox') > -1;
    var is_safari = navigator.userAgent.indexOf("Safari") > -1;
    var is_opera = navigator.userAgent.indexOf("Presto") > -1;
    $('#layout.layout.login').wrapInner('<div class="cloud6"></div>');
    if (is_chrome) {
        $("body").addClass ("chrome");
    } else {
        // $('#layout.layout.login').wrapInner('<div class="cloud1"><div class="cloud2"><div class="cloud3"></div></div></div>');
        if (is_safari) {
            $("body").addClass ("safari");
        } else if (is_firefox) {
            $("body").addClass ("firefox");
        } else if (is_opera) {
            $("body").addClass ("opera");
        } else {
            $("body").addClass ("other");
        }
    }
    
    $('body').css("opacity", "1");

    $('#layout').fadeIn('slow');

    xn.helper.stylize('body');

    try {
        var ciprofiler = $('#codeigniter_profiler');
        ciprofiler.detach();
        $("#profiler_btn").click(function(evt) {
            $.fancybox('<div id="complete_profiler" style="width: 800px; height: 400px; overflow: auto;"></div>', {
                'width': 853,
                'height': 510,
                'scrolling': 'none',
                'overlayColor': '#001',
                'overlayOpacity': 0.75,
                'centerOnScroll': true,
                'onComplete': function() {
                    ciprofiler.appendTo($('#complete_profiler'));
                    xn.helper.stylize('#complete_profiler');
                },
                'onClosed': function () {
                    $('#complete_profiler').remove();
                }
            });
            return evt.preventDefault();
        });
    } catch (e) {
        console.error(e);
    }
    
    $(window).resize(function () {
        xn.helper.resize();
    });
    xn.helper.resize();
});


var konami = "38,38,40,40,37,39,37,39,66,65".split(',');
var keyIndex = 0;
$(document).keydown(function(ev) {
    if (konami[keyIndex] == ev.keyCode) keyIndex++; else keyIndex = 0;
    if (keyIndex == konami.length) {
        $.fancybox('<div id="konamicode" style="width: 853px; height: 510px;"></div>', {
            'width': 853,
            'height': 510,
            'scrolling': 'none',
            'overlayColor': '#000000',
            'overlayOpacity': 0.75,
            'centerOnScroll': true,
            'onComplete': function() {
            },
            'onClosed': function () {
                $('.even').addClass ('rotate2');
                $('.odd').addClass ('rotate-2');
                $('#konamicode').remove ();
            }
        });
        keyIndex = 0;
    }
});

var dateFormat = function () {
    var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
    timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[\-+]\d{4})?)\b/g,
    timezoneClip = /[^-+\dA-Z]/g,
    pad = function (val, len) {
        val = String(val);
        len = len || 2;
        while (val.length < len) val = "0" + val;
        return val;
    };

    // Regexes and supporting functions are cached through closure
    return function (date, mask, utc) {
        var dF = dateFormat;

        // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
        if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
            mask = date;
            date = undefined;
        }

        // Passing date through Date applies Date.parse, if necessary
        date = date ? new Date(date) : new Date();
        if (isNaN(date)) throw SyntaxError("invalid date");

        mask = String(dF.masks[mask] || mask || dF.masks["default"]);

        // Allow setting the utc argument via the mask
        if (mask.slice(0, 4) == "UTC:") {
            mask = mask.slice(4);
            utc = true;
        }

        var _ = utc ? "getUTC" : "get",
        d = date[_ + "Date"](),
        D = date[_ + "Day"](),
        m = date[_ + "Month"](),
        y = date[_ + "FullYear"](),
        H = date[_ + "Hours"](),
        M = date[_ + "Minutes"](),
        s = date[_ + "Seconds"](),
        L = date[_ + "Milliseconds"](),
        o = utc ? 0 : date.getTimezoneOffset(),
        flags = {
            d:    d,
            dd:   pad(d),
            ddd:  dF.i18n.dayNames[D],
            dddd: dF.i18n.dayNames[D + 7],
            m:    m + 1,
            mm:   pad(m + 1),
            mmm:  dF.i18n.monthNames[m],
            mmmm: dF.i18n.monthNames[m + 12],
            yy:   String(y).slice(2),
            yyyy: y,
            h:    H % 12 || 12,
            hh:   pad(H % 12 || 12),
            H:    H,
            HH:   pad(H),
            M:    M,
            MM:   pad(M),
            s:    s,
            ss:   pad(s),
            l:    pad(L, 3),
            L:    pad(L > 99 ? Math.round(L / 10) : L),
            t:    H < 12 ? "a"  : "p",
            tt:   H < 12 ? "am" : "pm",
            T:    H < 12 ? "A"  : "P",
            TT:   H < 12 ? "AM" : "PM",
            Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
            o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
            S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
        };

        return mask.replace(token, function ($0) {
            return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
        });
    };
}();

// Some common format strings
dateFormat.masks = {
    "default":      "dd/mm/yyyy HH:MM:ss",
    shortDate:      "m/d/yy",
    mediumDate:     "mmm d, yyyy",
    longDate:       "mmmm d, yyyy",
    fullDate:       "dddd, mmmm d, yyyy",
    shortTime:      "h:MM TT",
    mediumTime:     "h:MM:ss TT",
    longTime:       "h:MM:ss TT Z",
    isoDate:        "yyyy-mm-dd",
    isoTime:        "HH:MM:ss",
    isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
    isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'",
    xinixDate:      "dd/mm/yyyy",
    xinixTime:      "HH:MM:ss"
};

// Internationalization strings
dateFormat.i18n = {
    dayNames: [
    "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
    "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
    ],
    monthNames: [
    "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
    "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
    ]
};

// For convenience...
Date.prototype.format = function (mask, utc) {
    return dateFormat(this, mask, utc);
};

$.Autocompleter.defaults = {
    dataType: 'json',
    inputClass: "ac_input",
    resultsClass: "ac_results",
    loadingClass: "ac_loading",
    minChars: 1,
    delay: 400,
    matchCase: false,
    matchSubset: true,
    matchContains: false,
    cacheLength: 10,
    max: 100,
    mustMatch: true,
    extraParams: {},
    selectFirst: true,
    parse: function(data) {
        var d = [];
        for(var i = 0; i < data.length; i++) {
            var r = {
                data: data[i],
                value: (data[i].key) ? ''+data[i].key : '',
                result: (data[i].value) ? ''+data[i].value : ''
            };
            d.push(r);
        }
        return d;
    },
    formatItem: function(row) {
        if (typeof(row.value) == 'undefined') {
            throw new Error('No field value on RPC');
        }
        return row.value;
    },
    formatResult: function(row) {
        if (typeof(row.value) == 'undefined') {
            throw new Error('No field key on RPC');
        }
        return row.key;
    },
    formatMatch: null,
    autoFill: true,
    width: 0,
    multiple: false,
    multipleSeparator: ", ",
    highlight: function(value, term) {
        return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1") + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>");
    },
    scroll: true,
    scrollHeight: 220
};

