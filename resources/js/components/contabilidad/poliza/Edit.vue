<template>
    <span v-if="poliza">
        <div class="row">
            <div class="col-md-12">
                <poliza-validar :poliza="poliza"></poliza-validar>
                <poliza-omitir :poliza="poliza"></poliza-omitir>
                <poliza-ingresar-folio :poliza="poliza"></poliza-ingresar-folio>
                <!--<poliza-ingresar-cuentas :movimientos="movimientosSinCuenta"></poliza-ingresar-cuentas>-->
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Información de Prepóliza
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table class="table table-striped">
                                    <tbody>
                                    <tr>
                                        <td class="bg-gray-light"><b>Tipo Póliza SAO:</b><br>{{
                                            poliza.transaccionInterfaz.descripcion }}
                                        </td>
                                        <td class="bg-gray-light"><b>Fecha de Prepóliza:</b><br>
                                            <span v-if="$root.can('editar_fecha_prepoliza')">
                                                <input
                                                        type="date"
                                                        class="form-control"
                                                        name="fecha"
                                                        v-model="poliza.fecha"
                                                        v-validate="{required: true, date_format: 'yyyy-MM-dd'}"
                                                        data-vv-as="Fecha de Prepóliza"
                                                        :class="{'is-invalid': errors.has('fecha')}"
                                                />
                                                <div class="invalid-feedback" v-show="errors.has('fecha')">
                                                    {{ errors.first('fecha') }}
                                                </div>
                                            </span>
                                            <span v-else>
                                                {{ poliza.fecha}}
                                            </span>
                                        </td>
                                        <td class="bg-gray-light"><b>Usuario Solicita:</b><br>
                                            {{ poliza.usuario_solicita }}
                                        </td>
                                        <td class="bg-gray-light"><b>Cuadre:</b><br>$
                                            {{ parseFloat(poliza.cuadre).formatMoney(2, '.', ',') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-light"><b>Estatus:</b><br>
                                            <estatus-label :value="poliza.estatusPrepoliza"></estatus-label>
                                        </td>
                                        <td class="bg-gray-light">
                                            <b>Póliza Contpaq:</b>
                                            <br>
                                            {{ poliza.poliza_contpaq ? '#' + poliza.poliza_contpaq : '' }}
                                        </td>
                                        <td class="bg-gray-light"><b>Tipo de Póliza:</b><br>
                                            {{ poliza.tipoPolizaContpaq.descripcion }}
                                        </td>
                                        <td class="bg-gray-light"><b>Transacción Antecedente:</b><br>
                                            <span v-if="poliza.transaccionAntecedente">
                                                [{{ poliza.transaccionAntecedente.tipo.descripcion }}]  #{{ poliza.transaccionAntecedente.numero_folio }}
                                            </span>
                                            <span v-else-if="poliza.traspaso">
                                                [Traspaso] #{{ poliza.traspaso.numero_folio }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="bg-gray-light">
                                            <b>Concepto:</b><br>
                                            <textarea
                                                    name="concepto"
                                                    type="text"
                                                    class="form-control"
                                                    v-model="poliza.concepto"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Concepto"
                                                    :class="{'is-invalid': errors.has('concepto')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('concepto')">{{
                                                errors.first('concepto') }}
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

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
                                            <add-movimiento v-on:add="add"></add-movimiento>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(movimiento, i) in movimientosOrdenados"
                                        :class="{'bg-success': ! movimiento.id}">
                                        <td>{{ i + 1 }}</td>
                                        <td>
                                            <span v-if="(movimiento.cuenta_contable && $root.can('editar_cuenta_contable_movimiento_prepoliza')) || $root.can('ingresar_cuenta_faltante_movimiento_prepoliza')">
                                                <span v-if="movimiento.id_tipo_cuenta_contable == 1 && original.movimientos.data[i] ? originalesOrdenados[i].cuenta_contable !=null ? true : false : false">
                                                    {{ movimiento.cuenta_contable }}
                                                </span>
                                                <span v-else>
                                                    <input
                                                            v-mask="{regex: datosContables}"
                                                            type="text"
                                                            class="form-control"
                                                            :name="`cuenta_contable[${i}]`"
                                                            v-model="movimiento.cuenta_contable"
                                                            v-validate="{required: true, regex: datosContables}"
                                                            data-vv-as="Cuenta Contable"
                                                            :class="{'is-invalid': errors.has(`cuenta_contable[${i}]`)}"
                                                    >
                                                    <div class="invalid-feedback" v-show="errors.has(`cuenta_contable[${i}]`)">{{ errors.first(`cuenta_contable[${i}]`) }}</div>
                                                </span>
                                            </span>
                                            <span v-else>
                                                <label v-if="movimiento.cuenta_contable">{{ movimiento.cuenta_contable }}</label>
                                                <label v-else></label>
                                            </span>
                                        </td>
                                        <td>{{ movimiento.tipoCuentaContable ? movimiento.tipoCuentaContable.descripcion :
                                            'No registrada'}}
                                        </td>
                                        <td>
                                            <span v-if="$root.can('editar_tipo_movimiento_prepoliza')">
                                                <select
                                                        class="form-control"
                                                        :name="`id_tipo_movimiento_poliza[${i}]`"
                                                        v-model="movimiento.id_tipo_movimiento_poliza"
                                                        v-validate="{required: true}"
                                                        data-vv-as="Tipo"
                                                        :class="{'is-invalid': errors.has(`id_tipo_movimiento_poliza[${i}]`)}"
                                                >
                                                    <option value="1">Cargo</option>
                                                    <option value="2">Abono</option>
                                                </select>
                                                <div class="invalid-feedback"
                                                     v-show="errors.has(`id_tipo_movimiento_poliza[${i}]`)">{{ errors.first(`id_tipo_movimiento_poliza[${i}]`) }}
                                                </div>
                                            </span>
                                            <span v-else>
                                                {{ movimiento.tipo.descripcion }}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="movimiento.id_tipo_movimiento_poliza == 1">
                                                <span v-if="$root.can('editar_importe_movimiento_prepoliza')">
                                                    <input
                                                            type="number"
                                                            step="any"
                                                            class="form-control"
                                                            :name="`importe[${i}]`"
                                                            v-model="movimiento.importe"
                                                            v-validate="{required: true, decimal: true}"
                                                            data-vv-as="Debe"
                                                            :class="{'is-invalid': errors.has(`importe[${i}]`)}"
                                                    />
                                                    <div class="invalid-feedback" v-show="errors.has(`importe[${i}]`)">{{ errors.first(`importe[${i}]`) }}</div>
                                                </span>
                                                <span v-else>
                                                    ${{ parseFloat(movimiento.importe).formatMoney(2, '.', ',') }}
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="movimiento.id_tipo_movimiento_poliza == 2">
                                                <span v-if="$root.can('editar_importe_movimiento_prepoliza')">
                                                    <input
                                                            type="number"
                                                            step="any"
                                                            class="form-control"
                                                            :name="`importe[${i}]`"
                                                            v-model="movimiento.importe"
                                                            v-validate="{required: true, decimal: true}"
                                                            data-vv-as="Debe"
                                                            :class="{'is-invalid': errors.has(`importe[${i}]`)}"
                                                    />
                                                    <div class="invalid-feedback" v-show="errors.has(`importe[${i}]`)">{{ errors.first(`importe[${i}]`) }}</div>
                                                </span>
                                                <span v-else>
                                                    ${{ parseFloat(movimiento.importe).formatMoney(2, '.', ',') }}
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <input
                                                    class="form-control"
                                                    type="text"
                                                    size="5"
                                                    :name="`referencia[${i}]`"
                                                    v-model="movimiento.referencia"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Referencia"
                                                    :class="{'is-invalid': errors.has(`referencia[${i}]`)}"
                                            >
                                            <div class="invalid-feedback" v-show="errors.has(`referencia[${i}]`)">{{
                                                errors.first(`referencia[${i}]`) }}
                                            </div>
                                        </td>
                                        <td>
                                            <textarea
                                                    class="form-control"
                                                    rows="3"
                                                    cols="40"
                                                    wrap="soft"
                                                    :name="`concepto[${i}]`"
                                                    v-model="movimiento.concepto"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Concepto"
                                                    :class="{'is-invalid': errors.has(`concepto[${i}]`)}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has(`concepto[${i}]`)">{{
                                                errors.first(`concepto[${i}]`) }}
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger" v-if="poliza.estatus != 1 && poliza.estatus != 2"
                                                    @click="remove(movimiento)"><i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-center" :class="color">
                                            <b>Sumas Iguales</b>
                                        </th>
                                        <th :class="color">
                                            <b>$&nbsp;{{(parseFloat(sumaDebe)).formatMoney(2,'.',',')}}</b>
                                        </th>
                                        <th :class="color">
                                            <b>$&nbsp;{{(parseFloat(sumaHaber)).formatMoney(2,'.',',')}}</b>
                                        </th>
                                        <th :class="color" colspan="3"></th>
                                    </tr>
                                    </tfoot>
                                </table>
                                <div class="col-sm-12" style="text-align: right">
                                    <h4><b>Total de la Prepóliza:</b>
                                        $&nbsp;{{ (parseFloat(poliza.total)).formatMoney(2, '.', ',') }}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-info float-right" type="submit"
                                        :disabled="errors.count() > 0 || !cuadrado || !cambio">
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import EstatusLabel from "./partials/EstatusLabel";
    import AddMovimiento from "./partials/AddMovimiento";
    import PolizaValidar from "./partials/Validar";
    import PolizaOmitir from "./partials/Omitir";
    import PolizaIngresarFolio from "./partials/IngresarFolio";
    import PolizaIngresarCuentas from "./partials/IngresarCuentas";

    export default {
        name: "poliza-edit",
        components: {
            PolizaIngresarCuentas,
            PolizaIngresarFolio, PolizaOmitir, PolizaValidar, AddMovimiento, EstatusLabel},
        props: ['id'],
        data() {
            return {
                poliza: null,
                original: null,
                cargando: false
            }
        },
        mounted() {
            this.$Progress.start();
            this.find()
                .finally(() => {
                    this.$Progress.finish();
                })
        },

        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('contabilidad/poliza/SET_POLIZA', null);
                return this.$store.dispatch('contabilidad/poliza/findEdit', {
                    id: this.id,
                    params: { include: 'transaccionAntecedente,movimientos,traspaso' }
                })
                    .then(data => {
                        this.$store.commit('contabilidad/poliza/SET_POLIZA', data);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            update(payload) {
                return this.$store.dispatch('contabilidad/poliza/update', payload)
                    .then(data => {
                        this.$store.commit('contabilidad/poliza/UPDATE_POLIZA', data);
                        let id = this.id
                        this.$router.push({ name: 'poliza-show', params: { id }})
                    })
            },

            add(movimiento) {
                this.poliza.movimientos.data.push(movimiento)
                this.original.movimientos.data.push(movimiento)
            },

            remove(movimiento) {
                swal({
                    title: "Quitar Movimiento",
                    text: "¿Estás seguro de que deseas quitar el movimiento de la Prepóliza?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Quitar',
                            closeModal: true,
                        }
                    },
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            if(!movimiento.id) {
                                this.original.movimientos.data = this.poliza.movimientos.data.filter(function (m) {
                                    return JSON.stringify(movimiento) != JSON.stringify(m)
                                })
                            }
                            this.poliza.movimientos.data = this.poliza.movimientos.data.filter(function (m) {
                                return JSON.stringify(movimiento) != JSON.stringify(m)
                            })
                        }
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update({
                            id: this.id,
                            data: this.poliza,
                            params: { include: 'transaccionAntecedente,movimientos,traspaso' }
                        });
                    }
                });
            }
        },

        watch: {
            currentPoliza: {
                handler(poliza) {
                    if (poliza) {
                        this.poliza = JSON.parse(JSON.stringify(poliza));
                        this.original = JSON.parse(JSON.stringify(poliza));
                    }
                },
                deep: true
            }
        },

        computed: {
            movimientosSinCuenta() {
                let array = this.original.movimientos.data.filter((mov) => {
                    return mov.cuenta_contable == null
                })

                return Array.from(new Set(array.map(s => s.id_tipo_cuenta_contable)))
                    .map(id => {
                        return {
                            id_tipo_cuenta_contable: id,
                            descripcion: array.find(s => s.id_tipo_cuenta_contable === id).tipoCuentaContable.descripcion
                        }
                    })
            },
            currentPoliza() {
                return this.$store.getters['contabilidad/poliza/currentPoliza']
            },
            diff() {
                return diff(this.poliza, this.original)
            },
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },
            sumaDebe() {
                let result = 0;
                this.poliza.movimientos.data.forEach(function (movimiento, i) {
                    if (movimiento.id_tipo_movimiento_poliza == 1) {
                        result += parseFloat(movimiento.importe);
                    }
                })
                return result
            },
            sumaHaber() {
                let result = 0;
                this.poliza.movimientos.data.forEach(function (movimiento, i) {
                    if (movimiento.id_tipo_movimiento_poliza == 2) {
                        result += parseFloat(movimiento.importe);
                    }
                })
                return result
            },
            cuadrado() {
                return Math.abs(this.sumaDebe - this.sumaHaber) <= 0.99;
            },
            color() {
                if (!this.cuadrado) {
                    return 'bg-danger'
                } else {
                    return 'bg-gray'
                }
            },
            cambio() {
                return JSON.stringify(this.poliza) != JSON.stringify(this.original) || this.nuevosMovimientos
            },

            nuevosMovimientos() {
                return !!this.original.movimientos.data.find(mov => {
                    return !mov.id
                })
            },

            originalesOrdenados() {
                return _.sortBy(this.original.movimientos.data, ['id_tipo_movimiento_poliza', 'concepto']);

            },
            movimientosOrdenados() {
                return _.sortBy(this.poliza.movimientos.data, ['id_tipo_movimiento_poliza', 'concepto']);

            }
        }
    }
</script>

<style scoped>
    textarea[name="concepto"] {
        resize: none;
    }
</style>
