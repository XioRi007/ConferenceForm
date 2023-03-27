  <template>
    <header class="navbar navbar-expand-lg navbar-light bg-light border border-success">
      <div class="container flex-nowrap">
        <router-link class="navbar-brand" to="/register">Conference</router-link>
        <div class="collapse navbar-collapse d-flex justify-content-end" id="navbarNav">
          <ul class="navbar-nav ml-auto d-flex flex-row col-6 col-sm-4 col-lg-2 justify-content-between">
            <li class="nav-item">
              <router-link class="nav-link" to="/register">Register</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/members">Members</router-link>
            </li>
          </ul>
        </div>
      </div>
    </header>
  </template>
  <script setup>
  import { RouterLink } from 'vue-router'

  </script>
  