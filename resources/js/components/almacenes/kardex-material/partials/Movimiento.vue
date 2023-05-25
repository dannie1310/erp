<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row" v-if="movimientos">
                                <div class="form-check form-check-inline">
                                    <input v-model="v_fecha" class="form-check-input" type="checkbox" id="v_fecha" value="v_fecha">
                                    <label class="form-check-label" for="v_fecha">Ver Fecha de Registro</label>
                                </div>
                            </div>
                            <br>
                            <div class="row" v-if="movimientos">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th class="encabezado c50" rowspan="3">
                                                    Fecha
                                                </th>
                                                <th class="encabezado v_fecha c120"  style="display: none" rowspan="3">
                                                    Fecha de Registro
                                                </th>
                                                <th class="encabezado" colspan="2" rowspan="2">
                                                    Transaccion
                                                </th>
                                                <th class="encabezado" colspan="2">Cantidad</th>
                                                <th class="encabezado c100" rowspan="3">Saldo</th>
                                            </tr>
                                            <tr>
                                                <th class="encabezado" colspan="2">({{ movimientos.unidad }})</th>
                                            </tr>
                                            <tr>
                                                <th class="c90">Tipo</th>
                                                <th class="c90">Folio</th>
                                                <th class="c100">Entrada</th>
                                                <th class="c100">Salida</th>
                                            </tr>
                                        </thead>
                                        <tbody v-for="movimiento in movimientos.data">
                                            <tr>
                                                <td style="text-align: center; " :style="movimiento.color">
                                                    {{movimiento.fecha}}
                                                </td>
                                                <td style="text-align: center; display: none" class="v_fecha">
                                                    {{movimiento.FechaHoraRegistro}}
                                                </td>
                                                 <td style="text-align: left">
                                                    {{movimiento.tipo}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{movimiento.folio}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{ parseFloat(movimiento.cantidad_entrada).formatea()}}
                                                </td>
                                                <td style="text-align: right">
                                                    {{ parseFloat(movimiento.cantidad_salida).formatea() }}
                                                </td>
                                                <td style="text-align: right">
                                                    {{ parseFloat(movimiento.saldo_restante).formatea() }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row" v-else>
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "Movimiento",
    props: ['movimientos'],
    data() {
        return {
            cargando: false,
            v_fecha: false
        }
    },
    watch: {
        v_fecha(val)
        {
            if(val) {
                $('.v_fecha').removeAttr('style');
            }else{
                $('.v_fecha').css('display', 'none');
            }
        },
    },
}
</script>

<style scoped>

</style>
