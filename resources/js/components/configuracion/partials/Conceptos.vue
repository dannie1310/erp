<template>
    <div class="card" id="configuracion-conceptos" v-if="true">
        <div class="card-header">
            <h3 class="card-title">Configuración Conceptos</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" v-if="form">
        <div class="row" role="form">
            <div class="form-group col-md-6">
                <label for="display_name">Nombre</label>
                <input
                    type="text"
                    name="display_name"
                    data-vv-as="Nombre"
                    v-validate="{required: true, max: 255}"
                    class="form-control"
                    id="display_name"

                    :class="{'is-invalid': errors.has('display_name')}"
                >
                <div class="invalid-feedback" v-show="errors.has('display_name')">{{ errors.first('display_name') }}</div>
            </div>

            <div class="form-group col-md-6">
                <label for="description">Descripción</label>
                <input
                    type="text"
                    name="description"
                    data-vv-as="Descripción"
                    v-validate="{required: true, max: 255}"
                    class="form-control"
                    id="description"

                    :class="{'is-invalid': errors.has('description')}"
                >
                <div class="invalid-feedback" v-show="errors.has('description')">{{ errors.first('description') }}</div>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "configuracion-conceptos",
        props: ['datosConcepto'],
        data() {
            return {
                form: null
            }
        },
        mounted() {
            this.form = JSON.parse(JSON.stringify(this.datosConcepto))
        },
    }
</script>

<style scoped>

</style>
