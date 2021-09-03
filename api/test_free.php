<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Signup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  </head>
  <body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-inverse">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.html">First Day Training</a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="index.html"><span class="glyphicon glyphicon-step-backward"></span>Back</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
            <div class="row centered-form">
            <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                            <h3 class="panel-title">Please sign up <small>God  bless!</small></h3>
                            </div>
                            <div class="panel-body">
                            <form role="form" method="post">
                    <div class="form-group">
                      <input type="text" name="user_name" id="username" class="form-control input-sm" placeholder="Username" autofocus >
                    </div>
                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                        <div class="form-group">
                                <input type="text" name="first_name" id="first_name" class="form-control input-sm" placeholder="First Name" >
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="last_name" id="last_name" class="form-control input-sm" placeholder="Last Name" >
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="email" name="email_address" id="email" class="form-control input-sm" placeholder="Email Address" >
                                </div>

                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <input type="password" name="password" id="password" class="form-control input-sm" placeholder="Password" >
                                        </div>
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control input-sm" placeholder="Confirm Password"  onChange="checkPasswordMatch();" >
                                        </div>
                                    </div>
                                </div>
                    <div>
                        <div id="divCheckPasswordMatch"></div>
                    </div>
                                <input type="submit" id="submit" value="Register" class="btn btn-info btn-block">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  </body>
  <!-- Password Validation -->
  <script>
      function checkPasswordMatch() {
      var password = $("#password").val();
      var confirmPassword = $("#password_confirmation").val();

      if (password != confirmPassword)
          $("#divCheckPasswordMatch").html("Passwords do not match!");
      else
          $("#divCheckPasswordMatch").html("Passwords match.");
        }

    $(document).ready(function () {
     $("#password_confirmation").keydown(checkPasswordMatch);
    });
  </script>

  <script>
        $(document).ready(function(){
        $("#submit").click(function(){
        var username = $("#username").val();
        var firstname = $("#first_name").val();
        var lastname = $("#last_name").val();
        var email = $("#email").val();
        var password = $("#password").val();
        var checkpassword = $("#password_confirmation").val();
        // Returns successful data submission message when the entered information is stored in database.
        var dataString = 'user_name='+ username + '&first_name='+ firstname + '&last_name='+ lastname + '&email_address='+ email + '&password='+ password;
        if(username==''|| firstname=='' || lastname=='' || email==''||password=='')
        {
          alert("Please Fill All Fields");
        }
        else if(!filter_var(email, FILTER_VALIDATE_EMAIL))
        {
          alert("Invalid email format");
        }
        else if(password != checkpassword)
        {
          alert("Password does not match.");
        }
        else
        {
        // AJAX Code To Submit Form.
        $.ajax({
        type: "POST",
        url: "signupbackend.php",
        data: dataString,
        cache: false,
        success: function(result){
        alert(result);
        }
        });
        }
        return false;
        });
        });
  </script>

  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</html>