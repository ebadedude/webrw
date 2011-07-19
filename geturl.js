// Experimenting with Bade's geturl.php tool 
// and taking the opportunity to also experiment with cloud9

console.log('geturl loaded');
geturl = function(url, callback) {
    var callId = 'callback' + Math.random().toString().slice(2);
    if (!callback) {
        callback = function(x) {
            console.log(x)
        }
    }
    if (typeof(callback)!== 'string') {
        var funName = callId;
        geturl[funName] = callback;
        callback = 'geturl.' + funName;
    }
    var s = document.createElement('script');
    s.src = 'http://sandbox1.mathbiol.org/geturl.php?url=' + url + '&callback=' + callback;
    s.id = callId;
    document.body.appendChild(s);
    geturl.queue = [geturl.queue, callId];
    return callId;
}