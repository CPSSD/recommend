$(document).ready(function(){
var likeButton= document.getElementsByName("like");
  //  for(var i=0;i<likeBut.length;i++){
    //    likeBut[i].onclick = function(){
      //  likemsg(HTTP);
   //}
console.log(likeButton);
    for(var i = 0; i < likeButton.length; i++){
        var string = "#like" + i;
        var getTitle = "#title" + i;
        var getId = "#id" + i;
        var getImage = "#image" + i;
        $(string).submit(function(){
            var title = $(getTitle).val();
            var id = $(getId).val();
            var image = $(getImage).val();
            var type = $("#type").val();
            var dataString = 'title='+ title + '&id='+ id + '&image='+ image + '&type=' + type;
            var url = "../Model/insertLikes.php";
            // AJAX Code To Submit Form.
            $.ajax({
                type: "POST",
                url: "../Model/insertLikes.php",
                data: dataString,
                cache: false,
                success: function(result){
                    alert(result);
                },
                error: function (req, msg, obj) {
                  console.log('An error occured while executing a request for: ' + url);
                  console.log('Error: ' + msg);
                }            
            });
        return false;
        });
    }
});
