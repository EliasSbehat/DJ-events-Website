<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.head')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
</head>

<body>

    <!-- Start wrapper-->
    <div class="container justify-content-center">
        <section class="vh-100">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                        <form id="register_form" action="/register/register" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                                <p class="lead fw-normal mb-0 me-3">SMS Verify</p>
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
                            <div class="mt-4 mb-4">
                                <label class="form-label" for="verify_input">Verify Code</label>
                                <input type="text" id="verify_input" name="verify_code" class="form-control form-control-lg" required placeholder="Enter a valid verify code" />
                            </div>
                            <div class="text-center text-lg-start mt-4 pt-2">
                                <button type="submit" class="btn btn-primary register-btn btn-lg" style="padding-left: 2.5rem; padding-right: 2.5rem;">Confirm</button>
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