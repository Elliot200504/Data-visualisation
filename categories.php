<h3 class="ui header">📊 Summary Charts</h3>
<div class="ui two column stackable grid">
  <div class="column">
    <div class="ui fluid card">
      <div class="content"><div class="header">Doughnut</div></div>
      <div class="content"><canvas id="pieChart"></canvas></div>
    </div>
  </div>
  <div class="column">
    <div class="ui fluid card">
      <div class="content"><div class="header">Bar</div></div>
      <div class="content"><canvas id="barChart"></canvas></div>
    </div>
  </div>
</div>

<h3 class="ui header" style="margin-top:20px;">📂 Categories</h3>
<table class="ui very compact celled striped table" id="categoryTable">
<thead><tr><th>Category</th><th>Total</th></tr></thead>
<tbody></tbody>
</table>

<div id="categoryModal" class="ui small modal">
  <div class="header" id="categoryTitle"></div>
  <div class="content"><canvas id="categoryChart"></canvas></div>
  <div class="actions"><div class="ui approve button">Close</div></div>
</div>
