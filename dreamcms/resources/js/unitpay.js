function loadCss(cssId, url) {
    if (!document.getElementById(cssId))
    {
        var head  = document.getElementsByTagName('head')[0];
        var link  = document.createElement('link');
        link.id   = cssId;
        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.href = url;
        link.media = 'all';
        head.appendChild(link);
    }
}

function isMobilePhone()
{
    var check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
}

Element.prototype.remove = function() {
    this.parentElement.removeChild(this);
}
NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
    for(var i = this.length - 1; i >= 0; i--) {
        if(this[i] && this[i].parentElement) {
            this[i].parentElement.removeChild(this[i]);
        }
    }
}

var head  = document.getElementsByTagName('head')[0];
var style  = document.createElement('style');
style.type = 'text/css';
head.appendChild(style);

style.innerHTML = '.up-widget {z-index: 9997;height: 100%;width: 100%;position: fixed;left: 0;top: 0;' +
    'animation: fadein 0.15s; -webkit-animation: fadein 0.15s; -moz-animation: fadein 0.15s;' +
    '-ms-animation: fadein 0.15s;-o-animation: fadein 0.15s;background: rgba(0, 0, 0, 0.6);}' +
    '.widget-frame {height: 100%;width: 100%;position: fixed;z-index: 9999;border: 0 none; left: 0px; top: 0px;}' +
    '.thumbnail-preview {position: absolute;top: 6%;left: 50%;z-index: 2;margin-top: 160px;' +
    'margin-left: -32px;}@keyframes loader {0% { transform: rotate(0deg); }100% { transform: rotate(360deg); }}' +
    '@-webkit-keyframes loader {0% { -webkit-transform: rotate(0deg); }100% { -webkit-transform: rotate(360deg); }}' +
    '@-moz-keyframes loader {0% { -moz-transform: rotate(0deg); }100% { -moz-transform: rotate(360deg); }}' +
    '@-ms-keyframes loader {0% { -ms-transform: rotate(0deg); }100% { -ms-transform: rotate(360deg); }}' +
    '@-o-keyframes loader {0% { -o-transform: rotate(0deg); }100% { -o-transform: rotate(360deg); }}' +
    '@keyframes fadein {0% { transform: rotate(0deg); }100% { transform: rotate(360deg); }}' +
    '@-webkit-keyframes fadein {0% { -webkit-transform: rotate(0deg); }100% { -webkit-transform: rotate(360deg); }}' +
    '@keyframes fadein{from{opacity:0;}to{opacity:1;}}@-webkit-keyframes fadein {from{opacity:0;}to{opacity:1;}}' +
    '@-moz-keyframes fadein {from{opacity:0;}to{opacity:1;}}@-ms-keyframes fadein {from{opacity:0;}to{opacity:1;}}' +
    '@-o-keyframes fadein {from{opacity:0;}to{opacity:1;}}';

