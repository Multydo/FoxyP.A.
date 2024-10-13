<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/home.js"></script>
    <script src="../js/functions.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <link rel="stylesheet" href="../style/home.css">
    <link rel="icon" href="../img/fox.png">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Foxy P.A.</title>
</head>

<body>
    <div class="mother">
        <div class="child">
            <div class="header">
                <div class="left_part">
                    <div class="title">
                        <img src="../img/logo.gif" class="logo">
                        <h2><span class="foxy">Foxy</span> Personal Assistant</h2>
                    </div>
                </div>
                <div class="right_part" id="right_part">
                    <div class="friends_a_div">
                        <a class="friends_a" href="people">
                            <lord-icon src="https://cdn.lordicon.com/kndkiwmf.json" state="hover-roll"
                                colors="primary:#121331,secondary:#ffa500" trigger="loop" delay="500"
                                style="width:60px;height:60px">
                            </lord-icon>
                            People
                        </a>

                    </div>
                    <div class="settings_a_div">
                        <a class="settings_a" href="settings">

                            <lord-icon src="https://cdn.lordicon.com/uecgmesg.json" trigger="loop" delay="500"
                                state="hover-squeeze" colors="primary:#121331,secondary:#ffa500"
                                style="width:60px;height:60px">
                            </lord-icon>
                            Settings
                        </a>
                    </div>
                </div>
            </div>
            <div class="box" id="box">

            </div>
        </div>
    </div>
    <script>
    getInfo()
    </script>
</body>

</html>