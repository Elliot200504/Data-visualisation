<!-- Login Modal -->
<div id="loginModal" class="ui small modal">
  <div class="header">Login</div>
  <div class="content">
    <form class="ui form" id="loginForm">
      <div class="field"><label>Username</label><input type="text" name="username" required></div>
      <div class="field"><label>Password</label><input type="password" name="password" required></div>
      <span class="form-link ui button circular black" id="toRegister">Don't have an account? Register</span>
    </form>
  </div>
  <div class="actions">
    <div class="ui approve green button" id="loginSubmit">Login</div>
  </div>
</div>

<!-- Register Modal -->
<div id="registerModal" class="ui small modal">
  <div class="header">Register</div>
  <div class="content">
    <form class="ui form" id="registerForm">
      <div class="field"><label>Username</label><input type="text" name="username" required></div>
      <div class="field"><label>Password</label><input type="password" name="password" required></div>
      <span class="form-link ui button circular black" id="toLogin">Already have an account? Login</span>
    </form>
  </div>
  <div class="actions">
    <div class="ui approve green button" id="registerSubmit">Register</div>
    <div class="ui cancel button">Cancel</div>
  </div>
</div>
