<template>
    <nav id="nav" class="mb-5">

        <b-navbar toggleable="lg" type="dark" variant="primary">
        <router-link to="/" class="navbar-brand" >Home</router-link>

          <!-- Right aligned nav items -->
          <b-navbar-nav class="ml-auto">

            <li v-if="!$auth.check()" v-for="(route, key) in routes.unlogged" v-bind:key="route.path">

                <b-button :to="{ name : route.path }" :key="key" variant="light" class="ml-2  mb-2" >
                  <b-icon :icon="route.icon" aria-hidden="true"></b-icon> {{route.name}}
                </b-button>

            </li>

            <li right v-if="$auth.check()" v-for="(route, key) in routes.admin" v-bind:key="route.path">
                <b-button :to="{ name : route.path }" :key="key" variant="light" class="ml-2  mb-2" >
                  <b-icon :icon="route.icon" aria-hidden="true"></b-icon> {{route.name}}
                </b-button>

            </li>

            <b-nav-item-dropdown right v-if="$auth.check()" >
              <template v-slot:button-content >
                <b-icon icon="person" aria-hidden="true"></b-icon> {{ $auth.user().name }}
              </template>
              <b-dropdown-item href="#" @click.prevent="$auth.logout()">
                  <b-icon icon="power" aria-hidden="true"></b-icon> Sign Out</b-dropdown-item>
            </b-nav-item-dropdown> 

          </b-navbar-nav>
      </b-navbar>

    </nav>
</template>
<script>
  export default {
    data() {
      return {
        routes: {
          // UNLOGGED
          unlogged: [
            {
              name: 'Registration',
              path: 'register',
              icon: 'person-plus'
            },
            {
              name: 'Login',
              path: 'login',
              icon: 'box-arrow-in-right'
            }
          ],
          // LOGGED USER
          user: [
            {
              name: 'Dashboard',
              path: 'dashboard'
            }
          ],
          // LOGGED ADMIN
          admin: [
            {
              name: 'Dashboard',
              path: 'admin.dashboard',
              icon: 'arrow-right-circle'
            }
          ]
        }
      }
    },
    mounted() {
      //
    }
  }
</script>