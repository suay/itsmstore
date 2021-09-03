<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bootstrap contact form with PHP example by BootstrapBay.com.">
    <meta name="author" content="BootstrapBay.com">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/js/bootstrapValidator.min.js"></script>
	<!-- <script src="js/validation.js"></script> -->
	<!-- <link rel="stylesheet" href="css/style.css"> -->
	<script type="text/javascript">
		$(document).ready(function() {
	    // $('#contact_form').bootstrapValidator({        
	    //     feedbackIcons: {
	    //         valid: 'glyphicon glyphicon-ok',
	    //         invalid: 'glyphicon glyphicon-remove',
	    //         validating: 'glyphicon glyphicon-refresh'
	    //     },
	    //     fields: {
	    //         fname: {
	    //             validators: {
	    //                     stringLength: {
	    //                     min: 2,
	    //                 },
	    //                     notEmpty: {
	    //                     message: 'Please enter your first name'
	    //                 }
	    //             }
	    //         },
	    //          sitename: {
	    //             validators: {
	    //                 regexp: {
     //                    	regexp: /^[a-zA-Z0-9_\.]+$/,
     //                	},
	    //                 notEmpty: {
	    //                     message: 'Please enter your site name'
	    //                 }
	    //             }
	    //         },
	    //         email: {
	    //             validators: {
	    //                 notEmpty: {
	    //                     message: 'Please enter your email address'
	    //                 },
	    //                 emailAddress: {
	    //                     message: 'Please enter a valid email address'
	    //                 }
	    //             }
	    //         },
	    //         tel: {
	    //             validators: {
	    //                  stringLength: {
	    //                     min: 10,
	    //                     max: 0,
	    //                     message:'Please enter number 10 digit'
	    //                 },
	    //                 number: true
	    //             }
	    //         }
	    //         }
	    //     }).on('success.form.bv', function(e) {           
	    //         e.preventDefault();
	    //         spinner.hide();                       
	    //         $.post($form.attr('action'), $form.serialize(), function(result) {                
	    //             $("#message").html(result.message).addClass('show');
	    //             $("#contact_form").find("input[type=text], input[type=email], textarea").val("");
	    //         }, 'json');
	    //     });
	    
	    var spinner = $('#loader');
	    $('#submit').click(function(e){
        e.preventDefault();
				spinner.show();

        var name = $("#name").val();
        var email = $("#email").val();
        //var msg_subject = $("#msg_subject").val();
        var company = $("#company").val();
        var tel = $('#tel').val();
        var voucher = $('#voucher').val();
        var policys = $('#policys').val();

					$.ajax({
            type: "POST",
            url: "http://localhost:8000/itsmstore4/api/formProcess.php",
            dataType: "json",
            data: {name:name, email:email, company:company, tel:tel, voucher:voucher, policys:policys},
            // Set to 50 seconds for timeout limit
            timeout: 50000,
            success : function(data){
                if (data.code == "200"){
                	// $(".display-error").html("<ul>"+data.msg+"</ul>");
                 //  $(".display-error").css("display","block");
                  location.replace("http://localhost:8000/itsmstore4/api/thankyou_free.php");
                  spinner.hide();
	                    
                } else {
                    $(".display-error").html("<ul>"+data.msg+"</ul>");
                    $(".display-error").css("display","block");
                    spinner.hide();
                }
            },
            error: function(xhr, textStatus, errorThrown) { //request, status, error
                    if (textStatus == 'timeout') {
	                    $(".display-error").html("<ul>The system has saved successfully. Please wait for an email reply.</ul>");
	                    $(".display-error").css("display","block");
	                    spinner.hide();
                    }
            }
        });

        


      });
		});




