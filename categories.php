<h3 class="ui header">📊 Summary Charts</h3>
<div class="ui stackable grid">
  <!-- Left column: Pie chart and User Logs label -->
  <div class="eight wide column">
    <div class="ui fluid card" style="margin-bottom: 20px;">
      <div class="content"><div class="header">Summary</div></div>
      <div class="content"><canvas id="pieChart"></canvas></div>
    </div>
    <div class="ui fluid card">
      <div class="content"><div class="header">User Logs</div></div>
      <div class="content"><canvas id="userLogsChart"></canvas></div>
    </div>
  </div>

  <!-- Right column: Bar chart -->
  <div class="eight wide column">
    <div class="ui fluid card" style="height: 100%;">
      <div class="content"><div class="header">Bar Chart</div></div>
      <div class="content"><canvas id="barChart"></canvas></div>
    </div>
  </div>
</div>

<h3 class="ui header" style="margin-top:20px;">📂 Categories</h3>
<table class="ui very compact celled striped table" id="categoryTable">
  <thead><tr><th>Category</th><th>Total</th></tr></thead>
  <tbody></tbody>
</table>
