<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../style/index.css" />
    <script src="../js/index.js"></script>
    <script src="../js/functions.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <link rel="icon" href="img/fox.png">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <title>Foxy P.A.</title>
</head>

<body>
    <div class="verify_div" id="verify_div" style="display: none;">

    </div>

    <div class="mother">
        <div class="child">
            <h1 class="title">Foxy Personal Assistant</h1>
            <div class="main_frame" id="main_frame">
                <div class="left_frame" id="left_frame">
                    <div class="left_x_frame" id="left_x_frame">
                        <div class="login_text">
                            <h3>Welcome back!</h3>
                            <h4>Please log in to access your account.</h4>
                            <h4>
                                If you don't have an account yet, you can sign up below.
                            </h4>
                            <button id="change" onclick="signup()">Register</button>
                        </div>
                    </div>
                    <div class="left_signup_frame" id="left_signup_frame">
                        <!--<iframe class="signup_frame" src="pages/signup.php"></iframe>-->
                        <div class="signup_frame">
                            <h1>
                                Register :
                            </h1>
                            <form onsubmit="sendRegister()">


                                <input class="in" type="text" id="r_fname" placeholder="First Name" required />
                                <input class="in" type="text" id="r_lname" placeholder="Last Name" required />
                                <input class="in" type="text" id="r_username" placeholder="User Name" required />
                                <input class="in" type="email" id="r_email" placeholder="Email" required />
                                <div class="passBox">
                                    <div class="passIN">
                                        <input class="in" id="passBoxIN" type="password" name="password"
                                            placeholder="Password" required />
                                        <input class="in" id="passBoxIN2" type="password" name="cpass"
                                            placeholder="Confirm Password" required />
                                    </div>
                                    <div class="btn_show">
                                        <button id="see_pass_btn" onclick="signup_pass_show()">
                                            <lord-icon src="https://cdn.lordicon.com/vfczflna.json" trigger="in"
                                                delay="1000" stroke="bold" state="morph-cross"
                                                colors="primary:#121331,secondary:#ffa500"
                                                style="width: 50px; height: 50px">
                                            </lord-icon>
                                        </button>
                                    </div>

                                </div>
                                <!--     <input type="tel" id="phone" class="in" placeholder="Phone number"
                                    pattern="\+\d{1,3}\d{1,14}" required>-->
                                <div id="errors" style="display: none;"></div>
                                <div id="out_messages" style="display: none;"></div>
                                <input type="submit" id="gsubmit" value="Register" />
                            </form>
                            <hr>
                            <div class="mobile_note">

                                <p>Already have an account ?</p>
                                <button id="change" onclick="mobile_login()">Login</button>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="right_frame" id="right_frame">
                    <div class="right_x_frame" id="right_x_frame">
                        <div class="signup_text">
                            <h3>Welcome to Foxy P.A.!</h3>

                            <h4>
                                New to Foxy P.A. ? Please provide your first name, last name,
                                email address, password, and confirm password to create an
                                account.
                            </h4>

                            <button id="change" onclick="login()">Login</button>
                        </div>
                    </div>
                    <div class="right_login_frame" id="right_login_frame">
                        <!-- <iframe class="login_frame" src="pages/login.php"></iframe>-->
                        <div class="login_frame">
                            <h1>Login:</h1>
                            <form onsubmit="sendLogin()">
                                <div class="inBox">
                                    <input type="email" id="email" name="log_email" placeholder="Email" required />
                                </div>
                                <div class="inBox">
                                    <div class="passBox">
                                        <input type="password" id="log_password" name="log_password"
                                            placeholder="Password" required />

                                        <button id="log_see_pass_btn" onclick="login_pass_show()">
                                            <lord-icon src="https://cdn.lordicon.com/vfczflna.json" trigger="in"
                                                delay="1000" stroke="bold" state="morph-cross"
                                                colors="primary:#121331,secondary:#ffa500"
                                                style="width: 50px; height: 50px">
                                            </lord-icon>
                                        </button>
                                    </div>
                                </div>

                                <div class="errors" id="log_errors" style="display:none;">
                                    <h4>The username or password you entered is incorrect!!</h4>
                                </div>
                                <input type="submit" id="log_gsubmit" value="LOGIN" />
                            </form>
                            <div class="forgot_pass">
                                <button class="forgot_pass_btn" onclick="showForgotPass()">Forgot Password!</button>
                            </div>
                            <hr>
                            <div class="mobile_note">
                                <p>New here ?</p>
                                <button id="change" onclick="mobile_signup()">Register</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>