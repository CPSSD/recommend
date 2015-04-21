$(document).ready(function(){
    /* if( navigator.userAgent.match(/Android/i)
        || navigator.userAgent.match(/webOS/i)
        || navigator.userAgent.match(/iPhone/i)
        || navigator.userAgent.match(/iPad/i)
        || navigator.userAgent.match(/iPod/i)
        || navigator.userAgent.match(/BlackBerry/i)
        || navigator.userAgent.match(/Windows Phone/i)){
    location.href='http://localhost/Tracker/View/mobile/getFilmListMobile.php';
  }*/

    /*if(screen.height < 1000){
        location.href='http://localhost/Tracker/View/mobile/getFilmListMobile.php';
    }*/


    $('.like').unbind('submit');    
    $('.like').on('submit',function(e) {
        console.log("heelo");
        var input = $(this);
        var title = $(input).children('#title').val();
        var id = $(input).children('#id').val();
        var image = $(input).children('#image').val();
        var type = $(input).children('#type').val();
        var dataString = 'title='+ title + '&id='+ id + '&image='+ image + '&type=' + type;
        console.log(dataString);
        var submit = $(input).children('#submit').val();
        if(submit == 'like'){
            $(input).children('#submit').val('unlike');
        }else{
            $(input).children('#submit').val('like');
        }
            $.ajax({
                type: "POST",
                url: "../Model/insertLikes.php",
                data: dataString,
                cache: false,
                error: function (req, msg, obj) {
                  console.log('An error occured while executing a request for: ' + url);
                  console.log('Error: ' + msg);
                }            
            });
        return false;
        });
});
