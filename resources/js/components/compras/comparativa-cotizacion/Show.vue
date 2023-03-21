<template>
    <span>
        <div class="card" v-if="cargando">
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
                        <encabezado-solicitud-compra v-bind:solicitud_compra="solicitud"></encabezado-solicitud-compra>

                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="custom-control custom-switch" >
                            <input type="checkbox" class="custom-control-input" id="cotizaciones_completas" v-model="cotizaciones_completas" >
                            <label class="custom-control-label" for="cotizaciones_completas" >Ver todas las cotizaciones</label>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-sm table-fs-sm">
                            <thead>
                            <tr  >
                                <td colspan="4" class="sin_borde"></td>
                                <template v-for = "(cotizacion, c) in cotizaciones" >
                                    <th class="c300 no_negrita" colspan="5" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <b>{{cotizacion.numero_folio}}</b>
                                            </div>
                                            <div class="col-md-9">
                                                <b>{{cotizacion.empresa}}</b>
                                            </div>
                                        </div>
                                        <hr style="margin: 1px" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        <div class="row">
                                            <div class="col-md-3">
                                                Fecha
                                            </div>
                                            <div class="col-md-3">
                                                Tipo
                                            </div>
                                            <div class="col-md-3">
                                                Folio de Invitación
                                            </div>
                                            <div class="col-md-3">
                                                Fecha de Envío
                                            </div>

                                        </div>
                                        <hr style="margin: 1px" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        <div class="row">

                                            <div class="col-md-3">
                                                <b>{{cotizacion.fecha}}</b>
                                            </div>
                                            <div class="col-md-3">
                                                <b :style="cotizacion.tipo_str == 'Contraoferta'?`color:#3386FF`:``">{{cotizacion.tipo_str}}</b>
                                            </div>
                                            <div class="col-md-3">
                                                <b>{{cotizacion.folio_invitacion}}</b>
                                            </div>
                                            <div class="col-md-3">
                                                <b>{{cotizacion.fecha_envio}}</b>
                                            </div>
                                        </div>
                                        <hr style="margin: 1px" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        <div class="row">
                                            <div class="col-md-3">
                                                % Anticipo
                                            </div>
                                            <div class="col-md-3">
                                                Crédito (días)
                                            </div>
                                            <div class="col-md-3">
                                                Plazo de Entrega (días)
                                            </div>
                                            <div class="col-md-3">
                                                Vigencia (días)
                                            </div>
                                        </div>
                                        <hr style="margin: 1px" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                         <div class="row">
                                            <div class="col-md-3">
                                                <b :class="cotizacion.anticipo == mejor_anticipo?`mejor_dato`:cotizacion.anticipo == peor_anticipo ?`peor_dato`:``">
                                                    {{cotizacion.anticipo}}
                                                </b>
                                            </div>
                                            <div class="col-md-3">
                                                <b :class="cotizacion.dias_credito == mejor_credito?`mejor_dato`:cotizacion.dias_credito == peor_credito ?`peor_dato`:``">{{cotizacion.dias_credito}}</b>
                                            </div>
                                            <div class="col-md-3">
                                                <b :class="cotizacion.plazo_entrega == mejor_plazo?`mejor_dato`:cotizacion.plazo_entrega == peor_plazo ?`peor_dato`:``">{{cotizacion.plazo_entrega}}</b>
                                            </div>
                                            <div class="col-md-3">
                                                <b>{{cotizacion.vigencia}}</b>
                                            </div>
                                        </div>
                                    </th>

                                </template>

                            </tr>
                            <tr>
                                <th  class="index_corto">
                                    #
                                </th>
                                <th class="c500">
                                    Descripción
                                </th>
                                <th class="c70" >
                                    Unidad
                                </th>
                                <th class="c70" >
                                    Cantidad
                                </th>
                                <template v-for = "(cotizacion, c) in cotizaciones" >
                                    <th :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        Precio Unitario
                                    </th>
                                    <th :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        IV
                                    </th>
                                    <th :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        Descuento
                                    </th>
                                    <th :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``" >
                                        Moneda
                                    </th>
                                    <th :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        Importe Pesos (MXN)
                                    </th>
                                </template>
                            </tr>
                            </thead>
                            <tbody>

                            <tr  v-for="(partida, i) in partidas">
                                <td>
                                    {{partida.indice}}
                                </td>
                                <td>
                                    {{partida.material}} {{partida.indice}}
                                </td>
                                <td>
                                    {{partida.unidad}}
                                </td>
                                <td style="text-align: right">
                                    {{partida.cantidad}}
                                </td>
                                <template v-for = "(cotizacion, c) in cotizaciones" >
                                    <td style="text-align: right ;"  :class="partida.cotizaciones[c] && partida.cotizaciones[c].precio_con_descuento == precios_menores[i]?`mejor_opcion`:c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        <span v-if="partida.cotizaciones[c]">
                                            ${{ parseFloat(partida.cotizaciones[c].precio_unitario).formatMoney(2,".",",")}}
                                        </span>
                                    </td>
                                    <td style="text-align: right ;"  :class="partida.cotizaciones[c] && partida.cotizaciones[c].precio_con_descuento == precios_menores[i]?`mejor_opcion`:c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        <span v-if="partida.cotizaciones[c]">
                                            {{ partida.cotizaciones[c].iv }}
                                        </span>
                                    </td>
                                    <td style="text-align: right;" :class="partida.cotizaciones[c] && partida.cotizaciones[c].precio_con_descuento == precios_menores[i]?`mejor_opcion`:c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        <span v-if="partida.cotizaciones[c]">
                                            {{partida.cotizaciones[c].descuento_partida_format}}
                                        </span>
                                    </td>
                                    <td :class="partida.cotizaciones[c] && partida.cotizaciones[c].precio_con_descuento == precios_menores[i]?`mejor_opcion`:c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        <span v-if="partida.cotizaciones[c]">
                                            {{partida.cotizaciones[c].moneda}}
                                        </span>
                                    </td>
                                    <td style="text-align: right" :class="partida.cotizaciones[c] && partida.cotizaciones[c].precio_con_descuento == precios_menores[i]?`mejor_opcion`:c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                        <span v-if="partida.cotizaciones[c]">
                                            ${{ parseFloat(partida.cotizaciones[c].precio_total_moneda).formatMoney(2,".",",")}}
                                        </span>
                                    </td>
                                </template>
                            </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="border: none"></td>

                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="4" style="text-align: right;border:none" class="titulo">Subtotal Pesos MXN:</td>
                                        <td style="text-align: right" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">${{ parseFloat(cotizacion.suma_subtotal_partidas).formatMoney(2,".",",")}}</td>
                                    </template>

                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>

                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="4" style="text-align: right;border:none" class="titulo">Descuento Global:</td>
                                        <td style="text-align: right" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">{{cotizacion.descuento_global}}</td>
                                    </template>

                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="4" style="text-align: right;border:none" class="titulo" >Subtotal Pesos MXN:</td>
                                        <td style="text-align: right" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">${{ parseFloat(cotizacion.subtotal_con_descuento).formatMoney(2,".",",")}}</td>
                                    </template>
                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="4" style="text-align: right;border:none" class="titulo" >IVA ({{cotizacion.tasa_iva_format}}%):</td>
                                        <td style="text-align: right" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">${{ parseFloat(cotizacion.iva).formatMoney(2,".",",")}}</td>
                                    </template>
                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="4" style="text-align: right;border:none" class="titulo" >Total:</td>
                                        <td style="text-align: right" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``"><b>${{ parseFloat(cotizacion.total).formatMoney(2,".",",")}}</b></td>
                                    </template>
                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="5" style="text-align: center;border: none" >&nbsp;</td>
                                    </template>
                                </tr>

                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="4" style="text-align: right;border:none" class="titulo" >Total Mejor Opción:</td>
                                        <td style="text-align: right" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">${{ parseFloat(suma_mejor_opcion).formatMoney(2,".",",")}}</td>
                                    </template>
                                </tr>

                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="4" style="text-align: right;border:none" class="titulo" >Diferencia:</td>
                                        <td style="text-align: right" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">${{ parseFloat(cotizacion.total- suma_mejor_opcion).formatMoney(2,".",",")}}</td>
                                    </template>
                                </tr>

                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="4" style="text-align: right;border:none" class="titulo" >Índice de Variación:</td>
                                        <td style="text-align: right" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">
                                            <b :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">{{cotizacion.ivg}}</b>
                                        </td>
                                    </template>
                                </tr>

                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="5" style="text-align: center;border: none" >&nbsp;</td>
                                    </template>
                                </tr>

                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="5" style="text-align: center;" class="encabezado" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``"><b>Observaciones</b></td>
                                    </template>
                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="5" :class=" c == mejor_cotizacion ?`mejor_cotizacion`:``">{{ cotizacion.observaciones }}</td>
                                    </template>
                                </tr>
                                <tr>
                                    <td :colspan="3 + (cantidad_cotizaciones * 4)" style="border: none">&nbsp;</td>
                                </tr>
                                </tfoot>
                        </table>
                                <template v-if="exclusiones.cantidad > 0">
                                    <table class="table table-sm table-fs-sm">
                                    <tr>
                                        <td colspan="4" style="border: none"></td>
                                        <td :colspan="cantidad_cotizaciones * 3" class="encabezado">EXCLUSIONES</td>
                                    </tr>
                                    <tr>
                                        <th class="encabezado index_corto" >
                                            #
                                        </th>
                                        <th class="encabezado c500" >
                                            Descripción
                                        </th>
                                        <th  class="encabezado c70">
                                            Unidad
                                        </th>
                                        <th class="encabezado c70">
                                            Cantidad
                                        </th>

                                         <template v-for = "(cotizacion, c) in cotizaciones" >
                                            <th class="encabezado">
                                                Precio Unitario
                                            </th>
                                            <th class="encabezado">
                                                Moneda
                                            </th>
                                            <th class="encabezado" >
                                                Importe Pesos (MXN)
                                            </th>
                                        </template>
                                    </tr>
                                    <template v-for="(exclusion, iex) in exclusiones">
                                        <tr  v-if="exclusion.importe>0">
                                            <td >{{exclusion.indice}}</td>
                                            <td >{{exclusion[0].descripcion}}</td>
                                            <td >{{exclusion[0].unidad}}</td>
                                            <td >{{exclusion[0].cantidad}}</td>
                                            <template v-for = "(cotizacion, c) in cotizaciones" >
                                                <template v-if="c == iex">
                                                    <td style="text-align: right;">${{ parseFloat(exclusion[0].precio_unitario).formatMoney(2,".",",")}}</td>
                                                    <td >{{exclusion[0].moneda}}</td>
                                                    <td style="text-align: right;" >${{ parseFloat(exclusion[0].total).formatMoney(2,".",",") }}</td>
                                                </template>
                                                <template v-else>
                                                    <td >&nbsp;</td>
                                                    <td >&nbsp;</td>
                                                    <td >&nbsp;</td>
                                                </template>
                                            </template>
                                        </tr>
                                    </template>
                                    <tr>
                                        <td colspan="4" style="border: none"></td>
                                        <template v-for = "(cotizacion, c) in cotizaciones" >
                                            <td colspan="2" style="text-align: right; border: none">Total Exclusiones:</td>
                                            <td style="text-align: right" v-if="exclusiones[c] && exclusiones[c].importe>0">${{ parseFloat(exclusiones[c].importe).formatMoney(2,".",",")}}</td>
                                            <td style="text-align: right" v-else >-</td>
                                        </template>
                                    </tr>

                                    <tr>
                                        <td :colspan="4 +(cantidad_cotizaciones *5)" style="border: none">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td colspan="4" style="border: none"></td>
                                        <template v-for = "(cotizacion, c) in cotizaciones" >
                                            <td colspan="2" style="text-align: right; border: none">Total Comparativa:</td>
                                            <td style="text-align: right" v-if="exclusiones[c] && exclusiones[c].importe>0"><b>${{ parseFloat(exclusiones[c].importe + cotizacion.total).formatMoney(2,".",",")}}</b></td>
                                            <td style="text-align: right" v-else><b>${{ parseFloat(cotizacion.total).formatMoney(2,".",",")}}</b></td>
                                        </template>
                                    </tr>
                                    </table>
                                </template>


                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                    <button type="button" class="btn btn-primary" v-on:click="pedirContraOferta"><i class="fa fa-comments-dollar"></i>Pedir Contraoferta</button>
                    <button type="button" class="btn btn-primary" v-on:click="invitar"><i class="fa fa-envelope"></i>Invitar a Cotizar</button>
                </div>
            </div>
        </div>
        <div class="modal fade" ref="modal_proveedores" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-check"></i> Seleccionar proveedores:</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="callout callout-warning">
                                    <h6 style="color: #c69500"><i class="fa fa-info-circle" style="color: #c69500"></i>Atención</h6>
                                    Seleccione los proveedores a los que desea enviar una invitación para realizar una contraoferta
                                </div>
                            </div>
                        </div>
                        <template >
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                    <label for="fecha_cierre" style="font-size: 10px">Fecha de Cierre:</label>
                                        <datepicker v-model = "fecha_cierre"
                                                    name = "Fecha de Cierre"
                                                    id = "fecha_cierre"
                                                    :format = "formatoFecha"
                                                    :language = "es"
                                                    :bootstrap-styling = "true"
                                                    class = "form-control"
                                                    v-validate="{required: true}"
                                                    style="font-size: 10px; padding: 0.375rem"
                                                    :disabled-dates="fechasDeshabilitadas"
                                                    :class="{'is-invalid': errors.has('fecha_cierre')}"
                                        />
                                        <div style="display:block" class="invalid-feedback" v-show="errors.has('fecha_cierre')">{{ errors.first('fecha_cierre') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-sm table-fs-sm">
                                        <thead>
                                            <tr>
                                                <th style="width: 15px">

                                                </th>
                                                <th>
                                                    Proveedor
                                                </th>
                                                <th>
                                                    Sucursal
                                                </th>
                                                <th>
                                                    Contacto
                                                </th>
                                                <th>
                                                    Correo
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(proveedor, i) in proveedores">
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label" style="cursor:pointer" >
                                                            <input class="form-check-input" type="checkbox" name="proveedor_en_catalogo" v-model="proveedor.seleccionado_contraoferta" value="1" >
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{proveedor.razon_social}}
                                                </td>
                                                <td>
                                                    {{proveedor.sucursal}}
                                                </td>
                                                <td>
                                                    <span v-if="proveedor.usuario_correo != ''">
                                                        {{proveedor.sucursal_contacto}}
                                                    </span>
                                                    <span v-else>
                                                        <input
                                                            :name="`contacto_${i}`"
                                                            :id="`contacto_${i}`"
                                                            v-model="proveedor.sucursal_contacto"
                                                            type="text"
                                                            style="font-size: 10px; padding: 0.375rem"
                                                            class="form-control"
                                                            v-validate ="{ required: proveedor.seleccionado_contraoferta == 1 ? true : false}"
                                                            :class="{'is-invalid': errors.has(`contacto_${i}`)}"
                                                        />
                                                        <div style="display:block" class="invalid-feedback" v-show="errors.has(`contacto_${i}`)">Ingrese el nombre del contacto</div>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span v-if="proveedor.usuario_correo != ''">
                                                        {{proveedor.usuario_correo}}
                                                    </span>
                                                    <span v-else>
                                                        <input
                                                            :name="`correo_${i}`"
                                                            :id="`correo_${i}`"
                                                            v-model="proveedor.sucursal_correo"
                                                            type="text"
                                                            style="font-size: 10px; padding: 0.375rem"
                                                            class="form-control"
                                                            v-validate ="{ required: proveedor.seleccionado_contraoferta == 1 ? true : false, email:true}"
                                                            :class="{'is-invalid': errors.has(`correo_${i}`)}"
                                                        />
                                                        <div style="display:block" class="invalid-feedback" v-show="errors.has(`correo_${i}`)">Debe ingresar un correo válido</div>

                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row" >
                                <div class="col-md-12">
                                    <ckeditor v-model="cuerpo_correo" ></ckeditor>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal"><i class="fa fa-close"></i>Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="enviar" :disabled="errors.count() > 0"><i class="fa fa-envelope"></i> Enviar</button>
                    </div>
                </div>
             </div>
        </div>
    </span>

</template>

<script>
import EncabezadoSolicitudCompra from "../solicitud-compra/partials/Encabezado";
import {es} from "vuejs-datepicker/dist/locale";
import Datepicker from 'vuejs-datepicker';
export default {
    name: "comparativa-cotizacion-compra-show",
    components: {EncabezadoSolicitudCompra, Datepicker},
    props: ['id'],
    data(){
        return{
            mejor_cotizacion : '',
            cotizaciones_completas : false,
            cargando: false,
            es:es,
            cotizaciones : [],
            exclusiones : [],
            partidas : [],
            precios_menores : [],
            cantidad_partidas:'',
            solicitud : '',
            cantidad_cotizaciones : '',
            indices_partidas : [],
            proveedores : [],
            fecha_cierre : new Date(),
            fechasDeshabilitadas : {},
            post : {},
            mejor_anticipo : '',
            peor_anticipo : '',
            mejor_credito : '',
            peor_credito : '',
            mejor_plazo : '',
            peor_plazo : '',
            suma_mejor_opcion : '',
            cuerpo_correo : '<p><strong>Estimado(a) [%contacto%]</strong></p>\n' +
                '\n' +
                '<p>[%proveedor%]</p>' +
                '<p>Se le invita cordialmente a realizar una contraoferta de la cotización [%folio_cotizacion%] que nos hizo llegar previamente.</p>\n'+
                '<p>Se requiere que realice el registro de su contraoferta de cotizaci&oacute;n en el <a href="http://portal-aplicaciones.grupohi.mx/" target="_blank">portal de proveedores</a> a mas tardar el d&iacute;a [%fecha_cierre%].</p>'
        }
    },
    mounted() {
        this.find();
        this.fechasDeshabilitadas.to = new Date();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('compras/solicitud-compra/getComparativaCotizaciones', {
                id: this.id,
                params:{ cotizaciones_completas : this.cotizaciones_completas}
            }).then(data => {
                this.solicitud = data.solicitud
                this.cotizaciones = data.cotizaciones
                this.exclusiones = data.exclusiones
                this.partidas = data.partidas
                this.precios_menores = data.precios_menores
                this.cantidad_partidas = data.cantidad_partidas;
                this.cantidad_cotizaciones = data.cantidad_cotizaciones
                this.proveedores = data.proveedores
                this.mejor_cotizacion = data.mejor_cotizacion;
                this.mejor_anticipo = data.mejor_anticipo;
                this.peor_anticipo = data.peor_anticipo;
                this.mejor_credito = data.mejor_credito;
                this.peor_credito = data.peor_credito;
                this.mejor_plazo = data.mejor_plazo;
                this.peor_plazo = data.peor_plazo;
                this.suma_mejor_opcion = data.suma_mejor_opcion;

            }).finally(()=> {
                this.cargando = false;
            })
        },
        invitar(){
            this.$router.push({name: 'invitacion-compra-create', params: {id_solicitud: this.id}});
        },
        salir() {
            this.$router.push({name: 'comparativa-cotizacion-compra'});
        },
        pedirContraOferta() {
            $(this.$refs.modal_proveedores).modal('show');
        },
        cerrarModal()
        {
            $(this.$refs.modal_proveedores).modal('hide');
        },
        enviar()
        {
            let _self = this;
            this.$validator.validate().then(result => {
                if (result) {
                    _self.post.id_transaccion = _self.id;
                    _self.post.observaciones = ''
                    _self.post.fecha_cierre = _self.fecha_cierre;//
                    _self.post.cuerpo_correo = _self.cuerpo_correo;//
                    _self.post.destinatarios = _self.proveedores;
                    _self.post.usuarios = _self.usuarios;

                    return this.$store.dispatch('compras/invitacion/storeInvitacionContraOferta', _self.post)
                        .then((data) => {
                            $(this.$refs.modal_proveedores).modal('hide');
                            this.$router.push({name: 'invitacion-compra'});
                        });
                }
            });
        },
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
    },
    computed: {

    },
    watch: {
        cotizaciones_completas(value){
            setTimeout(() => {
                this.find();
            }, 800);

        },
    }
}
</script>

<style scoped>

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

table th.mejor_cotizacion,  table td.mejor_cotizacion {
    border: 1px solid #93ea84;
}

table thead th.no_negrita b.mejor_cotizacion, table td.mejor_cotizacion b.mejor_cotizacion
{
    color: #40c535;
}

table thead th.no_negrita  b.mejor_dato{
    color: #40c535;
}

table thead th.no_negrita  b.peor_dato{
    color: #F00;
}

table td.mejor_opcion {
    background-color: #93ea84;
}

hr.mejor_cotizacion
{
    border-color: #93ea84;
}

table thead th
{
    padding: 0.2em;

    background-color: #f2f4f5;
    font-weight: bold;
    color: #86888d;
    overflow: hidden;
    text-align: center;
}

table thead th.no_negrita
{
    font-weight: normal;
}

table thead th.no_negrita b
{
    color: black;
}
table td.sin_borde {
    border: none;
    padding: 2px 5px;
}

table td.titulo {
    color: #86888d;
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
    color: #86888d;
}



</style>
