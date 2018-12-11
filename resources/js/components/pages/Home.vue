<template>
    <div class="container">
        <h1>Hola {{ currentUser.nombre }} . . . welcome to your work space.</h1>
    </div>
</template>

<script>
    export default {
        name: "home",
        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            }
        }
    }
</script>