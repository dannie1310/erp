<template>
    <vue-dropzone :options="dropzoneOptions" ref="dropzone"></vue-dropzone>
</template>

<script>
    export default {
        name: "configuracion-obra",
        data() {
            return {
                dropzoneOptions: {
                    url: '/api/SEGURIDAD_ERP/cnfiguracion-obra',
                    thumbnailWidth: 150,
                    maxFilesize: 0.5,
                    headers: { "My-Awesome-Header": "header value" }
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