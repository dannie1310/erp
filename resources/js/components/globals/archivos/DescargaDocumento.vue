<template>
    <span>
        <button @click="descargar" type="button" class="btn btn-sm btn-outline-primary" title="Descargar" v-if="!texto">
            <i class="fa fa-file-download"></i>
        </button>

        <button @click="descargar" type="button" class="btn btn-sm btn-outline-primary" title="Descargar" v-else>
            <i class="fa fa-download"></i>{{texto}}
        </button>
    </span>
</template>

<script>
    export default {
        props: ['id', 'url', 'descripcion','texto'],
        methods: {
            descargar(context, payload){
                var url = this.url.replace("{id}", this.id);
                var win = window.open(url, "_blank");

                win.onbeforeunload = () => {
                    swal("Archivo descargado correctamente.", {
                        icon: "success",
                        timer: 2000,
                        buttons: false
                    })
                }
            },
        }
    }
</script>
