<template>
    <span>
        <div class="card" v-if="!contrato">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <DatosContratoProyectado v-bind:contrato_proyectado="contrato" v-if="contrato"></DatosContratoProyectado>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-md-12 table-responsive">
                        <table id="tabla-conceptos" >
                            <thead>
                                <tr>
                                    <th rowspan="2">Clave</th>
                                    <th rowspan="2">Concepto</th>
                                    <th rowspan="2">UM</th>
                                    <th  class="contratado">Volumen</th>
                                    <th class="destino"></th>
                                </tr>
                                <tr>
                                    <th  class="contratado">Contratado</th>
                                    <th class="destino">Destino</th>
                                </tr>
                            </thead>
                            <tbody v-for="(concepto, i) in contrato.contratos.data">
                                <tr v-if="concepto.unidad == null">
                                    <td :title="concepto.clave"><b>{{concepto.clave}}</b></td>
                                    <td :title="concepto.descripcion">
                                        <span v-for="n in concepto.nivel">-</span>
                                        <b>{{concepto.descripcion}}</b>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr v-else-if="concepto.destino">
                                    <td :title="concepto.clave">{{ concepto.clave }}</td>
                                    <td :title="concepto.descripcion">
                                        <span v-for="n in concepto.nivel">-</span>
                                        {{concepto.descripcion}}
                                    </td>
                                    <td >{{concepto.unidad}}</td>
                                    <td class="numerico">{{concepto.cantidad_original_format}}</td>
                                    <td :title="concepto.destino.concepto.path" style="text-decoration: underline">
                                        {{concepto.destino.concepto.path_corta}}
                                    </td>
                                </tr>
                                <tr v-else style="background-color: #ff0000">
                                    <td :title="concepto.clave">{{ concepto.clave }}</td>
                                    <td :title="concepto.descripcion">
                                        <span v-for="n in concepto.nivel">-</span>
                                        {{concepto.descripcion}}
                                    </td>
                                    <td >{{concepto.unidad}}</td>
                                    <td class="numerico">{{concepto.cantidad_original_format}}</td>
                                    <td >DESTINO FALTANTE</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import DatosContratoProyectado from "./partials/DatosContratoProyectado";
    export default {
        name: "contrato-proyectado-show",
        components: {DatosContratoProyectado},
        props: ['id'],
        data(){
            return{
                cargando: false
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {

                this.cargando = true;
                this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', null);
                return this.$store.dispatch('contratos/contrato-proyectado/find', {
                    id: this.id,
                    params:{include: [
                        'contratos.destino'
                    ]}
                }).then(data => {
                    this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', data);
                })
            }
        },
        computed: {
            contrato() {
                return this.$store.getters['contratos/contrato-proyectado/currentContrato']
            },
        }
    }
</script>

<style scoped>
table#tabla-conceptos {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
}

table#tabla-conceptos th, table#tabla-conceptos td {
    border: 1px solid #dee2e6;
}

table thead th
{
    padding: 0.2em;

    background-color: #f2f4f5;
    font-weight: bold;
    color: black;
    overflow: hidden;
    text-align: center;
}

table thead th {
    text-align: center;
}
table tbody tr
{
    border-width: 0 1px 1px 1px;
    border-style: none solid solid solid;
    border-color: white #CCCCCC #CCCCCC #CCCCCC;
}
table tbody td,
table tbody th
{
    border-right: 1px solid #ccc;
    color: #242424;
    line-height: 20px;
    overflow: hidden;
    padding: 1px 5px;
    text-align: left;
    text-overflow: ellipsis;
    -o-text-overflow: ellipsis;
    -ms-text-overflow: ellipsis;
    white-space: nowrap;
}

table col.clave { width: 120px; }
table col.icon { width: 25px; }
table col.monto { width: 115px; }
table col.pct { width: 60px; }
table col.unidad { width: 80px; }
table col.clave  {width: 100px; }

table tbody td input.text
{
    border: none;
    padding: 0;
    margin: 0;
    width: 100%;
    background-color: transparent;
    font-family: inherit;
    font-size: inherit;
    font-weight: bold;
}

table tbody .numerico
{
    text-align: right;
    padding-left: 0;
    white-space: normal;
}

.text.is-invalid {
    color: #dc3545;
}

table tbody td input.text {
    text-align: right;
}

</style>