</script>
	<style type="text/css">
		/*body{
				background-color: #25274d;
			}*/
			.contactfree{
				padding: 4%;
				height: 600px;
			}
			.col-md-3{
				background: #f7d5cd;
				padding: 4%;
				border-top-left-radius: 0.5rem;
				border-bottom-left-radius: 0.5rem;
			}
			.contact-info{
				margin-top:10%;
			}
			.contact-info img{
				margin-bottom: 15%;
			}
			.contact-info h2{
				margin-bottom: 10%;
			}
			.col-md-9{
				background: #fff;
				padding: 3%;
				border-top-right-radius: 0.5rem;
				border-bottom-right-radius: 0.5rem;
			}
			.contact-form label{
				font-weight:600;
			}
			.contact-form button{
				background: #25274d;
				color: #fff;
				font-weight: 600;
				width: 25%;
			}
			.contact-form button:focus{
				box-shadow:none;
			}
			.help-block {
				color:#f1160a;
			}
			/*Hidden class for adding and removing*/
    .lds-dual-ring.hidden {
        display: none;
    }
    .col-md-3-do{
		width: 45%;
    padding: .375rem .75rem;
    font-size: 1rem;
    /*line-height: 1.5;*/
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: .25rem;
			}
		.fcolor{
			color: red;
		}

   #loader {
		  display: none;
		  position: fixed;
		  top: 0;
		  left: 0;
		  right: 0;
		  bottom: 0;
		  width: 100%;
		  background: rgba(0,0,0,0.75) url(http://staging2.netkasystem.com/itsmstore/wp-content/uploads/2021/04/loading4.svg) no-repeat center center;
		  z-index: 10000;
		}

	</style>

	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/contact.js"></script> -->
  </head>
  <body>
 <div class="overlay"></div>
	<div class="container contactfree">	
	<div class="row">
			
		<div class="col-md-9">
		 <!-- <form action="send_email.php" method="post" id="contact_form">
			<div class="contact-form">
				<div id="message" class="alert alert-danger alert-dismissible fade"></div>
				<div class="form-group">				  
				  <label class="control-label col-sm-2" for="fname">First Name - Last Name*:</label>
				  <div class="col-sm-10">          
					<input type="text" class="form-control" id="fname" placeholder="Enter First Name - Last Name" name="fname">
				  </div>
				</div>
				<div class="form-group">
				  <label class="control-label col-sm-2" for="sitename">Sub Domain Name*:</label>
				  <div class="col-sm-10">          
					<input type="text" class="form-control" id="sitename" placeholder="Sub Domain Name" name="sitename">
					<label for="showexample">.itsmnetka.com</label>
				  </div>
				</div>
				<div class="form-group">
				  <label class="control-label col-sm-2" for="email">Email*:</label>
				  <div class="col-sm-10">
					<input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
				  </div>
				</div> -->
				<!-- <div class="form-group">
				  <label class="control-label col-sm-2" for="comment">Comment*:</label>
				  <div class="col-sm-10">
					<textarea class="form-control" rows="5" name="comment" id="comment"></textarea>
				  </div>
				</div> -->
				<!-- <div class="form-group">
				  <label class="control-label col-sm-2" for="tel">Phone Number:</label>
				  <div class="col-sm-10">
					<input type="text" class="form-control" id="tel" placeholder="Phone Number" name="tel">
				  </div>
				</div>
				<div class="form-group">
				  <label class="control-label col-sm-2" for="voucher">Voucher:</label>
				  <div class="col-sm-10">
					<input type="voucher" class="form-control" id="voucher" placeholder="Voucher Code" name="voucher">
				  </div>
				</div>
				<div class="form-group">        
				  <div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-default" id="submit">Submit</button>
				  </div>
				</div>
			</div> -->

			<form id="contactForm" name="contact" role="form">
					<div class="alert alert-danger display-error" style="display: none">
    </div>
				<div class="modal-body">				
					<div class="form-group">
						<label for="name">Name</label><label style="color: red">*</label>
						<input type="text" name="name" id="name" class="form-control" placeholder="First & Last Name">
					</div>
					<div class="form-group">
						<label for="email">Email address*</label>
						<input type="email" name="email" id="email" class="form-control" placeholder="example@domain.com">
					</div>
					<div class="form-group">
						<label for="company">Your preferred site name*</label><br>
						<input type="text" name="company" id="company" class="col-md-3-do" placeholder="Your preferred site name">
						<label for="showexample"><b>.itsmnetka.com</b></label>
					</div>
					<div class="form-group">
						<label for="tel">Phone number</label>
						<input type="text" name="tel" id="tel" class="form-control" placeholder="081xxxxxxx">
					</div>
					<div class="form-group">
						<label for="tel">Voucher code</label>
						<input type="text" name="voucher" id="voucher" class="form-control" placeholder="Voucher Code">
					</div>
					<div class="form-group">
						<input type="checkbox" name="policys" id="policys" value="ischeck">
  					<label for="policy"> Terms and Privacy</label>
					</div>					
				</div>
				<div class="modal-footer">					
					<button type="reset" class="btn btn-default" data-dismiss="modal" onclick="window.location.href='http://localhost:8000/itsmstore4';">Cencel</button>
					<input type="submit" class="btn btn-success" id="submit" value="Sign up for free">
				</div>
				<div id="loader"></div>
			</form>
		</div>		
	</div>
</div>	
	<!-- <div id="contact"><button type="button" class="btn btn-info btn" data-toggle="modal" data-target="#contact-modal">Show Contact Form</button></div>
<div id="contact-modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<a class="close" data-dismiss="modal">×</a>
				<h3>Contact Form</h3>
			</div>
			<form id="contactForm" name="contact" role="form">
				<div class="modal-body">				
					<div class="form-group">
						<label for="name">Name</label>
						<input type="text" name="name" class="form-control">
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" name="email" class="form-control">
					</div>
					<div class="form-group">
						<label for="message">Message</label>
						<textarea name="message" class="form-control"></textarea>
					</div>					
				</div>
				<div class="modal-footer">					
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-success" id="submit">
				</div>
			</form>
		</div>
	</div>
</div> -->
 </body>
</html>