let data, users, details;
let pieChartInstance = null;
let barChartInstance = null;
let userLogsChartInstance = null;
let categoryChartInstance = null;

function fetchData() {
  $.get(
    "data.php?action=fetch_data",
    function (res) {
      data = res.data;
      users = res.users;
      details = res.details;
      renderCharts();
      renderCategoryTable();
      renderUserTable();
      renderLogs();
    },
    "json"
  );
}

// Dashboard charts — only render these once
function renderCharts() {
  const labels = Object.keys(data);
  const values = Object.values(data);
  const colors = ["#2185d0", "#21ba45", "#db2828", "#f39c12"];

  // Pie chart
  const pieCtx = document.getElementById("pieChart").getContext("2d");
  if (pieChartInstance) pieChartInstance.destroy();
  pieChartInstance = new Chart(pieCtx, {
    type: "pie",
    data: { labels, datasets:[{ data: values, backgroundColor: colors }] },
    options: { responsive:true, maintainAspectRatio:false }
  });

  // Bar chart
  const barCtx = document.getElementById("barChart").getContext("2d");
  if (barChartInstance) barChartInstance.destroy();
  barChartInstance = new Chart(barCtx, {
    type: "bar",
    data: { labels, datasets:[{ data: values, backgroundColor: colors }] },
    options: {
    plugins: {
      legend: {
        labels: {
          filter: (legendItem, data) => (typeof legendItem.text !== 'undefined')
        }
      }
    }
  }
  });

  // User Logs chart
  const userLabels = users.map(u => u.name);
  const userData = users.map(u => u.logs.length);
  const userColors = ["#f39c12", "#2185d0", "#21ba45", "#db2828"];

  const userCtx = document.getElementById("userLogsChart").getContext("2d");
  if (userLogsChartInstance) userLogsChartInstance.destroy();
  userLogsChartInstance = new Chart(userCtx, {
  type: "bar",
  data: {
    labels: userLabels,
    datasets: [{
      label: "Number of Logs",
      data: userData,
      backgroundColor: userColors,
      borderColor: "#ffffff",
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: { y: { beginAtZero: true } }
  }
});
}

// Render category table
function renderCategoryTable() {
  let tbody = $("#categoryTable tbody");
  tbody.empty();
  for (let cat in data) {
    tbody.append(
      `<tr><td><span class="clickable category" data-category="${cat}">${cat}</span></td><td>${data[cat]}</td></tr>`
    );
  }
}

// Render user table
function renderUserTable() {
  let tbody = $("#userTable tbody");
  tbody.empty();
  users.forEach((u) => {
    tbody.append(
      `<tr><td>${u.id}</td><td><span class="clickable user" data-id="${u.id}">${u.name}</span></td><td>${u.role}</td><td>${u.last_login}</td></tr>`
    );
  });
}

// Render logs
function renderLogs() {
  let container = $("#logsContainer");
  container.empty();
  users.forEach((u) => {
    let segment = $('<div class="ui segment"></div>');
    segment.append(
      `<h4 class="ui dividing header">${u.name} <span style="font-size:0.8em;color:gray">(${u.role})</span></h4>`
    );
    let list = $('<div class="ui relaxed divided list"></div>');
    u.logs.forEach((log) =>
      list.append(
        `<div class="item"><i class="history icon"></i><div class="content">${log}</div></div>`
      )
    );
    segment.append(list);
    container.append(segment);
  });
}

$(function () {
  fetchData();

  // Tabs
  $(".menu .item").tab({
    onVisible: function (tab) {
      $(".nav-section").hide();
      $('.nav-section[data-tab="' + tab + '"]').show();
    },
  });
  $(".nav-section").hide();
  $(".nav-section.active").show();

  // Category modal
  $(document).on("click", ".category", function () {
    const cat = $(this).data("category");
    $("#categoryTitle").text(cat + " — Details");

    if (categoryChartInstance) categoryChartInstance.destroy();
    const ctx = document.getElementById("categoryChart").getContext("2d");

    let chartData;
    if (cat === "User Logs") {
      chartData = {
        labels: users.map((u) => u.name),
        data: users.map((u) => u.logs.length),
      };
    } else {
      chartData = {
        labels: details[cat]?.labels || [],
        data: details[cat]?.values || [],
      };
    }

    const bg = chartData.data.map((_, i) =>
      cat === "User Logs"
        ? "#f39c12"
        : ["#2185d0", "#21ba45", "#db2828", "#f39c12"][i % 4]
    );

    categoryChartInstance = new Chart(ctx, {
      type: cat === "User Logs" ? "bar" : "line",
      data: {
        labels: chartData.labels,
        datasets: [
          {
            label: cat,
            data: chartData.data,
            borderColor: "#2185d0",
            backgroundColor: bg,
            fill: true,
          },
        ],
      },
      options: { responsive: true, scales: { y: { beginAtZero: true } } },
    });

    $("#categoryModal").modal("show");
  });

  // Add User
  $("#addUserBtn").click(() => $("#addUserModal").modal("show"));
  $("#saveUserBtn").click(() => {
    $.post(
      "data.php",
      {
        action: "add_user",
        name: $("#addUserForm [name=name]").val(),
        role: $("#addUserForm [name=role]").val(),
      },
      function () {
        fetchData();
        $("#addUserModal").modal("hide");
      }
    );
  });

  // --- LOGIN / REGISTER ---
  $("#loginBtn").click(() => $("#loginModal").modal("show"));
  $("#registerBtn").click(() => $("#registerModal").modal("show"));

  // Login
  $("#loginSubmit").click(() => {
    $.post(
      "auth.php",
      {
        action: "login",
        username: $("#loginForm [name=username]").val(),
        password: $("#loginForm [name=password]").val(),
      },
      function (res) {
        if (res.success) location.reload();
        else alert(res.msg);
      },
      "json"
    );
  });

  // Register
  $("#registerSubmit").click(() => {
    $.post(
      "auth.php",
      {
        action: "register",
        username: $("#registerForm [name=username]").val(),
        password: $("#registerForm [name=password]").val(),
      },
      function (res) {
        if (res.success) {
          $("#registerModal").modal("hide");
          alert("Registration successful!");
        } else alert(res.msg);
      },
      "json"
    );
  });

  // Logout
  $("#logoutBtn").click(() => {
    $.post(
      "auth.php",
      { action: "logout" },
      function () {
        location.reload();
      },
      "json"
    );
  });

  // Switch from Login to Register
  $("#toRegister").click(() => {
    $("#loginModal").modal("hide");
    $("#registerModal").modal("show");
  });

  // Switch from Register to Login
  $("#toLogin").click(() => {
    $("#registerModal").modal("hide");
    $("#loginModal").modal("show");
  });
});
