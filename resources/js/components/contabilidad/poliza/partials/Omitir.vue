<template>
    <button v-if="$root.can('omitir_prepoliza_generada') && (poliza.estatus == -2 || poliza.estatus == -1 || poliza.estatus == 0)" class="btn btn-app btn-info pull-right" @click="omitir">
        <i class="fa fa-thumbs-o-down"></i> Omitir
    </button>
</template>

<script>
    export default {
        name: "poliza-omitir",
        props: ['poliza'],
        methods: {
            omitir(){
                let self = this
                Swal({
                    title: "Omitir Prepóliza",
                    text: "¿Esta seguro de que deseas omitir la Prepóliza?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si, Omitir",
                    cancelButtonText: "No, Cancelar",
                }).then((result) => {
                    if(result.value) {
                        self.$store.dispatch('contabilidad/poliza/omitir', self.poliza.id)
                            .then(() => {
                                Swal({
                                    type: "success",
                                    title: '¡Correcto!',
                                    text: 'Prepóliza omitida con éxito',
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