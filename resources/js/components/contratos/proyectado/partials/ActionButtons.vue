<template>
    <div class="btn-group">
        <cambiar-area-subcontratante></cambiar-area-subcontratante>

    </div>
</template>

<script>
    import CambiarAreaSubcontratante from "../CambiarAreaSubcontratante";

    export default {
        name: "action-buttons",
        components: {CambiarAreaSubcontratante},
        props: ['value'],
        methods: {
            ajustar_saldo() {
                this.$router.push({name: 'fondo-garantia-ajustar-saldo', params: {id: this.value.id}});
            }
        }
    }
</script>