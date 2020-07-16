<template>
    <div class="btn-group">
    </div>
</template>

<script>
   
    export default {
        name: "action-buttons",
        props: ['value'],
        methods: {
            cambiar_area() {
                this.$router.push({name: 'cambiar-area-subcontratante', params: {id: this.value.id}});
            }
        }
    }
</script>