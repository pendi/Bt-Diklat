$(function() {
    // Run the clock
    function timer() {
        var t = new Date();
        $('.system-datetime').html(t.format('default'));
        $('.xinix-time').html(t.format('xinixTime'));
        $('.xinix-date').html(t.format('xinixDate'));
    }
    setInterval(timer, 1000);
    timer();

    prettyPrint();
});