Number.prototype.formatMoney = function(c, d, t) {
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

Date.prototype.toDate = function() {
    var date = this;
    var sMonth = padValue(date.getMonth() + 1);
    var sDay = padValue(date.getDate());
    var sYear = date.getFullYear();

    return sYear + "-" + sMonth + "-" + sDay;
};

function padValue(value) {
    return (value < 10) ? "0" + value : value;
};