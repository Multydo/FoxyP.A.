function signup_a_pass_show() {
    event.preventDefault();
    let button_eye = document.getElementById("see_a_pass_btn");
    button_eye.innerHTML = `
                             <lord-icon
      src="https://cdn.lordicon.com/vfczflna.json"
      trigger="loop"
      delay="500"
      stroke="bold"
      colors="primary:#121331,secondary:#ffa500"
      state="hover-look-around"
      style="width: 50px; height: 50px"
    >
    </lord-icon>
                        `;
    button_eye.onclick = signup_a_pass_hide;
    document.getElementById("adminPass").type = "text";
}
function signup_a_pass_hide() {
    event.preventDefault();
    let button_eye = document.getElementById("see_a_pass_btn");
    button_eye.innerHTML = `      <lord-icon src="https://cdn.lordicon.com/vfczflna.json" trigger="in" delay="500"
                                stroke="bold" colors="primary:#121331,secondary:#ffa500" state="morph-cross" style="width: 50px; height: 50px">
                            </lord-icon>
                       `;
    button_eye.onclick = signup_a_pass_show;
    document.getElementById("adminPass").type = "password";
}
function signup_pass_show() {
    event.preventDefault();
    let button_eye = document.getElementById("see_pass_btn");
    button_eye.innerHTML = `
                             <lord-icon
      src="https://cdn.lordicon.com/vfczflna.json"
      trigger="loop"
      delay="500"
      colors="primary:#121331,secondary:#ffa500"
      stroke="bold"
      state="hover-look-around"
      style="width: 50px; height: 50px"
    >
    </lord-icon>
                        `;
    button_eye.onclick = signup_pass_hide;
    document.getElementById("passBoxIN").type = "text";
    document.getElementById("passBoxIN2").type = "text";
}
function signup_pass_hide() {
    event.preventDefault();
    let button_eye = document.getElementById("see_pass_btn");
    button_eye.innerHTML = `      <lord-icon src="https://cdn.lordicon.com/vfczflna.json" trigger="in" delay="500"
                                stroke="bold" colors="primary:#121331,secondary:#ffa500" state="morph-cross" style="width: 50px; height: 50px">
                            </lord-icon>
                       `;
    button_eye.onclick = signup_pass_show;
    document.getElementById("passBoxIN").type = "password";
    document.getElementById("passBoxIN2").type = "password";
}
function login_pass_show() {
    event.preventDefault();
    let button_eye = document.getElementById("log_see_pass_btn");
    button_eye.innerHTML = `
                             <lord-icon
      src="https://cdn.lordicon.com/vfczflna.json"
      trigger="loop"
      delay="500"
      stroke="bold"
      colors="primary:#121331,secondary:#ffa500"
      state="hover-look-around"
      style="width: 50px; height: 50px"
    >
    </lord-icon>
                        `;
    button_eye.onclick = login_pass_hide;
    document.getElementById("log_password").type = "text";
}
function login_pass_hide() {
    event.preventDefault();
    let button_eye = document.getElementById("log_see_pass_btn");
    button_eye.innerHTML = `      <lord-icon src="https://cdn.lordicon.com/vfczflna.json" trigger="in" delay="500"
                                stroke="bold" state="morph-cross" style="width: 50px; height: 50px" colors="primary:#121331,secondary:#ffa500">
                            </lord-icon>
                       `;
    button_eye.onclick = login_pass_show;
    document.getElementById("log_password").type = "password";
}
function errorlog(text) {
    event.preventDefault();
    let errorOut = document.getElementById("errors");
    errorOut.style.display = "block";
    errorOut.innerHTML = `<h4> ${text} </h4>`;
}
function log_errorlog(text) {
    event.preventDefault();
    let errorOut = document.getElementById("log_errors");
    errorOut.style.display = "block";
    errorOut.innerHTML = `<h4> ${text} </h4>`;
}
function signup() {
    let xlogin = document.getElementById("right_x_frame");
    let xsignup = document.getElementById("left_x_frame");
    let mlogin = document.getElementById("right_login_frame");
    let msignup = document.getElementById("left_signup_frame");

    xlogin.style.transition = "transform 1s ease";
    xlogin.style.transform = "translateX(0)";
    xsignup.style.transition = "transform 1s ease";
    xsignup.style.transform = "translateX(100%)";

    mlogin.style.transition = "transform 1s ease";
    mlogin.style.transform = "translateX(-100%)";
    msignup.style.transition = "transform 1s ease";
    msignup.style.transform = "translateX(0)";
}
function login() {
    let xlogin = document.getElementById("right_x_frame");
    let xsignup = document.getElementById("left_x_frame");
    let mlogin = document.getElementById("right_login_frame");
    let msignup = document.getElementById("left_signup_frame");

    xlogin.style.transition = "transform 1s ease";
    xlogin.style.transform = "translateX(-100%)";
    xsignup.style.transition = "transform 1s ease";
    xsignup.style.transform = "translateX(0)";

    mlogin.style.transition = "transform 1s ease";
    mlogin.style.transform = "translateX(0)";
    msignup.style.transition = "transform 1s ease";
    msignup.style.transform = "translateX(100%)";
}
function mobile_signup() {
    let mlogin = document.getElementById("right_frame");
    let msignup = document.getElementById("left_frame");

    mlogin.style.transition = "transform 1s ease";
    mlogin.style.transform = "translateY(0)";
    msignup.style.transition = "transform 1s ease";
    msignup.style.transform = "translateY(0)";
}
function mobile_login() {
    let mlogin = document.getElementById("right_frame");
    let msignup = document.getElementById("left_frame");

    mlogin.style.transition = "transform 1s ease";
    mlogin.style.transform = "translateY(-110%)";
    msignup.style.transition = "transform 1s ease";
    msignup.style.transform = "translateY(-110%)";
}
async function sendRegister() {
    event.preventDefault();
    let r_fname = document.getElementById("r_fname").value;
    let r_lname = document.getElementById("r_lname").value;
    let r_user_name = document.getElementById("r_username").value;
    let r_email = document.getElementById("r_email").value;
    let r_passBoxIN = document.getElementById("passBoxIN").value;
    let passBoxIN2 = document.getElementById("passBoxIN2").value;

    let errorOut = document.getElementById("errors");

    if (r_passBoxIN != passBoxIN2) {
        errorOut.innerHTML = "passwords do not match";
        errorOut.style.display = "block";
        return;
    } else {
        errorOut.style.display = "none";
    }

    let payload = {
        fname: r_fname,
        lname: r_lname,
        username: r_user_name,
        email: r_email,
        password: r_passBoxIN,
    };
    loading(true);
    await fetch("/register_user", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify(payload),
    })
        .then(async (response) => {
            const data = await response.json(); // Parse response body as JSON

            // Check the response status
            if (response.status === 200) {
                document.cookie = "auth_token=" + data.token;
                document.getElementById("out_messages").innerHTML =
                    "User has registered, but requires verification.";
                document.getElementById("out_messages").style.display = "block";
                showVerification();
            } else if (response.status === 403) {
                document.getElementById("out_messages").innerHTML =
                    "Your account with this email already exists, but hasn't been verified yet. We've just sent you a verification code.";
                document.getElementById("out_messages").style.display = "block";
                showVerification();
            } else if (response.status === 409) {
                document.getElementById("errors").innerHTML =
                    "User email already exists.";
                document.getElementById("errors").style.display = "block";
            } else {
                document.getElementById("errors").innerHTML =
                    "Somthing went wrong";
                document.getElementById("errors").style.display = "block";
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            document.getElementById("errors").innerHTML =
                "An error occurred: " + error.message;
            document.getElementById("errors").style.display = "block";
        });
}
function showVerification() {
    let v_out = document.getElementById("verify_div");
    v_out.innerHTML = `<div class="v_wrapper">
            <div class="v_content">
                <form onsubmit="sendVerify()">
                    <h2>Verification Code:</h2>
                    <h5>A verification code has been sent to your registered email address. Please check your inbox and enter the code below to complete the 
process.</h5>
                    <input class="in" type="text" id="r_verify" placeholder="xxxx" required />
                    <input type="submit" id="gsubmit" value="Verify" />
                </form>
            </div>
        </div>`;
    loading(false);
    v_out.style.display = "flex";
}
async function sendVerify() {
    event.preventDefault();
    let code = document.getElementById("r_verify").value;
    let s_in = document.getElementById("gsubmit");
    s_in.disabled = true;
    s_in.value = "⌛ Please wait...";
    let user_token = getAuthToken();
    let user_timezone = getUserTimeZone();
    let payload = {
        code: code,
        timezone: user_timezone,
    };

    await fetch("/verify", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            Authorization: `Bearer ${user_token}`,
        },
        body: JSON.stringify(payload),
    }).then((response) => {
        console.log(response);
        let v_out = document.getElementById("verify_div");
        console.log(response);
        console.log(response.state);
        if (response.status == 201) {
            v_out.innerHTML = `
                <div class="v_wrapper">
                <div class="v_content">
                    <h3>User is verified </h3>
                </div>
            </div>
                `;
            v_out.style.display = "flex";
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else if (response.status == 400) {
            v_out.innerHTML = `
                
                <div class="v_wrapper">
                <div class="v_content">
                    <form onsubmit="sendVerify()">
                        <h2>Verification Code:</h2>
                        <h3>The code dose not match , another code has been sent to you pls try againVerification code doesn't match. A new verification code has been sent to your email. Please try again.</h3>
                   
                        <input class="in" type="text" id="r_verify" placeholder="xxxx" required />
                        <input type="submit" id="gsubmit" value="Verify" />
                    </form>
                </div>
            </div>
                
                
                `;
            v_out.style.display = "flex";
        } else if (response.state == 405) {
            v_out.innerHTML = `
                
                <div class="v_wrapper">
                <div class="v_content">
                    <h3>The user account is already verified.</h3>
                </div>
            </div>
                
                `;
            v_out.style.display = "flex";
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            v_out.innerHTML = `
                
                <div class="v_wrapper">
                <div class="v_content">
                    <h3>Something went wrong </h3>
                </div>
            </div>
                
                `;
            v_out.style.display = "flex";
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    });
}
function loading(state) {
    let out = document.getElementById("verify_div");
    if (state) {
        out.innerHTML = `
                <lord-icon
                    src="https://cdn.lordicon.com/rqptwppx.json"
                    trigger="loop"
                    stroke="bold"
                    state="loop-cycle"
                    colors="primary:#121331,secondary:#ffa500"
                    style="width:100px;height:100px">
                </lord-icon>`;
        out.style.display = "flex";
    } else {
        out.style.display = "none";
    }
}
async function sendLogin() {
    event.preventDefault();
    let u_email = document.getElementById("email").value;
    let u_pass = document.getElementById("log_password").value;
    let user_token = getAuthToken();
    let user_timezone = getUserTimeZone();
    let payload = {
        email: u_email,
        password: u_pass,
        timezone: user_timezone,
    };
    await fetch("/login_user", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify(payload),
    })
        .then(async (response) => {
            let data = await response.json();
            if (response.status == 200) {
                document.cookie = "auth_token=" + data.token;
                window.location.href = "/home";
            } else if (response.status == 401) {
                document.getElementById("log_errors").innerHTML =
                    "Invalid credentials. Please check your email and password.";
                document.getElementById("log_errors").style.display = "block";
            } else {
                document.getElementById("log_errors").innerHTML =
                    "Something went wrong on our end. Please try again or contact support for help.";
                document.getElementById("log_errors").style.display = "block";
            }
        })
        .catch((error) => {
            console.error("Error: ", error);
        });
}
function showForgotPass() {
    let v_out = document.getElementById("verify_div");
    v_out.innerHTML = `<div class="v_wrapper">
            <div class="v_content">
                <form onsubmit="forgotPassSetup()">
                    <h2>Enter your email:</h2>
                    <h5>A verification code will been sent to your email address. </h5>
                    <input class="in" type="email" id="r_email" placeholder="Email" required />
                    <input type="submit" id="gsubmit" value="Submit" />
                </form>
                <div class="verification_error_out" id="verification_error_out" style="display:none;"></div>
            </div>
        </div>`;
    loading(false);
    v_out.style.display = "flex";
}
async function forgotPass(user_email) {
    let payload = {
        email: user_email,
    };
    await fetch("/forgotpass/code", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify(payload),
    })
        .then((response) => {
            let data = response.json();

            if (response.status == 200) {
                showForgotPassVerify();
            } else if (response.status == 404) {
                document.getElementById("gsubmit").disabled = false;
                document.getElementById("verification_error_out").innerHTML =
                    "<p>User with this email does not exist.</p>";
                document.getElementById(
                    "verification_error_out"
                ).style.display = "block";
            } else {
                document.getElementById("gsubmit").disabled = false;
                document.getElementById("verification_error_out").innerHTML =
                    "<p>Something went wrong on our end. Please try again or contact support for help.</p>";
                document.getElementById(
                    "verification_error_out"
                ).style.display = "block";
            }
        })
        .catch((error) => {
            console.error("Error: ", error);
        });
}
function showForgotPassVerify() {
    let v_out = document.getElementById("verify_div");
    loading(false);
    v_out.innerHTML = `<div class="v_wrapper">
            <div class="v_content">
                <form onsubmit="forgotPassSubmit()">
                    <h2>Verification Code:</h2>
                    <h5>A verification code has been sent to your registered email address.<br/> Please check your inbox and enter the code below to complete the process.</h5>
                    <input class="in" type="text" id="r_verify" placeholder="xxxx" required />
                    <div class="passBox">
                        <div class="passIN">
                            <input class="in" id="new_passBoxIN" type="password" name="password" placeholder="Password" required />
                            <input class="in" id="new_passBoxIN2" type="password" name="cpass" placeholder="Confirm Password" required />
                        </div>
                        <div class="btn_show">
                            <button id="see_pass_btn" type="button" onclick="reset_pass_show()">
                                <lord-icon src="https://cdn.lordicon.com/vfczflna.json" trigger="in" delay="1000" stroke="bold" state="morph-cross" colors="primary:#121331,secondary:#ffa500" style="width: 50px; height: 50px">
                                </lord-icon>
                            </button>
                        </div>
                    </div>
                    <input type="submit" id="gsubmit" value="Submit" />
                </form>
                <div class="verification_error_out" id="verification_error_out" style="display:none;"></div>
            </div>
        </div>`;

    v_out.style.display = "flex";
}
async function forgotPassSetup() {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    let user_email = document.getElementById("r_email").value;
    document.getElementById("gsubmit").disabled = true;
    let payload = {
        email: user_email,
    };

    try {
        let response = await fetch("/forgotpass/setup", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify(payload),
        });

        // Await the response.json() to get the actual data
        let data = await response.json();

        console.log(data); // This will now log the actual JSON object
        console.log(response);

        if (response.status === 200) {
            document.cookie = "auth_token=" + data.token + "; path=/";
            forgotPass(user_email); // Call your next function
        } else {
            console.error("Error: ", data.message || "Unknown error occurred.");
        }
    } catch (error) {
        console.error("Fetch Error:", error); // Handle network errors
    }
}

