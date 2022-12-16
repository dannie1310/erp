<template>
    <span>
        <div  v-if="cargando">
            <div class="row" >
                <div class="col-md-12">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="card">
                <div class="card-body">
                     <div class="row">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <tr>
                                        <th class="encabezado">
                                            Folio
                                        </th>
                                        <th class="encabezado">
                                            Autorizado de Remesa
                                        </th>
                                        <th class="encabezado">
                                            A Pagar
                                        </th>
                                        <th class="encabezado">
                                            Tipo Cambio
                                        </th>
                                        <th class="encabezado">
                                            Total
                                        </th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center">
                                            {{solicitud.numero_folio}}
                                        </td>
                                        <td style="text-align: right" >
                                            {{solicitud.monto_autorizado_remesa_format}}
                                        </td>
                                        <td v-if="solicitud.tipo_transaccion == 65">
                                            <input
                                                type="text" @change="calcular"
                                                class="form-control"
                                                name="monto_pagar"
                                                style="text-align: right"
                                                data-vv-as="Monto a Pagar"
                                                v-model="monto_pagar"
                                                v-validate="{max_value:solicitud.monto_autorizado, min_value:0}"
                                                :class="{'is-invalid': errors.has('monto_pagar')}"
                                                id="monto_pagar">
                                            <div class="invalid-feedback" v-show="errors.has('monto_pagar')">{{ errors.first('monto_pagar') }}</div>
                                        </td>
                                        <td v-if="solicitud.tipo_transaccion == 72" style="text-align: right">
                                            {{ solicitud.saldo_format }}
                                        </td>
                                        <td v-if="cuenta != '' && cuenta.moneda.tipo_cambio != 1 && solicitud.tipo_transaccion == 65 && tipo_cambio_actual != 1" style="text-align: right">
                                            <input
                                                type="text" @change="calcular"
                                                class="form-control"
                                                name="tipo_cambio"
                                                style="text-align: right"
                                                data-vv-as="Tipo de Cambio"
                                                v-model="tipo_cambio"
                                                v-validate="{max_value: tipo_cambio_actual, min_value:0}"
                                                :class="{'is-invalid': errors.has('tipo_cambio')}"
                                                id="tipo_cambio">
                                            <div class="invalid-feedback" v-show="errors.has('tipo_cambio')">{{ errors.first('tipo_cambio') }}</div>
                                        </td>
                                        <td style="text-align: right" v-else-if="cuenta != ''">
                                            {{ parseFloat(tipo_cambio_actual).formatMoney(2, '.', ',') }}
                                        </td>
                                        <td style="text-align: right" v-else>0.00</td>
                                        <td style="text-align: right">
                                            {{ parseFloat(monto_calculado).formatMoney(2, '.', ',') }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" v-on:click="salir">
                        <i class="fa fa-angle-left"></i>
                        Regresar
                    </button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "kardex-show",
    props: ['id'],
    mounted() {
        this.find();
    },
    data() {
        return {
            cargando: false,
            materiales: []
        }
    },
    methods: {
        salir(){
            this.$router.push({name: 'kardex-material'});
        },
        find() {
            this.cargando = true;
            this.$store.commit('cadeco/material/SET_MATERIALES', null);
            return this.$store.dispatch('cadeco/material/index', {
                params: {
                    sort: 'descripcion',
                    order: 'asc',
                    scope:'MaterialPorAlmacen:' + this.id,
                }
            }).then(data => {
                this.$store.commit('cadeco/material/SET_MATERIALES', data);
            }).finally(() => {
                this.cargando = false;
            })
        },
    },
}
</script>

<style scoped>

</style>
