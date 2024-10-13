function show_search(search_data) {
    if (search_data[0] != 0) {
        let fname;
        let lname;
        let output = document.getElementById("search_box");
        //console.log(search_data);
        let form = ` <div class="content">
    
    `;
        for (let i = 0; i < search_data.length; i++) {
            if (search_data[i].f_status != "following") {
                if (search_data[i].fname.length > 0) {
                    fname =
                        search_data[i].fname.charAt(0).toUpperCase() +
                        search_data[i].fname.slice(1);
                } else {
                    fname = search_data[i].fname;
                }
                if (search_data[i].lname.length > 0) {
                    lname =
                        search_data[i].lname.charAt(0).toUpperCase() +
                        search_data[i].lname.slice(1);
                } else {
                    lname = search_data[i].lname;
                }

                form += `  <div class="person">
        <div class="name">
          <h2>${fname + " " + lname}</h2>
        </div>
        <div class="more_info">
          
          <div class="followers_box">
            <h4>Followers : ${search_data[i].followers_num}</h4>
          </div>
        </div>
      </div>
      <div class="be_follow_div">
        <form method="post" action="people.php?action=follow">
          <input type="hidden" name="user_p_id" value="${search_data[i].id}" />
          <button type="submit" id="follow_user">
            <lord-icon
              src="https://cdn.lordicon.com/zrkkrrpl.json"
              trigger="hover"
              colors="primary:#121331,secondary:#ffa500"
              style="width: 50px; height: 50px"
            >
            </lord-icon><p>Follow</p></button>
    
          
        </form>
      </div>`;

                form += `</div>`;
                output.innerHTML = form;
            } else {
                let fname;
                let lname;
                let output = document.getElementById("search_box");

                let form = ` <div class="content">
    
    `;

                if (search_data[i].fname.length > 0) {
                    fname =
                        search_data[i].fname.charAt(0).toUpperCase() +
                        search_data[i].fname.slice(1);
                } else {
                    fname = search_data[i].fname;
                }
                if (search_data[i].lname.length > 0) {
                    lname =
                        search_data[i].lname.charAt(0).toUpperCase() +
                        search_data[i].lname.slice(1);
                } else {
                    lname = search_data[i].lname;
                }

                form += `  <div class="person">
        <div class="name">
          <h2>${fname + " " + lname}</h2>
        </div>
        <div class="more_info">
          
          <div class="followers_box">
            
         
          
          </div>
        </div>
      </div>
      <div class="be_follow_div_2">

  <form method="post" action="request.php?action=setrequest">
          <input type="hidden" name="user_f_id" value="${search_data[i].id}" />
          <button type="submit" id="request">
            <lord-icon
              src="https://cdn.lordicon.com/rahouxil.json"
              trigger="morph"
              state="morph-plus"
              colors="primary:#121331,secondary:#ffa500"
              style="width: 50px; height: 50px"
            >
            </lord-icon>
            Add request
          </button>
        </form>

        <form method="post" action="people.php?action=unfollow">
          <input type="hidden" name="user_p_id" value="${search_data[i].id}" />
          <button type="submit" id="follow_user">
            <lord-icon
    src="https://cdn.lordicon.com/dykoqszm.json"
    trigger="hover"
    colors="primary:#121331,secondary:#ffa500"
    style="width:50px;height:50px">
</lord-icon><p>Unfollow</p></button>
    
          
        </form>
      </div>`;

                form += `</div>`;

                output.innerHTML = form;
            }
        }
    } else {
        let form = `<div class="no_result"><p>No result was found!</p></div>`;
        let output = document.getElementById("search_box");
        output.innerHTML = form;
    }
}

function show_following(follow_data) {
    if (follow_data[0] != 0) {
        let fname;
        let lname;
        let output = document.getElementById("following_box");
        console.log(follow_data);
        let form = ` <div class="content">
    
    `;
        for (let i = 0; i < follow_data.length; i++) {
            if (follow_data[i].fname.length > 0) {
                fname =
                    follow_data[i].fname.charAt(0).toUpperCase() +
                    follow_data[i].fname.slice(1);
            } else {
                fname = follow_data[i].fname;
            }
            if (follow_data[i].lname.length > 0) {
                lname =
                    follow_data[i].lname.charAt(0).toUpperCase() +
                    follow_data[i].lname.slice(1);
            } else {
                lname = follow_data[i].lname;
            }

            form += `  <div class="person">
        <div class="name">
          <h2>${fname + " " + lname}</h2>
        </div>
        <div class="more_info">
          
          <div class="followers_box">
            
          </div>
        </div>
      </div>
      <div class="be_follow_div_2">

  <form method="post" action="request.php?action=setrequest">
          <input type="hidden" name="user_f_id" value="${follow_data[i].id}" />
          <button type="submit" id="request">
            <lord-icon
              src="https://cdn.lordicon.com/rahouxil.json"
              trigger="morph"
              state="morph-plus"
              colors="primary:#121331,secondary:#ffa500"
              style="width: 50px; height: 50px"
            >
            </lord-icon>
            Add request
          </button>
        </form>

        <form method="post" action="people.php?action=unfollow">
          <input type="hidden" name="user_p_id" value="${follow_data[i].id}" />
          <button type="submit" id="follow_user">
            <lord-icon
    src="https://cdn.lordicon.com/dykoqszm.json"
    trigger="hover"
    colors="primary:#121331,secondary:#ffa500"
    style="width:50px;height:50px">
</lord-icon><p>Unfollow</p></button>
    
          
        </form>
      </div>`;
        }
        form += `</div>`;

        output.innerHTML = form;
    } else {
        let form = `<div class="no_result"><p>You are not following anyone </p></div>`;
        let output = document.getElementById("following_box");
        output.innerHTML = form;
    }
}
async function search() {
    event.preventDefault();
    let search = document.getElementById("search_in").value;
    let user_token = getAuthToken();
    let payload = {
        search: search,
    };
    await fetch("/people/searchPeople", {
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
            if (response.status == 200) {
                console.log(data[0]);
                show_search(data[0]);
            } else if (response.status == 401) {
                console.log(data[0]);
                show_search(data[0]);
            } else {
                //reffer to error page
            }
        })
        .catch((error) => {
            console.error("Error: ", error);
        });
}
