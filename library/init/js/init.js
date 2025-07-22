(function () {
    const originalAddEventListener = EventTarget.prototype.addEventListener;

    EventTarget.prototype.addEventListener = function (type, listener, options) {
        const passiveEvents = ['touchstart', 'touchmove', 'wheel', 'mousewheel'];

        // If options is false (means no capture, no passive)
        if (passiveEvents.includes(type) && options === false) {
            return originalAddEventListener.call(this, type, listener, { passive: true });
        }

        // If options is an object but missing passive
        if (
            passiveEvents.includes(type) &&
            typeof options === 'object' &&
            options !== null &&
            !options.passive
        ) {
            options.passive = true;
        }

        return originalAddEventListener.call(this, type, listener, options);
    };
})();


$(document).on('pjax:end', function() {
    // Example: Re-initialize tooltips, plugins, etc.
    $('[data-toggle="tooltip"]').tooltip();

    // If you use kartik widgets, re-initialize them here manually
});

$(function() {
    setTimeout(function() {
        $('#alertMessage').slideToggle(700);
    }, 5000);

});