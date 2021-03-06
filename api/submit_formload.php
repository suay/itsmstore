<!DOCTYPE html>
<html>
<head>
    <title>BootstrapValidator demo</title>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/js/bootstrapValidator.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <!-- form: -->
            <section>
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="page-header">
                        <h2>Sign up</h2>
                    </div>

                    <form id="defaultForm" method="post" class="form-horizontal" action="send_email.php">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Full name</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First name" />
                            </div>
                           <!--  <div class="col-lg-4">
                                <input type="text" class="form-control" name="lastName" placeholder="Last name" />
                            </div> -->
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Username</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="username" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Email address</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control" name="email" />
                            </div>
                        </div>

                        <!-- <div class="form-group">
                            <label class="col-lg-3 control-label">Password</label>
                            <div class="col-lg-5">
                                <input type="password" class="form-control" name="password" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Retype password</label>
                            <div class="col-lg-5">
                                <input type="password" class="form-control" name="confirmPassword" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Gender</label>
                            <div class="col-lg-5">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="gender" value="male" /> Male
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="gender" value="female" /> Female
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="gender" value="other" /> Other
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Languages</label>
                            <div class="col-lg-5">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="languages[]" value="english" /> English
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="languages[]" value="french" /> French
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="languages[]" value="german" /> German
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="languages[]" value="russian" /> Russian
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="languages[]" value="other" /> Other
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Programming Languages</label>
                            <div class="col-lg-5">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="programs[]" value="net" /> .Net
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="programs[]" value="java" /> Java
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="programs[]" value="c" /> C/C++
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="programs[]" value="php" /> PHP
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="programs[]" value="perl" /> Perl
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="programs[]" value="ruby" /> Ruby
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="programs[]" value="python" /> Python
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="programs[]" value="javascript" /> Javascript
                                    </label>
                                </div>
                            </div>
                        </div> -->

                        <div class="form-group">
                            <div class="col-lg-9 col-lg-offset-3">
                                <button type="submit" class="btn btn-primary">Sign up</button>
                                <button type="button" class="btn btn-info" id="validateBtn">Manual validate</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
            <!-- :form -->
        </div>
    </div>

<script type="text/javascript">
$(document).ready(function() {
    $('#defaultForm')
        .bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                firstName: {
                    validators: {
                        notEmpty: {
                            message: 'The first name is required and cannot be empty'
                        }
                    }
                },
                lastName: {
                    validators: {
                        notEmpty: {
                            message: 'The last name is required and cannot be empty'
                        }
                    }
                },
                username: {
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The username is required and cannot be empty'
                        },
                        stringLength: {
                            min: 6,
                            max: 30,
                            message: 'The username must be more than 6 and less than 30 characters long'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9_\.]+$/,
                            message: 'The username can only consist of alphabetical, number, dot and underscore'
                        },
//                        remote: {
//                            url: 'remote.php',
//                            message: 'The username is not available'
//                        },
                        different: {
                            field: 'password',
                            message: 'The username and password cannot be the same as each other'
                        }
                    }
                },
                email: {
                    validators: {
                        emailAddress: {
                            message: 'The input is not a valid email address'
                        }
                    }
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: 'The password is required and cannot be empty'
                        },
                        identical: {
                            field: 'confirmPassword',
                            message: 'The password and its confirm are not the same'
                        },
                        different: {
                            field: 'username',
                            message: 'The password cannot be the same as username'
                        }
                    }
                },
                confirmPassword: {
                    validators: {
                        notEmpty: {
                            message: 'The confirm password is required and cannot be empty'
                        },
                        identical: {
                            field: 'password',
                            message: 'The password and its confirm are not the same'
                        },
                        different: {
                            field: 'username',
                            message: 'The password cannot be the same as username'
                        }
                    }
                },
                gender: {
                    validators: {
                        notEmpty: {
                            message: 'The gender is required'
                        }
                    }
                },
                'languages[]': {
                    validators: {
                        notEmpty: {
                            message: 'Please specify at least one language you can speak'
                        }
                    }
                },
                'programs[]': {
                    validators: {
                        choice: {
                            min: 2,
                            max: 4,
                            message: 'Please choose 2 - 4 programming languages you are good at'
                        }
                    }
                }
            }
        })
        .on('error.form.bv', function(e) {
            console.log('error.form.bv');

            // You can get the form instance and then access API
            var $form = $(e.target);
            console.log($form.data('bootstrapValidator').getInvalidFields());

            // If you want to prevent the default handler (bootstrapValidator._onError(e))
            // e.preventDefault();
        })
        .on('success.form.bv', function(e) {
            console.log('success.form.bv');

            // If you want to prevent the default handler (bootstrapValidator._onSuccess(e))
            // e.preventDefault();
        })
        .on('error.field.bv', function(e, data) {
            console.log('error.field.bv -->', data);
        })
        .on('success.field.bv', function(e, data) {
            console.log('success.field.bv -->', data);
        })
        .on('status.field.bv', function(e, data) {
            // I don't want to add has-success class to valid field container
            data.element.parents('.form-group').removeClass('has-success');

            // I want to enable the submit button all the time
            data.bv.disableSubmitButtons(false);
        });

    // Validate the form manually
    $('#validateBtn').click(function() {
        $('#defaultForm').bootstrapValidator('validate');
    });
});
</script>
</body>
</html>