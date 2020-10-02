<template>
    <div id="main" class="">
        <header id="header">
            <!-- <h1>
                Laravel Vue
            </h1> -->
            <navigationMenu></navigationMenu>

        </header>
        <div id="content">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
    import navigationMenu from './components/Menu.vue'

  export default {
    data() {
      return {
        //
      }
    },
    components: {
        navigationMenu
    }
  }
</script>