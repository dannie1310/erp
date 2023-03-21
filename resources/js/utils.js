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

Array.prototype.diff = function (a) {
    return this.filter(function (i) {
        return ! a.find(function (r) {
            return r.id == i.id;
        })
    });
};

Number.prototype.formatea = function(c, d, t) {
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

String.prototype.formatearkeyUp =function () {
    fin1 = this;
    if (fin1 == "") {
        campo = "";
        return campo;
    } else if (fin1 == ".") {
        campo = ".";
        return campo;
    } else {
        var index = fin1.indexOf(".");

        if (index == -1) {
            fin = fin1.replace(/,/g, '');
            resultado1 = parseFloat(fin).toString();
            var cadena = "";
            cont = 1
            for (m = resultado1.length - 1; m >= 0; m--) {
                if(!isNaN(resultado1.charAt(m)))
                {
                    cadena = resultado1.charAt(m) + cadena
                    cont % 3 == 0 && m > 0 ? cadena = "," + cadena : cadena = cadena
                    cont == 3 ? cont = 1 : cont++
                }
            }
            campo = cadena;
            return campo;
        } else {
            fin = fin1.replace(/,/g, '');
            resultados = fin.split(".");
            var cadena = "";
            cont = 1
            for (m = resultados[0].length - 1; m >= 0; m--) {
                if(!isNaN(resultados[0].charAt(m)))
                {
                    cadena = resultados[0].charAt(m) + cadena
                    cont % 3 == 0 && m > 0 ? cadena = "," + cadena : cadena = cadena
                    cont == 3 ? cont = 1 : cont++
                }
            }
            if (resultados[1]) {
                campo = cadena + "." + resultados[1];
                return campo;
            }else {
                campo = cadena + ".";
                return campo;
            }
        }
    }
}
