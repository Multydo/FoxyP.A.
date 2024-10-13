function show_settings(
    j_work_from,
    j_work_to,
    j_break_time,
    j_logic,
    j_max_app,
    j_days,
    j_public_id
) {
    let work_from = j_work_from;
    let work_to = j_work_to;
    var break_time = j_break_time;
    let logic = j_logic;
    let max_app = j_max_app;
    let days = j_days;
    console.log(days);
    let output = document.getElementById("box");
    let form = ``;
    form += `<div class="content">
  <div class="public_id">
  <h4>You're public ID: ${j_public_id} .(you can share it to make your followers find you easily)</h4>
  </div>
      
  <form onsubmit="saveSettings()" >
  <div class="form_content">
  <div class="left_form">  
      <h2>Work Hours:</h2>
        <div class="part_1">
        <div class="sub_part_1">
          <label for="work_from"> Start: </label>
          <input
            id="work_from"
            name="work_from"
            type="time"
            placeholder="Starting time"
            value="${work_from}"
            required
          />
          </div>
          <div class="sub_part_2">
          <label for="work_to"> End:</label>
          <input
            id="work_to"
            name="work_to"
            type="time"
            placeholder="Ending time"
            value="${work_to}"
          />
</div>

        </div>
        
        <h2>Woking Days</h2>
        <div class= "w_days">

        
        
`;

    if (days[1] == "accepted") {
        form += `
    <div class="left_w_day">
    <div>
    <label for="mon">Mon:</label>
   <input id="mon" name="mon" type="checkbox" value="accepted" checked>
   </div>`;
    } else {
        form += `
    <div class="left_w_day">
    <div>
    <label for="mon">Mon:</label>
   <input id="mon" name="mon" type="checkbox" value="rejected">
   </div>`;
    }

    if (days[2] == "accepted") {
        form += `<div><label for="tue">Tue:</label>
   <input id="tue" name="tue" type="checkbox" value="accepted" checked>
   </div>`;
    } else {
        form += `<div><label for="tue">Tue:</label>
   <input id="tue" name="tue" type="checkbox" value="rejected">
   </div>`;
    }

    if (days[3] == "accepted") {
        form += `<div><label for="wed">Wed:</label>
   <input id="wed" name="wed" type="checkbox" value="accepted" checked>
   </div>`;
    } else {
        form += `<div><label for="wed">Wed:</label>
   <input id="wed" name="wed" type="checkbox" value="rejected">
   </div>`;
    }

    if (days[4] == "accepted") {
        form += `<div><label for="thu">Thu:</label>
   <input id="thu" name="thu" type="checkbox" value="accepted" checked>
   </div></div>
   
   `;
    } else {
        form += `<div><label for="thu">Thu:</label>
   <input id="thu" name="thu" type="checkbox" value="rejected">
   </div></div>
   
   `;
    }

    if (days[5] == "accepted") {
        form += `
    <div class="right_w_day"><div>
    <label for="fri">Fri:</label>
   <input id="fri" name="fri" type="checkbox" value="accepted" checked>
   </div>`;
    } else {
        form += `
    <div class="right_w_day"><div>
    <label for="fri">Fri:</label>
   <input id="fri" name="fri" type="checkbox" value="rejected">
   </div>`;
    }

    if (days[6] == "accepted") {
        form += `<div><label for="sat">Sat:</label>
   <input id="sat" name="sat" type="checkbox" value="accepted" checked>
   </div>`;
    } else {
        form += `<div><label for="sat">Sat:</label>
   <input id="sat" name="sat" type="checkbox" value="rejected">
   </div>`;
    }

    if (days[0] == "accepted") {
        form += `<div><label for="sun">Sun:</label>
   <input id="sun" name="sun" type="checkbox" value="accepted" checked>
   </div></div>
   `;
    } else {
        form += `<div><label for="sun">Sun:</label>
   <input id="sun" name="sun" type="checkbox" value="rejected">
   </div></div>
   
   `;
    }
    form += `
  </div>
  <h2>System Logic:</h2>`;
    if (logic == "appointment") {
        form += `
    <div class="logic_choose">
    <label for="logic">Preferred Reservation System:</label><br />
        <select id="logic" name="logic" onchange="check_input()">
          <option value="appointment" selected>Appointments</option>
          <!--<option value="ticket">Tickets</option>-->
        </select>
        </div>
        <div id="break_div"  style = "display : flex ;">
        <h2>Break time</h2>
         <div class="break_div">
        <label for="break_time"> Take a break between appointments: </label>
        <input type="text" id="break_time" name="break_time" pattern="[0-2][0-9]:[0-5][0-9]:[0-5][0-9]" placeholder="HH:mm:ss" value="${break_time}" >
</div></div>
        <h2>Requests / Day</h2>
        <div class="max_app_div">
        <label for="max_app">Max number of requests you get per day:</label>
        <input
          id="max_app"
          name="max_app"
          type="number"
          placeholder="Max requests"
          value="${max_app}"
          required
        />
        </div>
        </div>
        <div class="right_form">
        <img src="../img/settings.png" class="set_fox">
        </div></div>
        <div class="div_save_set">
        <button type="submit" class="save_set">Save Changes</button>
      </div>
      
      </form>
    </div>
        
        `;
    } else {
        form += `<div class="logic_choose">
    <label for="logic">Preferred Reservation System:</label><br />
        <select id="logic" name="logic" onchange="check_input()">
          <option value="appointment" selected>Appointments</option>
          <!--<option value="ticket">Tickets</option>-->
        </select>
        </div>
        <div id="break_div" class="break_div" style = "display : none ;">
        <h2>Break time</h2>
        <div class="break_div">
        <label for="break_time"> Take a break between appointments: </label>
        <input type="text" id="break_time" name="break_time" pattern="[0-2][0-9]:[0-5][0-9]:[0-5][0-9]" placeholder="HH:mm:ss" value="${break_time}" >
</div></div>
        <h2>Requests / Day</h2>
        <div class="max_app_div">
        <label for="max_app">Max number of requests you get per day:</label>
        <input
          id="max_app"
          name="max_app"
          type="number"
          placeholder="Max requests"
          value="${max_app}"
          required
        />
        </div>
        </div>
        <div class="right_form">
        <img src="../img/settings.png" class="set_fox">
        </div></div>
        <div class="div_save_set">
        <button type="submit" class="save_set">Save Changes</button>
      </div>
      
        </form>
    </div>
        
        
        `;
    }

    output.innerHTML = form;
}
function check_input() {
    let logic = document.getElementById("logic").value;
    if (logic != "appointment") {
        document.getElementById("break_div").style.display = "none";
    } else {
        document.getElementById("break_div").style.display = "flex";
    }
}
async function getSettings() {
    let user_token = getAuthToken();
    await fetch("/settings/getSettings", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            Authorization: `Bearer ${user_token}`,
        },
    })
        .then(async (response) => {
            let data = await response.json();
            console.log(data);
            if (response.status == 200) {
                let days = [
                    data.data.sunday,
                    data.data.monday,
                    data.data.tuesday,
                    data.data.wednesday,
                    data.data.thursday,
                    data.data.friday,
                    data.data.saturday,
                ];
                show_settings(
                    data.data.work_from,
                    data.data.work_to,
                    data.data.break_time,
                    data.data.logic,
                    data.data.max_app,
                    days,
                    data.data.id
                );
            } else {
                //redirect them to an error page
            }
        })
        .catch((error) => {
            console.error("Error: ", error);
        });
}
async function saveSettings() {
    event.preventDefault();
    let user_token = getAuthToken();
    let work_from = document.getElementById("work_from").value;
    let work_to = document.getElementById("work_to").value;
    let monElement = document.getElementById("mon");
    let tueElement = document.getElementById("tue");
    let wedElement = document.getElementById("wed");
    let thuElement = document.getElementById("thu");
    let friElement = document.getElementById("fri");
    let satElement = document.getElementById("sat");
    let sunElement = document.getElementById("sun");

    let mon = monElement.checked ? "accepted" : "rejected";
    let tue = tueElement.checked ? "accepted" : "rejected";
    let wed = wedElement.checked ? "accepted" : "rejected";
    let thu = thuElement.checked ? "accepted" : "rejected";
    let fri = friElement.checked ? "accepted" : "rejected";
    let sat = satElement.checked ? "accepted" : "rejected";
    let sun = sunElement.checked ? "accepted" : "rejected";

    let logic = document.getElementById("logic").value;
    let break_time = document.getElementById("break_time").value;
    let max_app = document.getElementById("max_app").value;

    let payload = {
        work_from: work_from,
        work_to: work_to,
        break_time: break_time,
        time_zone: getUserTimeZone(),
        logic: logic,
        max_app: max_app,
        monday: mon,
        tuesday: tue,
        wednesday: wed,
        thursday: thu,
        friday: fri,
        saturday: sat,
        sunday: sun,
        max_duration_swicth: false,
        max_duration_time: "00:00:00",
        min_time_switch: false,
        min_time: "00:00:00",
        app_fixed_duration_switch: false,
        app_fixed_duration: "00:00:00",
        allow_dm: false,
    };
    console.log(payload);
    await fetch("/settings/saveSettings", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            Authorization: `Bearer ${user_token}`,
        },
        body: JSON.stringify(payload),
    })
        .then(async (response) => {
            let data = await response.json();
            console.log(data);
            if (response.status == 200) {
                location.reload();
            } else {
                // window.location.href("../");
            }
        })
        .catch((error) => {
            console.error("Error: ", error);
        });
}
