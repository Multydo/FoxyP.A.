<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/settings.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="../js/functions.js"></script>
    <link rel="stylesheet" href="../style/settings.css">
    <link rel="icon" href="../img/fox.png">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Foxy P.A.</title>
</head>

<body>
    <div class="mother">
        <div class="child">
            <div class="header">
                <div class="left_part">
                    <lord-icon src="https://cdn.lordicon.com/uecgmesg.json" trigger="loop" delay="500"
                        state="hover-squeeze" colors="primary:#121331,secondary:#ffa500" style="width:60px;height:60px">
                    </lord-icon>
                    <h1>Settings</h1>
                </div>
                <a href="home" class="go_back">
                    <lord-icon src="https://cdn.lordicon.com/gwvmctbb.json" trigger="loop" delay="500"
                        colors="primary:#121331,secondary:#ffa500" style="width:60px;height:60px">
                    </lord-icon>
                    Go Back
                </a>
            </div>
            <div class="box" id="box"></div>
        </div>
    </div>
    <script>
    getSettings()
    </script>
</body>

</html>