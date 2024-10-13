function show_welcome(fname_x, dates, work_days) {
    let output = document.getElementById("box");
    let fname;

    if (fname_x.length > 0) {
        fname = fname_x.charAt(0).toUpperCase() + fname_x.slice(1);
    } else {
        fname = fname_x;
    }

    form = `<div class="content">
      <div class="top">
        <h2>Hello, ${fname}</h2>

        <h4>
          Here you will find all the days you have appointments set by you or
          others.
        </h4>
      </div>
      <div class="table_div" id="table_div">
      
       
      `;

    if (dates.length > 0) {
        form += `<table>
      <thead>
        <tr>
          <th>Date Range</th>
          
          <th>Details</th>
        </tr>
      </thead>
      <tbody id="taskTableBody">`;

        for (let i = 0; i < dates.length; i++) {
            form += `
        <tr>
        <td>${dates[i]}</td>
        <td>
        <form method="post" action="main.php?action=show_details" id="details_form">
      <input type="hidden" id="r_date" name="r_date" value="${dates[i]}" required />
      <button type="submit">check</button>
    </form>
        
        </td>
        </tr>
        `;
        }
        form += `</tbody>
    </table>
      `;
    } else {
        form += `<h2 class="nothin_found">No appointments were found</h2></div>
    </div>`;
    }
    output.innerHTML = form;
}
function show_time_details(time_data) {
    let output = document.getElementById("box");
    form = `  <div class="content">
      <div class="top">
       

        <h4>these are all of your appointment for the day.</h4>
      </div>
      <div class="table_div" id="table_div">
        <table>
          <thead>
            <tr>
              <td>Time from-to</td>
              <td>Appointment Setter</td>
              <td>Appointment Attendee</td>
              <td>Details</td>
            </tr>
          </thead>
          <tbody id="h_tbody"></tbody>
        </table>
      </div>
    </div>`;
    output.innerHTML = form;
    h_table_content(time_data);
}

function h_table_content(time_data) {
    document.getElementById("right_part").innerHTML = `
<div class="friends_a_div">
                        <a class="friends_a" href="people.php">
                            <lord-icon src="https://cdn.lordicon.com/kndkiwmf.json" state="hover-roll"
                                colors="primary:#121331,secondary:#ffa500" trigger="loop" delay="500"
                                style="width:60px;height:60px">
                            </lord-icon>
                            People
                        </a>

                    </div>
                    <div class="settings_a_div">
                        <a class="settings_a" href="settings.php">

                            <lord-icon src="https://cdn.lordicon.com/uecgmesg.json" trigger="loop" delay="500"
                                state="hover-squeeze" colors="primary:#121331,secondary:#ffa500"
                                style="width:60px;height:60px">
                            </lord-icon>
                            Settings
                        </a>
                    </div>

                    <a href="main.php" class="go_back">
                    <lord-icon src="https://cdn.lordicon.com/gwvmctbb.json" trigger="loop" delay="500"
                        colors="primary:#121331,secondary:#ffa500" style="width:60px;height:60px">
                    </lord-icon>
                    Go Back
                </a>



`;
    let output = document.getElementById("h_tbody");
    let form = ``;

    for (let i = 0; i < time_data.length; i++) {
        form += `
        <tr>
        <td>${time_data[i].start}-${time_data[i].end}</td>
        
        `;

        let fname;
        if (time_data[i].app_id == "app_at") {
            if (time_data[i].fname.length > 0) {
                fname =
                    time_data[i].fname.charAt(0).toUpperCase() +
                    time_data[i].fname.slice(1);
            } else {
                fname = time_data[i].fname;
            }

            form += `
            <td>you</td>
            <td>${fname}:${time_data[i].id}</td>
            <td>
            <form method="post" action="main.php?action=details" id="details_form">
            <input type="hidden" name="aid" id="aid" value="${time_data[i].aid}" required>
            <button type="submit">Check</button>

            </form>
            </td>
            `;
        } else if (time_data[i].app_id == "app_for") {
            if (time_data[i].fname.length > 0) {
                fname =
                    time_data[i].fname.charAt(0).toUpperCase() +
                    time_data[i].fname.slice(1);
            } else {
                fname = time_data[i].fname;
            }
            form += `<td>${fname}:${time_data[i].id}</td>
            <td>you</td>
            
            <td>
            <form method="post" action="main.php?action=details" id="details_form" >
            <input type="hidden" name="aid" id="aid" value="${time_data[i].aid}" required>
            <button type="submit">Check</button>

            </form>
            </td>
            `;
        } else if (time_data[i].app_id == "break_time") {
            form += `<td> Breack</td>
            <td> Breack</td>
            
            <td>
           Breack
            </td>
            `;
        } else {
            return 0;
        }
    }

    output.innerHTML = form;
}

function details_of_app(time_data, raid) {
    document.getElementById("right_part").innerHTML = `
<div class="friends_a_div">
                        <a class="friends_a" href="people.php">
                            <lord-icon src="https://cdn.lordicon.com/kndkiwmf.json" state="hover-roll"
                                colors="primary:#121331,secondary:#ffa500" trigger="loop" delay="500"
                                style="width:60px;height:60px">
                            </lord-icon>
                            People
                        </a>

                    </div>
                    <div class="settings_a_div">
                        <a class="settings_a" href="settings.php">

                            <lord-icon src="https://cdn.lordicon.com/uecgmesg.json" trigger="loop" delay="500"
                                state="hover-squeeze" colors="primary:#121331,secondary:#ffa500"
                                style="width:60px;height:60px">
                            </lord-icon>
                            Settings
                        </a>
                    </div>

                    <a href="main.php" class="go_back">
                    <lord-icon src="https://cdn.lordicon.com/gwvmctbb.json" trigger="loop" delay="500"
                        colors="primary:#121331,secondary:#ffa500" style="width:60px;height:60px">
                    </lord-icon>
                    Go Back
                </a>



`;

    let output = document.getElementById("box");
    form = ``;
    console.log(time_data);
    for (let i = 0; i < time_data.length; i++) {
        if (time_data[i].aid == raid) {
            let fname;
            let lname;
            if (time_data[i].fname.length > 0) {
                fname =
                    time_data[i].fname.charAt(0).toUpperCase() +
                    time_data[i].fname.slice(1);
            } else {
                fname = time_data[i].fname.fname;
            }
            if (time_data[i].lname.length > 0) {
                lname =
                    time_data[i].lname.charAt(0).toUpperCase() +
                    time_data[i].lname.slice(1);
            } else {
                lname = time_data[i].lname;
            }
            form += `
             <div class="content">
      <h4>Appointment at : ${time_data[i].start}-${time_data[i].end}</h4>
      <h4>With: ${fname} ${lname} : ${time_data[i].id}</h4>
      <div class="title_div">
        <h4>Title:</h4>
        <p>${time_data[i].title}</p>
      </div>

      <div class="desc_div">
        <h4>Description:</h4>
        <p>${time_data[i].desc}</p>
      </div>
    </div>
            `;
        }
    }
    output.innerHTML = form;
}

async function getInfo() {
    let user_timezone = getUserTimeZone();
    let user_token = getAuthToken();
    let payload = {
        timezone: user_timezone,
    };
    await fetch("/home/data", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            Authorization: `Bearer ${user_token}`,
        },
        body: JSON.stringify(payload),
    }).then(async (response) => {
        let data = await response.json();
        if (response.status == 401) {
            window.location.href = "../";
        } else if (response.status == 500) {
            window.location.href = "../";
        } else if (response.status == 200) {
            show_welcome(data.data.name, data.data.dates, data.data.work_days);
        }
    });
}
