<template>
    <div class="row" v-if="solicitud">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-sm">
                    <tr>
                        <th class="encabezado">
                            Empresa
                        </th>
                        <th class="encabezado">
                            Departamento
                        </th>
                        <th class="encabezado">
                            Solicitud de Pago
                        </th>
                    </tr>
                    <tr>
                        <th>
                            {{solicitud.empresa_descripcion}}
                        </th>
                        <th>
                            {{solicitud.departamento}}
                        </th>
                        <th>
                            {{solicitud.folio_format}}
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado" colspan="2">
                            Empleado
                        </th>
                        <th class="encabezado">
                            Obra / Proyecto
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            {{ solicitud.empleado_descripcion }}
                        </td>
                        <td>
                           {{ solicitud.ubicacion }}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado  c130">
                            Fecha
                        </th>
                        <th class="encabezado  c130">
                            Fecha Del:
                        </th>
                        <th class="encabezado  c130">
                            Fecha Al:
                        </th>
                    </tr>
                    <tr>
                        <td>
                            {{ solicitud.fecha_format }}
                        </td>
                        <td v-if="solicitud.comprobacion">
                            {{ solicitud.comprobacion.del_format }}
                        </td>
                        <td v-else></td>
                        <td v-if="solicitud.comprobacion">
                            {{ solicitud.comprobacion.al_format }}
                        </td>
                        <td v-else></td>
                    </tr>
                    <tr>
                        <th class="encabezado">Importe</th>
                        <th class="encabezado">Comprobado</th>
                        <th class="encabezado">Saldo</th>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 15px">{{ solicitud.importe_format }}</td>
                        <td style="text-align: right; font-size: 15px"><b>{{ solicitud.comprobado_format }}</b></td>
                        <td style="text-align: right; font-size: 15px"><b>{{ solicitud.saldo_format }}</b></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';
export default {
    name: "TablaDatos",
    components: { datepicker, es },
    props: ['solicitud'],
    data(){
        return{
            es: es
        }
    },
    methods :{
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
    }
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
