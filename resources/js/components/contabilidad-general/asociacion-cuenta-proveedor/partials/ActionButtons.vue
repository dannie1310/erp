<template>
    <div class="btn-group">
        <AsociacionCtaProvModal v-bind:id_empresa="value.id_empresa" v-bind:id_cuenta="value.id_cuenta" v-bind:nombre="value.nombre"></AsociacionCtaProvModal>
        <button @click="eliminar" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar" v-if="value.eliminar && $root.can('eliminar_asociacion_cuentas_contpaq_con_proveedor',1)">
            <i class="fa fa-trash"></i>
        </button>
    </div>
</template>

<script>
    import AsociacionCtaProvModal from './AsociarCuentaProveedor.vue';
    export default {
        name: "action-buttons",
        components: {AsociacionCtaProvModal},
        props: ['value'],
        data(){
            return{
                guardando:false,
            }
        },
        methods:{
            eliminar() {
                this.guardando = true;
                return this.$store.dispatch('contabilidadGeneral/cuenta/eliminarAsociacion', {id_cuenta: this.value.id_cuenta, id_empresa:this.value.id_empresa})
                    .then(data => {
                        this.$store.commit('contabilidadGeneral/cuenta/UPDATE_CUENTA', data);
                        this.guardando = false;
                    })
            },
        }
    }

</script>
