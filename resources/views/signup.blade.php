<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.head')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
</head>

<body>

    <!-- Start wrapper-->
    <div class="container justify-content-center d-flex">
        <section class="vh-100">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-md-9 col-lg-6 col-xl-5">
                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                        class="img-fluid" alt="Sample image">
                    </div>
                    <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                        <form>
                            <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                                <p class="lead fw-normal mb-0 me-3">Sign up</p>
                            </div>

                            <!-- Email input -->
                            <div class="mt-4 mb-4">
                                <label class="form-label" for="email_input">Email address</label>
                                <input type="email" id="email_input" class="form-control form-control-lg" placeholder="Enter a valid email address" />
                            </div>

                            <!-- First Name input -->
                            <div class="mt-4 mb-4">
                                <label class="form-label" for="first_name_input">First Name</label>
                                <input type="text" id="first_name_input" class="form-control form-control-lg" placeholder="Enter your first name" />
                            </div>

                            <!-- Last Name input -->
                            <div class="mt-4 mb-4">
                                <label class="form-label" for="last_name_input">Last Name</label>
                                <input type="text" id="last_name_input" class="form-control form-control-lg" placeholder="Enter your last name" />
                            </div>

                            <!-- Phone input -->
                            <div class="mt-4 mb-4">
                                <input type="tel" id="userphone" class="form-control form-control-lg" placeholder="Enter your phone number" />
                            </div>

                            <!-- Password input -->
                            <div class="mb-3">
                                <label class="form-label" for="pwd_input">Password</label>
                                <input type="password" id="pwd_input" class="form-control form-control-lg" placeholder="Enter password" />
                            </div>

                            <!-- Re Password input -->
                            <div class="mb-3">
                                <label class="form-label" for="pwd_input2">Confirm Password</label>
                                <input type="password" id="pwd_input2" class="form-control form-control-lg" placeholder="Enter repeat password" />
                            </div>

                            <div class="text-center text-lg-start mt-4 pt-2">
                                <button type="button" class="btn btn-primary btn-lg"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">Register</button>
                                <p class="small fw-bold mt-2 pt-1 mb-0">Do you have an account? 
                                <a href="/signin" class="link-danger">Login</a></p>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!--wrapper-->

    <!-- Bootstrap core JavaScript-->
    <script>
        const phoneInputField = document.querySelector("#userphone");
        const phoneInput = window.intlTelInput(phoneInputField, {
            utilsScript:
                "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
        var phone = phoneInput.getNumber();
    </script>
</body>

</html>