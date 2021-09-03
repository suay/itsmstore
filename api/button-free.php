<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bootstrap contact form with PHP example by BootstrapBay.com.">
    <meta name="author" content="BootstrapBay.com">
    <title>Bootstrap Contact Form With PHP Example</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
		<style type="text/css">
			/*Hidden class for adding and removing*/
			.display-none {
			    display: none !important;
			}

			/*Add an overlay to the entire page blocking any further presses to buttons or other elements.*/
			.overlay {
			    position: fixed;
			    top: 0;
			    left: 0;
			    width: 100%;
			    height: 100vh;
			    background: rgba(0,0,0,.8);
			    z-index: 999;
			    opacity: 1;
			    transition: all 0.5s;
			}
			 
			/*Spinner Styles*/
			.lds-dual-ring {
			    display: inline-block;
			}
			.lds-dual-ring:after {
			    content: " ";
			    display: block;
			    width: 64px;
			    height: 64px;
			    margin: 5% auto;
			    border-radius: 50%;
			    border: 6px solid #fff;
			    border-color: #fff transparent #fff transparent;
			    animation: lds-dual-ring 1.2s linear infinite;
			}
			@keyframes lds-dual-ring {
			    0% {
			        transform: rotate(0deg);
			    }
			    100% {
			        transform: rotate(360deg);
			    }
			}
			#getDataBtn{
			    background: #e2e222;
			    border: 1px solid #e2e222;
			    padding:  10px 20px;
			}
			.text-center{
			    text-align: center;
			}
			#data-table table{
			    margin: 20px auto;
			}
		</style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {


      $('#submit').click(function(e){
        e.preventDefault();


        var name = $("#name").val();
        var email = $("#email").val();
        //var msg_subject = $("#msg_subject").val();
        var company = $("#company").val();
        var tel = $('#tel').val();
        var voucher = $('#voucher').val();


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
                			$('#contact-modal').removeClass('display-none')
	                    alert("Success: " +data.msg);
	                    $('.modal').each(function(){
			                    $(this).modal('hide');
			                });
			               //  //$('#contact-modal').html("<img src='img/bg/success.gif'>").delay(3000).fadeOut(450);
				              // // $('body').removeClass('modal-open');
				              // // $('.modal-backdrop.show').css('opacity','0');
				              // // $('.modal-backdrop').css('z-index','-1')
				              // $('#contact-modal').hide();
                } else {
                    $(".display-error").html("<ul>"+data.msg+"</ul>");
                    $(".display-error").css("display","block");
                }
            }
        });


      });
  });
	</script>
  </head>
  <body>
	<div id="contact"><button type="button" class="btn btn-info btn" data-toggle="modal" data-target="#contact-modal">Free Trial</button></div>
<div id="contact-modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<a class="close" data-dismiss="modal">×</a>
				<h3>Start your 10-day free trial</h3>
			</div>
			<form id="contactForm" name="contact" role="form">
					<div class="alert alert-danger display-error" style="display: none">
    </div>
				<div class="modal-body">				
					<div class="form-group">
						<label for="name">Name*</label>
						<input type="text" name="name" id="name" class="form-control" placeholder="First & Last Name">
					</div>
					<div class="form-group">
						<label for="email">Email*</label>
						<input type="email" name="email" id="email" class="form-control" placeholder="example@domain.com">
					</div>
					<div class="form-group">
						<label for="company">Sub Domain Name*</label>
						<input type="text" name="company" id="company" class="form-control" placeholder="example.itsmnetka.com">
						<label for="showexample">.itsmnetka.com</label>
					</div>
					<div class="form-group">
						<label for="tel">Phone Number</label>
						<input type="text" name="tel" id="tel" class="form-control" placeholder="081xxxxxxx">
					</div>
					<div class="form-group">
						<label for="tel">Voucher</label>
						<input type="text" name="voucher" id="voucher" class="form-control" placeholder="Voucher Code">
					</div>					
				</div>
				<div class="modal-footer">					
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-success" id="submit">
				</div>
				<div id="loader" class="lds-dual-ring display-none overlay"></div>
			</form>
		</div>
	</div>
</div>
 </body>
</html>