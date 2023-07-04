<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.head')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <title>Sign Up</title>
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
                        <form id="register_form" action="/register/register" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                                <p class="lead fw-normal mb-0 me-3">Sign up</p>
                            </div>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Email input -->
                            <!-- <div class="mt-4 mb-4">
                                <label class="form-label" for="email_input">Email address</label>
                                <input type="email" id="email_input" name="email" class="form-control form-control-lg" required placeholder="Enter a valid email address" />
                            </div> -->

                            <!-- First Name input -->
                            <div class="mt-4 mb-4">
                                <label class="form-label" for="first_name_input">First Name</label>
                                <input type="text" id="first_name_input" name="first_name" class="form-control form-control-lg" required placeholder="Enter your first name" />
                            </div>

                            <!-- Last Name input -->
                            <div class="mt-4 mb-4">
                                <label class="form-label" for="last_name_input">Last Name</label>
                                <input type="text" id="last_name_input" name="last_name" class="form-control form-control-lg" required placeholder="Enter your last name" />
                            </div>

                            <!-- Phone input -->
                            <div class="mt-4 mb-4">
                                <input type="tel" id="userphone" name="phone" class="form-control form-control-lg w-100" required placeholder="Enter your phone number" />
                            </div>

                            <!-- Password input -->
                            <!-- <div class="mb-3">
                                <label class="form-label" for="pwd_input">Password</label>
                                <input type="password" id="pwd_input" name="password" class="form-control form-control-lg" required placeholder="Enter password" />
                            </div> -->

                            <!-- Re Password input -->
                            <!-- <div class="mb-3">
                                <label class="form-label" for="pwd_input2">Confirm Password</label>
                                <input type="password" id="pwd_input2" name="password_confirmation" class="form-control form-control-lg" required placeholder="Enter repeat password" />
                            </div> -->

                            <div class="text-center text-lg-start mt-4 pt-2">
                                <button type="submit" class="btn btn-primary register-btn btn-lg" style="padding-left: 2.5rem; padding-right: 2.5rem;">Register</button>
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
            initialCountry: "gb",
            utilsScript:
                "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
        var phone = phoneInput.getNumber();
    </script>
</body>

</html>