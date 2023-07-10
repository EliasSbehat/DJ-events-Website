<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.head')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <title>DJ Nick Burrett Karaoke Requests</title>
</head>

<body>

    <!-- Start wrapper-->
    <div class="container justify-content-center d-flex">
        <section class="vh-100">
            <div class="container py-5 h-100">
                <div class="row d-flex align-items-center justify-content-center h-100">
                    <div class="col-md-8 col-lg-7 col-xl-6 d-flex mx-auto justify-content-center">
                        <img src="/assets/imgs/DJ Nick Burrett RGB.png" style="width:70%;" class="img-fluid" alt="Phone image">
                    </div>
                    <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
                        <form>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <!-- Email input -->
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
                                <label class="form-label" for="userphone">Mobile Number</label><br>
                                <input type="tel" id="userphone" name="phone" class="form-control form-control-lg w-100" required placeholder="Enter your phone number" />
                            </div>

                            <!-- Submit button -->
                            <button type="button" class="btn btn-primary signin-btn btn-lg btn-block mb-8">Sign in</button>

                                            <!-- Register buttons -->
                            <!-- <div class="text-center mt-4"> -->
                                <!-- <p>Not a member? <a href="/register">Register</a></p> -->
                            <!-- </div> -->
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

        $(".signin-btn").click(function() {
            signIn();
        });
        $("#password").keypress(function() {
            if (event.keyCode==13) {
                signIn();
            }
        });

        function signIn() {
            var first_name = $("#first_name_input").val();
            var last_name = $("#last_name_input").val();
            var phone = $("#userphone").val();
            $.get(
                "signin/checkuser", {
                    first_name: first_name, //
                    last_name: last_name, //
                    phone: phone
                },
                function(res) {
                    if (res == "wrong user") {
                        alert("Not registered.");
                    } else if (res == "not verified") {
                        alert("Not verified");
                    } else {
                        sessionStorage.setItem("x-t", res);
                        location.href = '/verify';
                    }
                }
            );
        }
    </script>
</body>

</html>