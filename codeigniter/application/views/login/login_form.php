<div id="content-wrapper" class="d-flex flex-column" style="height: 100vh;">
    <div class="text-center mt-4">
        <div class="d-flex align-items-center justify-content-center" style="font-size: 2.5rem;">
            <div class="rotate-n-15">
                <i class="fas fa-swatchbook"></i>
            </div>
            <div class="mx-3">LMS</div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-1"></div>
        <div class="col-10">
            <div class="card shadow mb-5">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Login</h6>
                </div>

                <div class="card-body">
                    <form method="post" id="login-form">
                        <div class="row">
                            <div class="col mb-4">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" id="username">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-4">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block" type="submit" id="login-button">
                            <span class="spinner-border spinner-border-sm" style="display: none;" role="status"></span>
                            <span class="button-text">
                                Login
                            </span>
                        </button>
                    </form>
                    <div class="row mt-2">
                        <div class="col-6">
                            <a href="<?= base_url("forgot_password") ?>" role="button" class="btn btn-primary btn-block">Forgot Password</a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url("register") ?>" role="button" class="btn btn-primary btn-block">Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-1"></div>
    </div>
</div>

<script>
    $("#login-form").submit(function(e) {
        e.preventDefault();
        $("span.spinner-border.spinner-border-sm").show();
        $("#login-button span.button-text").html("Loading...");
        $.ajax({
            url: baseUrl + "userapi/login",
            type: "POST",
            dataType: "JSON",
            data: new FormData(this),
            processData: false,
            contentType: false,
            cache: false,
            dataType: "JSON",
            success: function success(res) {
                if (res.response) {
                    window.location.href = baseUrl + "user";
                } else {
                    $("span.spinner-border.spinner-border-sm").hide();
                    $("#login-button").html("Login");
                    $("input").addClass("is-invalid");
                    $(".invalid-feedback").html(res.message);
                }
            },
            error: function error(jqxhr, err, textStatus) {
                errorHandler(jqxhr, err, textStatus);
            },
        });
    });
</script>