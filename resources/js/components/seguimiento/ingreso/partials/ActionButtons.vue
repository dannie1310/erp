<template>
    <div class="btn-group">
       <button @click="envio"  v-if="value.envioCorreo" type="button" class="btn btn-sm btn-outline-secondary" title="Reenvio de Correo"><i class="fa fa-envelope"></i></button>
    </div>
</template>

<script>
    export default {
        name: "ingreso-action-buttons",
        components: {},
        props: ['value'],
        methods: {
            envio() {
                return this.$store.dispatch('seguimiento/vw-ingreso/envioCorreo', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            },
        }
    }
</script>
