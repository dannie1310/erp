<template>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Logotipo de Obra</h3>
                </div>
                <div class="card-body">
                    <vue-dropzone :options="dropzoneOptions" ref="dropzone" id="dropzone"></vue-dropzone>
                </div>
            </div>
        </div>
</template>

<script>
    export default {
        name: "configuracion-obra",
        data() {
            return {
                dropzoneOptions: {
                    url: '/api/SEGURIDAD_ERP/configuracion-obra',
                    method: 'PATCH',
                    thumbnailWidth: 150
                }
            }
        },

        mounted() {

        },

        methods: {

        }
    }
</script>

<style scoped>

</style>