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

                swal({
                    title: "Validar Prepóliza",
                    text: "¿Esta seguro de que deseas validar la Prepóliza?",
                    icon: "warning",
                    buttons: ["Cancelar", "Si, Validar"]
                })
                    .then((success) => {
                        if (success) {
                            self.$store.dispatch('contabilidad/poliza/validar', self.poliza.id)
                                .then(() => {
                                    swal("Prepóliza Validada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
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