async function forgotPassSubmit() {
    event.preventDefault();
    document.getElementById("gsubmit").disabled = true;
    let v_code = document.getElementById("r_verify").value;
    let pass_1 = document.getElementById("new_passBoxIN").value;
    let pass_2 = document.getElementById("new_passBoxIN2").value;
    let user_token = getAuthToken();
    let v_out = document.getElementById("verify_div");
    let error_out = document.getElementById("verification_error_out");
    let payload = {
        code: v_code,
        pass_1: pass_1,
        pass_2: pass_2,
        token: user_token,
    };
    loading(true);
    await fetch("/forgotpass/submit", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify(payload),
    }).then((response) => {
        let data = response.json();
        if (response.status == 201) {
            v_out.innerHTML = `<div class="v_wrapper">
                <div class="v_content">
                <p>Password updated successfully.</p>
                </div>
                </div>`;
            v_out.style.display = "flex";
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else if (response.status == 403) {
            document.getElementById("gsubmit").disabled = fasle;
            error_out.innerHTML = `<p>Passwords don't match. Try again.</p>`;
            error_out.style.display = "block";
        } else if (response.status == 401) {
            document.getElementById("gsubmit").disabled = false;
            error_out.innerHTML = `<p>Verification failed, a new code has been sent to your email.</p>`;
            error_out.style.display = "block";
        } else {
            document.getElementById("gsubmit").disabled = false;
            error_out.innerHTML = `<p>Something went wrong on our end. Please try again or contact support for help.</p>`;
            error_out.style.display = "block";
        }
    });
}
function reset_pass_show() {
    event.preventDefault();
    let button_eye = document.getElementById("see_pass_btn");
    button_eye.innerHTML = `
                             <lord-icon
      src="https://cdn.lordicon.com/vfczflna.json"
      trigger="loop"
      delay="500"
      colors="primary:#121331,secondary:#ffa500"
      stroke="bold"
      state="hover-look-around"
      style="width: 50px; height: 50px"
    >
    </lord-icon>
                        `;
    button_eye.onclick = reset_pass_hide;
    document.getElementById("new_passBoxIN").type = "text";
    document.getElementById("new_passBoxIN2").type = "text";
}
function reset_pass_hide() {
    event.preventDefault();
    let button_eye = document.getElementById("see_pass_btn");
    button_eye.innerHTML = `      <lord-icon src="https://cdn.lordicon.com/vfczflna.json" trigger="in" delay="500"
                                stroke="bold" colors="primary:#121331,secondary:#ffa500" state="morph-cross" style="width: 50px; height: 50px">
                            </lord-icon>
                       `;
    button_eye.onclick = reset_pass_show;
    document.getElementById("new_passBoxIN").type = "password";
    document.getElementById("new_passBoxIN2").type = "password";
}
