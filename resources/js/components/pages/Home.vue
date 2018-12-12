<template>
    <div class="container">
        <p>Home</p>
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