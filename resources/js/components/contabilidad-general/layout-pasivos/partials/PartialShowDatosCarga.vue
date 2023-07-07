<template>
    <span>
        <div class="spinner-border text-success" role="status" v-if="!layout">
           <span class="sr-only">Cargando...</span>
        </div>
        <div v-else>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-sm table-fs-sm">
                        <thead>
                            <tr>
                                <th class="c200">Nombre Archivo</th>
                                <th class="c90">Usuario Carga</th>
                                <th class="c90">Fecha / Hora Carga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{layout.nombre_archivo}}
                                </td>
                                <td >
                                    {{layout.usuario_carga}}
                                </td>
                                <td>
                                    {{layout.fecha_hora_carga}}
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
export default {
    name: "layout-partial-show-datos-carga",
    props : ['id','layout_parametro'],
    components : {},
    data(){
        return {
            cargando:false,
        }
    },
    mounted() {
    },
    methods: {
    },
    computed: {
        layout(){
            return this.$store.getters['contabilidadGeneral/layout-pasivo/currentLayout'];
        },
    },
}
</script>

<style scoped>

tr.hover td{
    background-color: #b8daa9;
}

tr.click td{
    background-color: #50b920;
}

.form-control {
    font-size: 10px !important;
}
table {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
}
table.table-fs-sm{
    font-size: 10px;
}

table th,  table td {
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

    position: sticky;
    position: -webkit-sticky;
    top: 0;
    z-index: 2;
}

table thead th.no_negrita
{
    padding: 0.2em;

    background-color: #f2f4f5;
    font-weight: normal;
    color: black;
    overflow: hidden;
    text-align: center;
}

table td.sin_borde {
    border: none;
    padding: 2px 5px;
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
    padding: 2px 5px;
    text-align: left;
    text-overflow: ellipsis;
    -o-text-overflow: ellipsis;
    -ms-text-overflow: ellipsis;
    white-space: nowrap;
}

.encabezado{
    text-align: center;
    background-color: #f2f4f5;
    font-weight: bold;
}

.sin_borde{
    border:none; background-color:#FFF
}
</style>
