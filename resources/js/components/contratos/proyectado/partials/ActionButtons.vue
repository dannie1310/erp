<template>
    <div class="btn-group">
        <cambiar-area-subcontratante :id="value.id" :value="value"></cambiar-area-subcontratante>

    </div>
</template>

<script>
    import CambiarAreaSubcontratante from "../CambiarAreaSubcontratante";

    export default {
        name: "action-buttons",
        components: {CambiarAreaSubcontratante},
        props: ['value'],
        methods: {
            cambiar_area() {
                this.$router.push({name: 'cambiar-area-subcontratante', params: {id: this.value.id}});
            }
        }
    }
</script>