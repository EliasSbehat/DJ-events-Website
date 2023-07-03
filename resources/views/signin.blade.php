<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.head')
</head>

<body>

    <!-- Start wrapper-->
    <div class="container justify-content-center d-flex">
        <section class="vh-100">
            <div class="container py-5 h-100">
                <div class="row d-flex align-items-center justify-content-center h-100">
                    <div class="col-md-8 col-lg-7 col-xl-6">
                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.svg"
                        class="img-fluid" alt="Phone image">
                    </div>
                    <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
                        <form>
                        <!-- Email input -->
                            <div class="mb-4">
                                <label class="form-label" for="form1Example13">Email address</label>
                                <input type="email" id="form1Example13" class="form-control form-control-lg" />
                            </div>

                            <!-- Password input -->
                            <div class="mb-4">
                                <label class="form-label" for="form1Example23">Password</label>
                                <input type="password" id="form1Example23" class="form-control form-control-lg" />
                            </div>

                            <div class="d-flex justify-content-around align-items-center mb-4">
                                <!-- Checkbox -->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="form1Example3" checked />
                                    <label class="form-check-label" for="form1Example3"> Remember me </label>
                                </div>
                            </div>

                            <!-- Submit button -->
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>

                                            <!-- Register buttons -->
                            <div class="text-center mt-4">
                                <p>Not a member? <a href="/register">Register</a></p>
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
        $(".signin_btn").click(function() {
            signIn();
        });
        $("#pwd").keypress(function() {
            if (event.keyCode==13) {
                signIn();
            }
        });

        function signIn() {
            var name = $("#username").val();
            var pwd = $("#pwd").val();
            $.get(
                "signin/checkuser", {
                    n: name, //name
                    p: pwd //pwd
                },
                function(res) {
                    if (res == "wrong user") {
                        alert("The user name is wrong.");
                        $("#username").focus();
                    } else if (res == "wrong pwd") {
                        alert("The password is wrong.");
                        $("#pwd").focus();
                    } else {
                        sessionStorage.setItem("x-t", res);
                        sessionStorage.setItem("user", name);
                        location.href = 'cvlist';
                    }
                }
            );
        }
    </script>
</body>

</html>