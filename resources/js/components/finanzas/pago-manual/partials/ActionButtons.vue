<template>
    <div class="btn-group ">
        <router-link :to="{ name: 'pago-aplicar', params: {id: this.value.id}}" type="button" v-if="$root.can('registrar_pago_manual')" class="btn btn-sm btn-outline-info" title="Aplicar">
            <i class="fa fa-check"></i>
        </router-link>
    </div>
</template>

<script>
    import FacturaAplicar from "../FacturaAplicarModal";
    export default {
        name: "action-buttons",
        components: { FacturaAplicar },
        props: ['value']
    }
</script>