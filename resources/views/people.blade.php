<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="icon" href="../img/fox.png">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="../js/people.js"></script>
    <script src="../js/functions.js"></script>
    <link rel="stylesheet" href="../style/people.css">
    <title>Foxy P.A.</title>
</head>

<body>
    <div class="mother">
        <div class="child">
            <div class="header">
                <div class="left_part">
                    <lord-icon src="https://cdn.lordicon.com/kndkiwmf.json" state="hover-roll"
                        colors="primary:#121331,secondary:#ffa500" trigger="loop" delay="500"
                        style="width:60px;height:60px">
                    </lord-icon>
                    <h1>People</h1>
                </div>
                <a href="home" class="go_back">
                    <lord-icon src="https://cdn.lordicon.com/gwvmctbb.json" trigger="loop" delay="500"
                        colors="primary:#121331,secondary:#ffa500" style="width:60px;height:60px">
                    </lord-icon>
                    Go Back
                </a>
            </div>
            <div class="search_people">
                <form onsubmit="search()">
                    <div class="form_content">
                        <div class="search_in_part">
                            <input type="text" name="search_in" id="search_in"
                                placeholder="Search by Email / User_name / Public ID">
                        </div>
                        <div class="search_btn_div">
                            <button type="submit" class="search_btn">
                                <lord-icon src="https://cdn.lordicon.com/unukghxb.json" trigger="hover"
                                    colors="primary:#121331,secondary:#ffa500" style="width:50px;height:50px">
                                </lord-icon>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="search_box" id="search_box"></div>
            <div class="following_box" id="following_box"></div>
        </div>
    </div>
</body>

</html>