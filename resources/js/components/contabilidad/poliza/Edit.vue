<template>
    <div class="row" v-if="!cargando">
        <div class="col-12">
            <!-- Main content -->
            <div class="invoice p-3 mb-3">
                <!-- title row -->
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <i class="fa fa-list"></i>  Información de Prepóliza
                        </h4>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- info row -->
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td class="bg-gray-light"><b>Tipo Póliza SAO:</b><br>{{ poliza.transaccionInterfaz.descripcion }}</td>
                            <td class="bg-gray-light"><b>Fecha de Prepóliza:</b><br>
                                <span v-if="$root.can('editar_fecha_prepoliza')">
                                    <input type="date" class="form-control" v-model="poliza.fecha"/>
                                </span>
                                <span v-else>
                                    {{ poliza.fecha}}
                                </span>

                            </td>
                            <td class="bg-gray-light"><b>Usuario Solicita:</b><br>{{ poliza.usuario_solicita }}</td>
                            <td class="bg-gray-light"><b>Cuadre:</b><br>$ {{ parseFloat(poliza.cuadre).formatMoney(2, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td class="bg-gray-light"><b>Estatus:</b><br><estatus-label :value="poliza.estatusPrepoliza"></estatus-label></td>
                            <td class="bg-gray-light"><b>Póliza Contpaq:</b><br>#{{ poliza.poliza_contpaq }}</td>
                            <td class="bg-gray-light"><b>Tipo de Póliza:</b><br>{{ poliza.tipoPolizaContpaq.descripcion }}</td>
                            <td class="bg-gray-light"><b>Transacción Antecedente:</b><br>
                                <span v-if="poliza.factura">
                                    [{{ poliza.factura.tipo.descripcion }}]  #{{ poliza.factura.numero_folio }}
                                </span>
                                <span v-else-if="poliza.traspaso">
                                    [Traspaso] #{{ poliza.traspaso.numero_folio }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="bg-gray-light">
                                <b>Concepto:</b><br>
                                <textarea name="concepto" type="text" class="form-control" v-model="poliza.concepto"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.row -->

                <!-- Table row -->
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped" v-if="!cargando">
                            <thead>
                            <tr>
                                <th class="bg-gray-light">#</th>
                                <th class="bg-gray-light">Cuenta Contable</th>
                                <th class="bg-gray-light">Tipo Cuenta Contable</th>
                                <th class="bg-gray-light">Tipo</th>
                                <th class="bg-gray-light">Debe</th>
                                <th class="bg-gray-light">Haber</th>
                                <th class="bg-gray-light">Referencia</th>
                                <th class="bg-gray-light">Concepto</th>
                                <th class="bg-gray-light">
                                    <button type="button" class="btn btn-sm btn-outline-success"><i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(movimiento, i) in poliza.movimientos.data">
                                <td>{{ i + 1 }}</td>
                                <td>
                                    <span v-if="(movimiento.cuenta_contable && $root.can('editar_cuenta_contable_movimiento_prepoliza')) || $root.can('ingresar_cuenta_faltante_movimiento_prepoliza')">
                                        <span v-if="movimiento.id_tipo_cuenta_contable == 1 && movimiento.cuenta_contable != null">
                                            {{ movimiento.cuenta_contable }}
                                        </span>
                                        <span v-else>
                                        <input
                                               v-mask="{regex: datosContables}"
                                               type="text"
                                               class="form-control"
                                               v-model="movimiento.cuenta_contable">
                                        </span>
                                    </span>
                                    <span v-else>
                                        <label v-if="movimiento.cuenta_contable">{{ movimiento.cuenta_contable }}</label>
                                        <label v-else>{{ datosContables }}</label>
                                    </span>
                                </td>
                                <td>{{ movimiento.tipoCuentaContable ? movimiento.tipoCuentaContable.descripcion : 'No registrada'}}</td>
                                <td>
                                    <span v-if="$root.can('editar_tipo_movimiento_prepoliza')">
                                        <select class="form-control" v-model="movimiento.tipo.id">
                                            <option value="1">Cargo</option>
                                            <option value="2">Abono</option>
                                        </select>
                                    </span>
                                    <span v-else>
                                        {{ movimiento.tipo.descripcion }}
                                    </span>
                                </td>
                                <td>
                                    <span v-if="movimiento.tipo.id == 1">
                                        <span v-if="$root.can('editar_importe_movimiento_prepoliza')">
                                            <input
                                                    type="number"
                                                    step="any"
                                                    class="form-control"
                                                    v-model="movimiento.importe"/>
                                        </span>
                                        <span v-else>
                                            ${{ parseFloat(movimiento.importe).formatMoney(2, '.', ',') }}
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    <span v-if="movimiento.tipo.id == 2">
                                        <span v-if="$root.can('editar_importe_movimiento_prepoliza')">
                                            <input
                                                    type="number"
                                                    step="any"
                                                    class="form-control"
                                                    v-model="movimiento.importe"/>
                                        </span>
                                        <span v-else>
                                            ${{ parseFloat(movimiento.importe).formatMoney(2, '.', ',') }}
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    <input v-validate="'required'" class="form-control" type="text" size="5" v-model="movimiento.referencia">
                                </td>
                                <td>
                                    <textarea class="form-control" rows="3" cols="40" wrap="soft" v-model="movimiento.concepto"></textarea>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="4" class="text-center" :style="color">
                                    <b>Sumas Iguales</b>
                                </th>
                                <th :style="color">
                                    <b>$&nbsp;{{(parseFloat(sumaDebe)).formatMoney(2,'.',',')}}</b>
                                </th>
                                <th :style="color">
                                    <b>$&nbsp;{{(parseFloat(sumaHaber)).formatMoney(2,'.',',')}}</b>
                                </th>
                                <th :style="color" colspan="3"></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.invoice -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</template>

<script>
    import EstatusLabel from "./partials/EstatusLabel";

    export default {
        name: "poliza-edit",
        components: {EstatusLabel},
        props: ['id'],
        data() {
            return {
                poliza: null,
            }
        },
        mounted() {
            this.find(this.id).then(data => {
                this.poliza = data;
            });
        },

        methods: {
            find(id) {
                return this.$store.dispatch('contabilidad/poliza/find', {id: id, params: {include: 'factura,movimientos,traspaso'}})

            }
        },

        computed: {
            cargando() {
                return this.$store.getters['contabilidad/poliza/cargando']
            },
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },
            sumaDebe() {
                let result = 0;
                this.poliza.movimientos.data.forEach(function (movimiento, i) {
                    if (movimiento.tipo.id == 1) {
                        result += parseFloat(movimiento.importe);
                    }
                })
                return result
            },
            sumaHaber() {
                let result = 0;
                this.poliza.movimientos.data.forEach(function (movimiento, i) {
                    if (movimiento.tipo.id == 2) {
                        result += parseFloat(movimiento.importe);
                    }
                })
                return result
            },
            color() {
                if(this.sumaDebe - this.sumaHaber > 0.99){
                    return {
                        'background-color': 'red',
                        'color': 'white'
                    }
                }
                else{
                    return {
                        'background-color': 'gray',
                        'color': 'white'
                    }
                }
            }
        }
    }
</script>

<style scoped>
    textarea[name="concepto"] {
        resize: none;
    }
</style>