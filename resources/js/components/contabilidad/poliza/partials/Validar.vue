<template>
    <button v-if="$root.can('validar_prepoliza') && (poliza.estatus == -2 || poliza.estatus == 0)" class="btn btn-app btn-info pull-right" @click="validar">
        <i class="fa fa-check-square-o"></i> Validar
    </button>
</template>

<script>
    export default {
        name: "poliza-validar",
        props: ['poliza'],
        methods: {
            validar(){
                let self = this
                Swal({
                    title: "Validar Prepóliza",
                    text: "¿Esta seguro de que deseas validar la Prepóliza?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si, Validar",
                    cancelButtonText: "No, Cancelar",
                }).then((result) => {
                    if(result.value) {
                        self.$store.dispatch('contabilidad/poliza/validar', self.poliza.id)
                            .then(() => {
                                Swal({
                                    type: "success",
                                    title: '¡Correcto!',
                                    text: 'Prepóliza validada con éxito',
                                    confirmButtonText: "Ok",
                                    closeOnConfirm: true
                                }).then(() => {
                                    self.$emit('success')
                                })
                            })
                    }
                });
            },
        }
    }
</script>