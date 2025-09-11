let data, users, details;
let pieChartInstance = null;
let barChartInstance = null;
let userLogsChartInstance = null;
let categoryChartInstance = null;

// Fetch data from PHP
function fetchData() {
  $.get("data.php?action=fetch_data", function(res){
    data = res.data;
    users = res.users;
    details = res.details;

    renderPieAndBarCharts();
    renderUserLogsChart();
    renderCategoryTable();
    renderUserTable();
    renderLogs();
  }, "json");
}

// === PIE + MAIN BAR CHART ===
function renderPieAndBarCharts() {
  const labels = Object.keys(data);
  const values = Object.values(data);
  const colors = ["#2185d0", "#21ba45", "#db2828", "#f39c12"];

  // Pie chart
  const pieCtx = document.getElementById("pieChart").getContext("2d");
  if (pieChartInstance) pieChartInstance.destroy();
  pieChartInstance = new Chart(pieCtx, {
    type: "pie",
    data: { labels, datasets: [{ data: values, backgroundColor: colors }] },
    options: { responsive:true, maintainAspectRatio:false }
  });

  // Main bar chart
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
    },
    responsive:true,
    scales: { y:{ beginAtZero:true } }
    }
  });
}

// === USER LOGS CHART ===
function renderUserLogsChart() {
  const labels = users.map(u => u.name);
  const values = users.map(u => u.logs.length);
  const colors = ["#f39c12", "#2185d0", "#21ba45", "#db2828"];

  const ctx = document.getElementById("userLogsChart").getContext("2d");

  if (!userLogsChartInstance) {
    userLogsChartInstance = new Chart(ctx, {
      type: "bar",
      data: {
        labels,
        datasets: [{ label:"Number of Logs", data: values, backgroundColor: colors, borderColor:"#fff", borderWidth:1 }]
      },
      options: { responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } } }
    });
  } else {
    userLogsChartInstance.data.labels = labels;
    userLogsChartInstance.data.datasets[0].data = values;
    userLogsChartInstance.update();
  }
}

// === CATEGORY TABLE ===
function renderCategoryTable() {
  const tbody = $("#categoryTable tbody");
  tbody.empty();
  for (let cat in data) {
    tbody.append(
      `<tr><td><span class="clickable category" data-category="${cat}">${cat}</span></td><td>${data[cat]}</td></tr>`
    );
  }
}

// === USER TABLE ===
function renderUserTable() {
  const tbody = $("#userTable tbody");
  tbody.empty();
  users.forEach(u => {
    tbody.append(`<tr><td>${u.id}</td><td><span class="clickable user" data-id="${u.id}">${u.name}</span></td><td>${u.role}</td><td>${u.last_login}</td></tr>`);
  });
}

// === USER LOGS RENDERING ===
function renderLogs() {
  const container = $("#logsContainer");
  container.empty();
  users.forEach(u => {
    const segment = $('<div class="ui segment"></div>');
    segment.append(`<h4 class="ui dividing header">${u.name} <span style="font-size:0.8em;color:gray">(${u.role})</span></h4>`);
    const list = $('<div class="ui relaxed divided list"></div>');
    u.logs.forEach(log => list.append(`<div class="item"><i class="history icon"></i><div class="content">${log}</div></div>`));
    segment.append(list);
    container.append(segment);
  });
}

// === CATEGORY MODAL (for daily viewer) ===
$(document).on("click", "#barChart", function() {
  const cat = $(this).data("category");
  $("#categoryTitle").text(cat + " — Details");

  if (categoryChartInstance) categoryChartInstance.destroy();
  const ctx = document.getElementById("categoryChart").getContext("2d");

  let chartData;
  if (cat === "User Logs") {
    chartData = { labels: users.map(u => u.name), data: users.map(u => u.logs.length) };
  } else {
    chartData = { labels: details[cat]?.labels || [], data: details[cat]?.values || [] };
  }

  const bg = chartData.data.map((_, i) => cat==="User Logs" ? "#f39c12" : ["#2185d0","#21ba45","#db2828","#f39c12"][i%4]);

  categoryChartInstance = new Chart(ctx, {
    type: cat==="User Logs"?"bar":"line",
    data: { labels: chartData.labels, datasets:[{ label:cat, data:chartData.data, borderColor:"#2185d0", backgroundColor:bg, fill:true }] },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } } }
  });

  $("#categoryModal").modal("show");
});

// === INITIALIZATION ===
$(function(){ 
  fetchData();
  // Tabs
  $(".menu .item").tab({
    onVisible: function(tab) {
      $(".nav-section").hide();
      $('.nav-section[data-tab="'+tab+'"]').show();
    }
  });
  $(".nav-section").hide();
  $(".nav-section.active").show();

  // Add User
  $("#addUserBtn").click(() => $("#addUserModal").modal("show"));
  $("#saveUserBtn").click(() => {
    $.post("data.php", {
      action: "add_user",
      name: $("#addUserForm [name=name]").val(),
      role: $("#addUserForm [name=role]").val()
    }, function() { fetchData(); $("#addUserModal").modal("hide"); });
  });

  // LOGIN / REGISTER
  $("#loginBtn").click(() => $("#loginModal").modal("show"));
  $("#registerBtn").click(() => $("#registerModal").modal("show"));

  $("#loginSubmit").click(() => {
    $.post("auth.php", {
      action: "login",
      username: $("#loginForm [name=username]").val(),
      password: $("#loginForm [name=password]").val()
    }, function(res) {
      if(res.success) location.reload();
      else alert(res.msg);
    }, "json");
  });

  $("#registerSubmit").click(() => {
    $.post("auth.php", {
      action: "register",
      username: $("#registerForm [name=username]").val(),
      password: $("#registerForm [name=password]").val()
    }, function(res) {
      if(res.success) { $("#registerModal").modal("hide"); alert("Registration successful!"); }
      else alert(res.msg);
    }, "json");
  });

  $("#logoutBtn").click(() => {
    $.post("auth.php", { action:"logout" }, () => location.reload(), "json");
  });

  $("#toRegister").click(() => { $("#loginModal").modal("hide"); $("#registerModal").modal("show"); });
  $("#toLogin").click(() => { $("#registerModal").modal("hide"); $("#loginModal").modal("show"); });
});
