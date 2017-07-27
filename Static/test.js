/*
 * Типы данных
 *  - number (целое число, число с плавающей точкой)
 *  - string (строка)
 *  - null (пустое значение)
 *  - boolean (true, false)
 *  - undefined
 *
 *  - object -> function + array
 */
(function() {
    var num = {
        number: 45,
        name: 'текст',
        array: ['test', 'netets'],
        function: function(a) {
            return a;
        },
    }
    console.log(num.name);
})();

(function(){
    var counter = document.getElementById('counter');
    var form = document.getElementById('my-form');
    var interval = setInterval(function(){
        --counter.innerHTML;
        if (counter.innerHTML == 0) {
            form.setAttribute('class', 'none');
            setTimeout(function () {
                form.parentNode.removeChild(form);
            }, 1000);
        }
    }, 1000);
})();

(function () {
    var content = document.getElementById('content');
    content.onclick = function(event) {
        var id = event.target.getAttribute('id');
        if (id == 'content') {
            return;
        }
        var name = document.getElementById(id);
        var count = name.getAttribute('data-count');
        name.innerHTML = name.innerHTML + ' +';
        if (count <= 5) {
            name.setAttribute('class', 'green');
        } else if (count <= 10) {
            name.setAttribute('class', 'orange');
        } else if (count <= 15) {
            name.setAttribute('class', 'red');
        } else {
            setTimeout(function() {
                name.parentNode.removeChild(name);
            }, 1000);

            name.setAttribute('class', 'none');
        }
        name.setAttribute('data-count', ++count);
        console.log(count);
    }
})();

(function () {
    document.onkeyup = function() {
        if (document.getElementById('myInput').value == '') {
            return;
        }
        document.getElementById('counter').innerHTML = document.getElementById('myInput').value;
    }
})();
