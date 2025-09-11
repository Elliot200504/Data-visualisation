<button class="ui tiny green button" id="addUserBtn">Add User</button>
<table class="ui very compact celled striped table" id="userTable" style="margin-top:10px;">
<thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Last Login</th></tr></thead>
<tbody></tbody>
</table>

<div id="addUserModal" class="ui small modal">
  <div class="header">Add New User</div>
  <div class="content">
    <form class="ui form" id="addUserForm">
      <div class="field"><label>Name</label><input type="text" name="name" required></div>
      <div class="field"><label>Role</label><input type="text" name="role" required></div>
    </form>
  </div>
  <div class="actions">
    <div class="ui approve green button" id="saveUserBtn">Save</div>
    <div class="ui cancel button">Cancel</div>
  </div>
</div>
