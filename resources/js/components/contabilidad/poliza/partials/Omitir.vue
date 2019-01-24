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

                swal({
                    title: "Omitir Prepóliza",
                    text: "¿Esta seguro de que deseas omitir la Prepóliza?",
                    icon: "warning",
                    buttons: ["Cancelar", "Si, Omitir"]
                })
                    .then((success) => {
                        if (success) {
                            self.$store.dispatch('contabilidad/poliza/omitir', self.poliza.id)
                                .then(() => {
                                    swal("Prepóliza omitida correctamente", {
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