var UnitPay = function(params) {
    this.success = function(handler) {
        UnitPay.success = handler;
    };

    this.error = function(handler) {
        UnitPay.error = handler;
    };

    var wrapper = document.createElement("div");
    wrapper.className = 'up-widget';

    var loader = document.createElement("div");
    loader.className = "thumbnail-preview";
    loader.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64" fill="white">' +
        '<circle id="circle1" transform="translate(8 0)" cx="0" cy="32" r="0">' +
        '<animate attributeName="r" values="0; 8; 0; 0" dur="1.2s" repeatCount="indefinite" begin="0" keytimes="0;0.2;0.7;1" keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.6 0.4 0.8" calcMode="spline" /></circle>' +
        '<circle transform="translate(24 0)" cx="0" cy="32" r="0">' +
        '<animate attributeName="r" values="0; 8; 0; 0" dur="1.2s" repeatCount="indefinite" begin="0.3" keytimes="0;0.2;0.7;1" keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.6 0.4 0.8" calcMode="spline" /></circle>' +
        '<circle transform="translate(40 0)" cx="0" cy="32" r="0">' +
        '<animate attributeName="r" values="0; 8; 0; 0" dur="1.2s" repeatCount="indefinite" begin="0.6" keytimes="0;0.2;0.7;1" keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.6 0.4 0.8" calcMode="spline" />' +
        '</circle></svg>';

    wrapper.appendChild(loader);

    this.createWidget = function(params) {
        var publicKey = null;
        if (params['publicId']) {
            publicKey = params['publicId'];
        }
        if (params['publicKey']) {
            publicKey = params['publicKey'];
        }

        var iframeFormUrl = 'https\x3A\x2F\x2Fwidget.unitpay.ru\x2Fpay\x2F\x40PUBLIC_ID\x40';
        iframeFormUrl = iframeFormUrl.replace('@PUBLIC_ID@', publicKey) + '?';

        var iframe = document.createElement("iframe");
        iframe.name = "widget_frame";
        iframe.className = "widget-frame";
        iframe.src = iframeFormUrl;
        iframe.style.visibility = 'hidden';
        iframe.setAttribute('onload', "this.style.visibility='visible';");

        if (params['cashItems']) {
            if (Object.prototype.toString.call(params['cashItems']) == '[object Array]') {
                params['cashItems'] = UnitPay.base64.encode(JSON.stringify(params['cashItems']));
            }
        }

        if (params['paymentType'] === 'sms') {
            delete params['paymentType'];
        }

        if (params['paymentType']) {
            iframe.src = iframe.src.replace('\?', '/'+params['paymentType']+'?');
            delete params['paymentType'];
        }

        if (params['hideMenu'] === 'true'
            || params['hideOtherMethods'] === 'true') {
            params['hideMenu'] = 'true';
        }

        var paymentFormUrl = 'https\x3A\x2F\x2Funitpay.ru\x2Fpay\x2F\x40PUBLIC_ID\x40\x2Fcard';
        paymentFormUrl = paymentFormUrl.replace('@PUBLIC_ID@', publicKey) + '?';

        if (isMobilePhone()) {
            params['hideMenu'] = typeof params['hideMenu'] == 'string' ? params['hideMenu'] : 'true';
            paymentFormUrl += UnitPay.buildQuery(params);
            window.location.href = paymentFormUrl;
            return;
        }

        params['animate'] = 'false';
        iframe.src += UnitPay.buildQuery(params);

        wrapper.appendChild(iframe);
        document.body.appendChild(wrapper);
    };

    if (params !== undefined) {
        this.createWidget(params);
    }
};


UnitPay.base64 = {

    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // public method for encoding
    encode : function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = this._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
                this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },

    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = this._utf8_decode(output);

        return output;

    },

    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

}

UnitPay.buildQuery = function(obj, prefix) {
    var str = [], p;
    for(p in obj) {
        if (obj.hasOwnProperty(p)) {
            var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
            str.push((v !== null && typeof v === "object") ?
                UnitPay.buildQuery(v, k) :
                encodeURIComponent(k) + "=" + encodeURIComponent(v));
        }
    }
    return str.join("&");
}

UnitPay.success = function (params) {};
UnitPay.error = function (message, params) {};

window.onmessage = function(event) {
    var data = null;
    try {
        data = JSON.parse(event.data);
    } catch (e) {}
    if (!data) {
        return;
    }
    if (data.action == "close") {
        var w = document.body.getElementsByClassName('up-widget');
        var pr = document.body.getElementsByClassName('thumbnail-preview').remove();
        setTimeout(function () {
            while(w[0]) {
                w[0].parentNode.removeChild(w[0]);
            }
        }, 200);
    }

    if (data.action == "success") {
        UnitPay.success(data.params);
    }

    if (data.action == "error") {
        UnitPay.error(data.message, data.params);
    }

    if (data.action == "doPayment") {
        var w = document.body.getElementsByClassName('widget_frame');
        for(var i = 0; i < w.length; i++) {
            w[i].style.background = 'white';
        }
    }

    if (data.action == "openReceipt") {
        var w = document.body.getElementsByClassName('widget_frame');
        for(var i = 0; i < w.length; i++) {
            w[i].style.background = 'transparent';
        }
    }
};