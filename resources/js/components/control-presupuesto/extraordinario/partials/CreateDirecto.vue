<template>
    <span>
        <br>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-sm">
                    <tr >
                        <th class="encabezado">
                            Descripción
                        </th>
                        <th class="encabezado cantidad_input">
                            Unidad
                        </th>
                        <th class="encabezado cantidad_input">
                            Cantidad
                        </th>
                        <th class="encabezado cantidad_input">
                            Precio Unitario
                        </th>
                        <th class="encabezado cantidad_input">
                            Monto Presupuestado
                        </th>
                    </tr>
                    <tr>
                        <td style="text-align: center">
                            <input type="text"
                                   class="form-control"
                                   name="descipcion"
                                   data-vv-as="Descripcion Concepto"
                                   v-model="descripcion_concepto"
                                   v-validate="{required: true}"
                                   :class="{'is-invalid': errors.has('descripcion')}"
                                   id="descripcion">
                            <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                        </td>
                        <td style="text-align: center">

                        </td>
                        <td style="text-align: center">
                        </td>
                        <td style="text-align: right">
                        </td>
                        <td style="text-align: right">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "extraordinario-directo-create",
    components: {ModelListSelect},
    props: [],
    data() {
        return {
            cargando: false,


            suma_importe : 0,
            suma_importe_cambio : 0,
        }
    },
    methods: {

        agregar(){
        },

        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.store()
                }
            });
        },

        calcular() {


        },
        eliminarPartida(index){

        },
    },
    computed: {

    },
    mounted() {

    },
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

td, th{
    border: 1px #dee2e6 solid;
}

</style>

