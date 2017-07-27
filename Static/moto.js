(function(){
    var message = document.getElementById('message');
    var id_question = document.getElementById('question');
    var counter = document.getElementById('counter');
    setInterval(function(){
        --counter.innerHTML;
        if (counter.innerHTML < 0) {
            counter.innerHTML = 5;
            //console.log(message.value);
            $.post('/java/moto/send/', {message: message.value, id_question: id_question.value}, function(data){
                console.log(data);
            });
            message.value = '';
        }        
    }, 1000);
    
})();