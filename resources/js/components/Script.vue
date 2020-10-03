<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">Shell script to generate n num of records</div>
            <div class="card-body">
                <code>
                    factory(App\User::class, 10)->create()
                </code>
            </div>
        </div>
    </div>
</template>
<script>
  export default {
    data() {
      return {
        //
      }
    },
    components: {
      //
    }
  }
</script>