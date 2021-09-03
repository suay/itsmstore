<form method="GET" action="formProcess.php" id="searchform">
        <div class="form-inline">
        <div class="form-group">
            <input type="text" class="form-control" id="fname" name="fname" placeholder="Search" style="min-width: 300px;" required autofocus>
        </div>
        <button type="submit" class="btn btn-dark btn-md">Search</button>
        </div>
</form>
<br>
<div class="modal">
</div>
<style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba( 255, 255, 255, .8 ) 
                        url("{% static 'img/loading.gif' %}") 
                        50% 50% 
                        no-repeat;
            }
        body.loading {
            overflow: hidden;   
        }
        body.loading .modal {
            display: block;
        }
 #loader {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  background: rgba(0,0,0,0.75) url(images/loading2.gif) no-repeat center center;
  z-index: 10000;
}
</style>
<script type="text/javascript">
  var spinner = $('#loader');
  $("#searchform").submit(function(event) {

  /* stop form from submitting normally */
  event.preventDefault();
  spinner.show();

$.ajax({
            type: "POST",
            url: "http://localhost:8000/itsmstore4/api/formProcess.php",
            dataType: "json",
           // data: {name:name, email:email, msg_subject:msg_subject, message:message},
           data: {name:name, email:email, company:company, tel:tel, voucher:voucher},
           //  beforeSend: function () {
           //    $('#loader').removeClass('display-none');
            // },
            success : function(data){
                if (data.code == "200"){
                    spinner.hide();
                    $(".display-error").html("<ul>"+data.msg+"</ul>");
                    $(".display-error").css("display","block");
                      
                } else {
                    $(".display-error").html("<ul>"+data.msg+"</ul>");
                    $(".display-error").css("display","block");
                }
            }
        });
  /* get some values from elements on the page: */
  // var $form = $( this );
  // var url = $form.attr( "action" );
  // //before send
  // $("body").addClass("loading");

  // /* Send the data using post */
  // var posting = $.post(url , $( "#searchform" ).serialize() );

  // /* Alerts the results */
  // posting.done(function( data ) {
  //    //use data
  //    $("body").removeClass("loading");

  // });
});
</script>