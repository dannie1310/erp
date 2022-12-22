<template>
    <span>
        <nav>
            <div class="card" v-if="registro.cargando">
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
                        <div class="col-md-2" v-if="registro.xml != undefined">
                            <label for="fecha_emision">Fecha de Emisión</label>
                            <h6>{{registro.fecha_emision}}</h6>
                        </div>
                        <div class="col-md-2" v-else>
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="fecha_emision" >Fecha de Emisión</label>
                                    <datepicker v-model = "registro.fecha_emision"
                                                name = "fecha_emision"
                                                v-on:keyup="getTipoCambio"
                                                :format = "formatoFecha"
                                                :language = "registro.es"
                                                :bootstrap-styling = "true"
                                                class = "form-control"
                                                :disabled-dates="registro.fechasDeshabilitadas"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('fecha_emision')}"
                                    ></datepicker>
                                    <div class="invalid-feedback" v-show="errors.has('fecha_emision')">{{ errors.first('fecha_emision') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" v-if="registro.xml != undefined">
                            <label for="numero_factura">Número de Factura:</label>
                            <h6>{{registro.numero_factura}}</h6>
                        </div>
                        <div class="col-md-2" v-else>
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="numero_factura">Número de Factura:</label>
                                    <input class="form-control"
                                           style="width: 100%"
                                           placeholder="Número de factura"
                                           name="numero_factura"
                                           id="numero_factura"
                                           data-vv-as="Número de Factura"
                                           v-validate="{required: true}"
                                           v-model="registro.numero_factura"
                                           :class="{'is-invalid': errors.has('numero_factura')}">
                                    <div class="invalid-feedback" v-show="errors.has('numero_factura')">{{ errors.first('numero_factura') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="id_proyecto">Proyecto:</label>
                                    <select
                                        type="text"
                                        name="id_proyecto"
                                        data-vv-as="Proyecto"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="id_proyecto"
                                        v-model="registro.id_proyecto"
                                        :class="{'is-invalid': errors.has('id_proyecto')}">
                                         <option value>--Seleccionar--</option>
                                        <option v-for="proyecto in registro.proyectos" :value="proyecto.id">{{ proyecto.nombre }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_proyecto')">{{ errors.first('id_proyecto') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3" v-if="registro.xml != undefined">
                            <label for="id_empresa">Empresa:</label>
                            <h6>{{registro.razon_social}} ({{registro.empresa_rfc}})</h6>
                        </div>
                        <div class="col-md-3" v-else>
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="id_empresa">Empresa:</label>
                                    <select
                                        type="text"
                                        name="id_empresa"
                                        data-vv-as="Empresa"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="id_empresa"
                                        v-model="registro.id_empresa"
                                        :class="{'is-invalid': errors.has('id_empresa')}">
                                         <option value>--Seleccionar--</option>
                                        <option v-for="empresa in registro.empresas" :value="empresa.id">{{ empresa.nombre }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="descripcion">Descripción:</label>
                                    <input class="form-control"
                                           style="width: 100%"
                                           placeholder="Descripción"
                                           name="descripcion"
                                           id="descripcion"
                                           data-vv-as="Descripción"
                                           v-validate="{required: true}"
                                           v-model="registro.descripcion"
                                           :class="{'is-invalid': errors.has('descripcion')}">
                                    <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3" v-if="registro.xml != undefined">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="id_cliente">Cliente:</label>
                                    <h6>{{registro.cliente_razon_social}} ({{registro.cliente_rfc}})</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3" v-else>
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="id_cliente">Cliente:</label>
                                    <select
                                        type="text"
                                        name="id_cliente"
                                        data-vv-as="Cliente"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="id_cliente"
                                        v-model="registro.id_cliente"
                                        :class="{'is-invalid': errors.has('id_cliente')}">
                                         <option value>--Seleccionar--</option>
                                        <option v-for="cliente in registro.clientes" :value="cliente.id">{{ cliente.nombre }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_cliente')">{{ errors.first('id_cliente') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="fecha_inicial" >Periodo que cubre:</label>
                                    <datepicker v-model = "registro.fecha_inicial"
                                                name = "fecha_inicial"
                                                :format = "formatoFecha"
                                                :language = "registro.es"
                                                :bootstrap-styling = "true"
                                                class = "form-control"
                                                :disabled-dates="registro.fechasDeshabilitadas"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('fecha_inicial')}"
                                    ></datepicker>
                                    <div class="invalid-feedback" v-show="errors.has('fecha_inicial')">{{ errors.first('fecha_inicial') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="fecha_fin" >al</label>
                                    <datepicker v-model = "registro.fecha_fin"
                                                name = "fecha_fin"
                                                :format = "formatoFecha"
                                                :language = "registro.es"
                                                :bootstrap-styling = "true"
                                                class = "form-control"
                                                :disabled-dates="registro.fechasDeshabilitadas"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('fecha_fin')}"
                                    ></datepicker>
                                    <div class="invalid-feedback" v-show="errors.has('fecha_fin')">{{ errors.first('fecha_fin') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="id_moneda">Moneda:</label>
                                    <select
                                        type="text"
                                        :disabled="registro.xml != undefined ? true : false"
                                        name="id_moneda"
                                        data-vv-as="Moneda"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="id_moneda"
                                        v-model="registro.id_moneda"
                                        :class="{'is-invalid': errors.has('id_moneda')}">
                                         <option value>--Seleccionar--</option>
                                        <option v-for="moneda in registro.monedas" :value="moneda.id">{{ moneda.nombre }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_moneda')">{{ errors.first('id_moneda') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="tipo_cambio">Tipo de Cambio:</label>
                                    <input class="form-control"
                                           :disabled="registro.xml != undefined ? true : false"
                                           style="width: 100%"
                                           placeholder="Tipo de Cambio"
                                           name="tipo_cambio"
                                           id="tipo_cambio"
                                           data-vv-as="Tipo de Cambio"
                                           v-validate="{required: true}"
                                           v-model="registro.tipo_cambio"
                                           :class="{'is-invalid': errors.has('tipo_cambio')}">
                                    <div class="invalid-feedback" v-show="errors.has('tipo_cambio')">{{ errors.first('tipo_cambio') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <concepto-create @created="getConceptos" />
                                <button  v-if="registro.xml == undefined" type="button" class="btn btn-success btn-sm" @click="agregarConcepto()"><i class="fa fa-plus"></i>Agregar Concepto</button>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row" v-if="registro.xml == undefined">
                        <div  class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th>Concepto</th>
                                        <th>Importe</th>
                                        <th class="index_corto"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(concepto, i) in registro.conceptos">
                                            <td>{{i+1}}</td>
                                            <td>
                                                <select
                                                    type="text"
                                                    :name="`concepto[${i}]`"
                                                    data-vv-as="Concepto"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    :id="`concepto[${i}]`"
                                                    v-model="concepto.idconcepto"
                                                    :class="{'is-invalid': errors.has(`concepto[${i}]`)}">
                                                    <option value>--Seleccionar--</option>
                                                    <option v-for="concept in registro.tipoConceptos" :value="concept.id">{{ concept.nombre }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-show="errors.has(`concepto[${i}]`)">{{ errors.first(`concepto[${i}]`) }}</div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       :name="`importe[${i}]`"
                                                       data-vv-as="Importe"
                                                       v-on:keyup="importeTotalConceptos"
                                                       v-model="concepto.importe"
                                                       style="text-align: right"
                                                       v-validate="{required: true, regex: /^[0-9]\d*(\.\d{0,2})?$/, min: 0.01, decimal:2}"
                                                       :class="{'is-invalid': errors.has(`importe[${i}]`)}"
                                                       :id="`importe[${i}]`">
                                                <div class="invalid-feedback" v-show="errors.has(`importe[${i}]`)">{{ errors.first(`importe[${i}]`) }}</div>
                                            </td>
                                            <td class="icono">
                                                <button @click="eliminarConcepto(i)" :disabled="registro.conceptos.length === 1" type="button" class="btn btn-sm btn-outline-danger pull-left" title="Eliminar">
                                                    <i class="fa fa-spin fa-spinner" v-if="registro.cargando"></i>
                                                    <i class="fa fa-trash" v-else></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-else>
                        <div  class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th>Concepto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Descuento</th>
                                        <th>Importe</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(concepto, i) in registro.conceptos">
                                            <td>{{i+1}}</td>
                                            <td>
                                                <select
                                                    type="text"
                                                    :name="`concepto[${i}]`"
                                                    data-vv-as="Concepto"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    :id="`concepto[${i}]`"
                                                    v-model="concepto.idconcepto"
                                                    :class="{'is-invalid': errors.has(`concepto[${i}]`)}">
                                                    <option value>--Seleccionar--</option>
                                                    <option v-for="concept in registro.tipoConceptos" :value="concept.id">{{ concept.nombre }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-show="errors.has(`concepto[${i}]`)">{{ errors.first(`concepto[${i}]`) }}</div>
                                            </td>
                                            <td style="text-align: right">
                                                {{parseFloat(concepto.cantidad).formatMoney(2,'.',',')}}
                                            </td>
                                            <td style="text-align: right">
                                                {{parseFloat(concepto.valor_unitario).formatMoney(2,'.',',')}}
                                            </td>
                                            <td style="text-align: right">
                                                {{parseFloat(concepto.descuento).formatMoney(2,'.',',')}}
                                            </td>
                                            <td style="text-align: right">
                                               {{parseFloat(concepto.importe).formatMoney(2,'.',',')}}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="registro.xml == undefined">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th style="width:50%">Importe:</th>
                                            <td style="text-align: right">{{parseFloat(registro.importe_conceptos).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" >
                        <div class="col-md-12">
                            <div class="pull-right">
                                <descuento-create @created="getPartidas" />
                                <button v-if="registro.xml == undefined" type="button" class="btn btn-success btn-sm" @click="agregarPartida()"><i class="fa fa-plus"></i>Agregar Partida</button>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row" v-if="registro.xml == undefined">
                        <br />
                        <div  class="col-12" v-if="registro.partidas.length != 0">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th>Partida</th>
                                        <th></th>
                                        <th class="c150">Importe</th>
                                        <th class="index_corto"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(partida, i) in registro.partidas">
                                            <td>{{i+1}}</td>
                                            <td>
                                                <select
                                                    type="text"
                                                    :name="`partida[${i}]`"
                                                    data-vv-as="Partida"
                                                    v-validate="{required: true}"
                                                    v-on:change="getPartida(i)"
                                                    class="form-control"
                                                    :id="`partida[${i}]`"
                                                    v-model="partida.idpartida"
                                                    :class="{'is-invalid': errors.has(`partida[${i}]`)}">
                                                    <option value>--Seleccionar--</option>
                                                    <option v-for="par in registro.tipos_partida" :value="par.id">{{ par.partida }} ({{par.nombre_operador}})</option>
                                                </select>
                                                <div class="invalid-feedback" v-show="errors.has(`partida[${i}]`)">{{ errors.first(`partida[${i}]`) }}</div>
                                            </td>
                                            <td>
                                                 <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" :id="partida.antes_iva" v-model="partida.antes_iva" :disabled="partida.idpartida == ''" v-on:change="importeTotalPartidas" v-on:click="importeTotalPartidas">
                                                    <label class="form-check-label" for="antes_iva">¿Antes de IVA?</label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       :name="`importe_partida[${i}]`"
                                                       data-vv-as="Importe"
                                                       v-on:keyup="importeTotalPartidas"
                                                       v-model="partida.total"
                                                       style="text-align: right"
                                                       v-validate="{required: true, regex: /^[0-9]\d*(\.\d{0,2})?$/, min: 0.01, decimal:2}"
                                                       :class="{'is-invalid': errors.has(`importe_partida[${i}]`)}"
                                                       :id="`importe_partida[${i}]`">
                                                <div class="invalid-feedback" v-show="errors.has(`importe_partida[${i}]`)">{{ errors.first(`importe_partida[${i}]`) }}</div>
                                            </td>
                                            <td class="icono">
                                                <button @click="eliminarPartida(i)" type="button" class="btn btn-sm btn-outline-danger pull-left" title="Eliminar">
                                                    <i class="fa fa-spin fa-spinner" v-if="registro.cargando"></i>
                                                    <i class="fa fa-trash" v-else></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-7" v-if="registro.xml != undefined">
                            <label for="archivo_pdf" class="col-form-label">Adjuntar PDF: </label>
                            <input type="file" class="form-control" id="archivo_pdf"
                                   @change="onFileChange"
                                   row="3"
                                   v-validate="{ ext: ['pdf']}"
                                   name="archivo_pdf"
                                   data-vv-as="Soporte"
                                   ref="archivo_pdf"
                                   :class="{'is-invalid': errors.has('archivo_pdf')}">
                            <div class="invalid-feedback" v-show="errors.has('archivo_pdf')">{{ errors.first('archivo_pdf') }} (pdf)</div>
                        </div>
                        <div class="col-md-7" v-else></div>
                        <div class="col-md-5">
                            <div class="table-responsive">
                                <table class="table table-sm" v-if="registro.xml == undefined">
                                    <tbody>
                                        <tr>
                                            <th style="width:50%">Subtotal:</th>
                                            <td style="text-align: right">{{parseFloat(registro.importe_conceptos).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Descuentos Antes:</th>
                                            <td style="text-align: right">{{parseFloat(Math.abs(registro.importe_partidas_antes_suma - registro.importe_partidas_antes_resta)).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                        <tr>
                                            <th>IVA</th>
                                            <td style="text-align: right">{{parseFloat(registro.iva).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Descuentos despues:</th>
                                            <td style="text-align: right">{{parseFloat(Math.abs(registro.importe_partidas_despues_suma - registro.importe_partidas_despues_resta)).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                        <tr>
                                            <th>Total:</th>
                                            <td style="text-align: right">{{parseFloat(registro.total).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-sm" v-else>
                                    <tbody>
                                        <tr>
                                            <th style="width:50%">Subtotal:</th>
                                            <td style="text-align: right" colspan="2">{{parseFloat(registro.importe_conceptos).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                        <tr v-for="(retencion, i) in registro.retencionesLocales" v-if="retencion.antes_iva == true">
                                            <th style="width:50%">
                                                <select
                                                    type="text"
                                                    :name="`retencion[${i}]`"
                                                    data-vv-as="Descuento"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    :id="`retencion[${i}]`"
                                                    v-model="retencion.id"
                                                    :class="{'is-invalid': errors.has(`retencion[${i}]`)}">
                                                    <option value>--Seleccionar--</option>
                                                    <option v-for="partida in registro.tipos_partida" :value="partida.id">{{ partida.partida }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-show="errors.has(`retencion[${i}]`)">{{ errors.first(`retencion[${i}]`) }}</div>
                                                 <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" :id="retencion.antes_iva" v-model="retencion.antes_iva" :disabled="retencion.id == ''" v-on:change="importeTotalPartidas" v-on:click="importeTotalPartidas">
                                                    <label class="form-check-label" for="antes_iva">¿Antes de IVA?</label>
                                                </div>
                                            </th>
                                            <td style="text-align: right">{{parseFloat(retencion.total).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                        <tr v-if="registro.xml != undefined" >
                                            <th style="width:50%">Descuentos:</th>
                                            <td style="text-align: right">{{parseFloat(registro.descuento).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                        <tr>
                                            <th>IVA</th>
                                            <td style="text-align: right">{{parseFloat(registro.iva).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                        <tr v-for="(retencion, i) in registro.retencionesLocales" v-if="retencion.antes_iva == false">
                                            <th style="width:50%">
                                                <select
                                                    type="text"
                                                    :name="`retencion[${i}]`"
                                                    data-vv-as="Descuento"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    :id="`retencion[${i}]`"
                                                    v-model="retencion.id"
                                                    :class="{'is-invalid': errors.has(`retencion[${i}]`)}">
                                                    <option value>--Seleccionar--</option>
                                                    <option v-for="partida in registro.tipos_partida" :value="partida.id">{{ partida.partida }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-show="errors.has(`retencion[${i}]`)">{{ errors.first(`retencion[${i}]`) }}</div>
                                                 <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" :id="retencion.antes_iva" v-model="retencion.antes_iva" :disabled="retencion.id == ''" v-on:change="importeTotalPartidas" v-on:click="importeTotalPartidas">
                                                    <label class="form-check-label" for="antes_iva">¿Antes de IVA?</label>
                                                </div>
                                            </th>
                                            <td style="text-align: right">{{parseFloat(retencion.total).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                        <tr>
                                            <th>Total:</th>
                                            <td style="text-align: right">{{parseFloat(registro.total).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" v-on:click="salir">
                            <i class="fa fa-angle-left"></i>
                            Regresar</button>
                        <button type="button" @click="validate" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import ConceptoCreate from "./partials/ConceptoCreate";
    import DescuentoCreate from "./partials/DescuentoCreate";
    export default {
        name: "create",
        components: {Datepicker, es, ConceptoCreate, DescuentoCreate},
        props: ['datos'],
        data() {
            return {
                registro : {
                    es: es,
                    cargando: false,
                    fecha_emision: '',
                    fechasDeshabilitadas: {},
                    numero_factura: '',
                    descripcion: '',
                    proyectos: {},
                    id_proyecto: '',
                    empresas: {},
                    id_empresa: '',
                    clientes: {},
                    id_cliente: '',
                    fecha_inicial: '',
                    fecha_fin: '',
                    monedas: {},
                    id_moneda: '',
                    tipo_cambio: '',
                    tipoConceptos: {},
                    conceptos: [],
                    importe_conceptos: 0,
                    subtotal: 0,
                    iva: 0,
                    total: 0,
                    partidas: [],
                    importe_partidas_antes_suma: 0,
                    importe_partidas_antes_resta: 0,
                    importe_partidas_despues_suma: 0,
                    importe_partidas_despues_resta: 0,
                    tipos_partida: {},
                    tipos_cambios: [],
                    archivo: null,
                    archivo_name: null,
                    archivo_pdf: null,
                    archivo_pdf_name: null,
                }
            }
        },
        mounted() {
            if(this.datos)
            {
                this.registro = this.datos
                this.registro.es = es
                this.registro.fechasDeshabilitadas = {};
                this.registro.fecha_inicial = '';
                this.registro.fecha_fin = '';
                this.registro.importe_conceptos = 0
                this.registro.importe_partidas_antes_suma = 0
                this.registro.importe_partidas_antes_resta = 0
                this.registro.importe_partidas_despues_suma = 0
                this.registro.importe_partidas_despues_resta = 0
                this.registro.descripcion = ''
                this.importeTotalConceptos();
            }else {
                this.registro.fecha_emision = new Date()
                this.registro.conceptos.push({
                    idconcepto: '',
                    importe: ''
                });
                this.getClientes();
                this.getConceptos();
                this.getEmpresas();
                this.getMonedas();
                this.getTipoCambio();
            }
            this.getProyectos();
            this.registro.fechasDeshabilitadas.from = new Date();
            this.registro.fecha_inicial = new Date();
            this.registro.fecha_fin = new Date();
            this.$validator.reset();
        },

        methods: {
            formatoFecha(date) {
                return moment(date).format('DD/MM/YYYY');
            },
            agregarConcepto(){
                let temp_index = this.registro.conceptos.length;
                this.registro.conceptos.splice(temp_index, 0, {
                    idconcepto : '',
                    importe : ''
                });
            },
            eliminarConcepto(index){
                let temp_index = index - 1;
                while(temp_index in this.registro.conceptos){
                    temp_index= temp_index - 1;
                }
                this.registro.conceptos.splice(index, 1);
                this.importeTotalConceptos()
            },
            importeTotalConceptos() {
                let importe = 0;
                for(let i=0; i < this.registro.conceptos.length; i++) {
                    importe += parseFloat(this.registro.conceptos[i].importe);
                }
                this.registro.importe_conceptos = parseFloat(importe).formatMoney(2,'.','')
                this.totales();
            },
            totales(){
                if(this.registro.xml == undefined)
                {
                    this.registro.subtotal = this.registro.importe_conceptos
                    this.registro.subtotal = this.registro.subtotal + this.registro.importe_partidas_antes_suma;
                    this.registro.subtotal = this.registro.subtotal - this.registro.importe_partidas_antes_resta;
                    this.registro.iva = this.registro.subtotal * 0.16;
                    this.registro.total = (this.registro.subtotal + this.registro.iva)
                    this.registro.total = this.registro.total + this.registro.importe_partidas_despues_suma;
                    this.registro.total = this.registro.total - this.registro.importe_partidas_despues_resta;
                }
            },
            agregarPartida(){
                this.getPartidas()
                let temp_index = this.registro.partidas.length;
                this.registro.partidas.splice(temp_index, 0, {
                    idpartida : '',
                    antes_iva : false,
                    total : ''
                });
                this.importeTotalPartidas();
            },
            eliminarPartida(index){
                let temp_index = index - 1;
                while(temp_index in this.registro.partidas){
                    temp_index= temp_index - 1;
                }
                this.registro.partidas.splice(index, 1);
                this.importeTotalPartidas()
            },
            importeTotalPartidas() {
                if(this.registro.xml == undefined) {
                    let importe_despues_resta = 0;
                    let importe_despues_suma = 0;
                    let importe_antes_resta = 0;
                    let importe_antes_suma = 0;

                    for (let i = 0; i < this.registro.partidas.length; i++) {
                        if (this.registro.partidas[i].nombre_operador != undefined && this.registro.partidas[i].total != '') {
                            if (this.registro.partidas[i].nombre_operador == 'MENOS') {
                                if (this.registro.partidas[i].antes_iva) {
                                    importe_antes_resta += parseFloat(this.registro.partidas[i].total);
                                } else {
                                    importe_despues_resta += parseFloat(this.registro.partidas[i].total);
                                }
                            }
                            if (this.registro.partidas[i].nombre_operador == 'MAS') {
                                if (this.registro.partidas[i].antes_iva) {
                                    importe_antes_suma += parseFloat(this.registro.partidas[i].total);
                                } else {
                                    importe_despues_suma += parseFloat(this.registro.partidas[i].total);
                                }
                            }
                        }
                    }
                    this.registro.importe_partidas_antes_suma = importe_antes_suma;
                    this.registro.importe_partidas_antes_resta = importe_antes_resta;
                    this.registro.importe_partidas_despues_suma = importe_despues_suma;
                    this.registro.importe_partidas_despues_resta = importe_despues_resta;
                    this.totales();
                }
            },
            getClientes() {
                this.registro.cargando = true;
                return this.$store.dispatch('seguimiento/cliente/index', {
                    params: {
                        scope: ['activos'],
                        order: 'ASC',
                        sort: 'cliente'
                    }
                })
                    .then(data => {
                        this.registro.clientes = data.data;
                    })
            },
            getConceptos() {
                return this.$store.dispatch('seguimiento/tipo-ingreso/index', {
                    params: {
                        scope: ['activos'],
                        order: 'ASC',
                        sort: 'tipo_ingreso'
                    }
                })
                    .then(data => {
                        this.registro.tipoConceptos = data.data;
                    })
            },
            getEmpresas() {
                this.registro.cargando = true;
                return this.$store.dispatch('seguimiento/empresa/index', {
                    params: {
                        scope: ['activos'],
                        order: 'ASC',
                        sort: 'empresa'
                    }
                })
                    .then(data => {
                        this.registro.empresas = data.data;
                    })
            },
            getMonedas() {
                this.registro.cargando = true;
                return this.$store.dispatch('seguimiento/moneda/index', {
                    params: {
                        order: 'ASC',
                        sort: 'orden'
                    }
                })
                    .then(data => {
                        this.registro.monedas = data.data;
                        this.registro.id_moneda = 3
                        this.registro.tipo_cambio = 1.00
                    })
            },
            getProyectos() {
                this.registro.proyectos = {};
                return this.$store.dispatch('seguimiento/proyecto/index', {
                    params: {
                        scope: ['porTipo'],
                        order: 'ASC',
                        sort: 'proyecto'
                    }
                }).then(data => {
                    this.registro.proyectos = data;
                }).finally(()=>{
                    this.registro.cargando = false;
                })
            },
            getPartidas() {
                return this.$store.dispatch('seguimiento/ingreso-partida/index', {
                    params: {
                        scope: ['activos'],
                        order: 'ASC',
                        sort: 'partida'
                    }
                })
                    .then(data => {
                        this.registro.tipos_partida = data.data;
                    })
            },
            getPartida(i) {
                 return this.$store.dispatch('seguimiento/ingreso-partida/find', {
                    id: this.registro.partidas[i]['idpartida'],
                    params: {}
                }).then(data => {
                     this.registro.partidas[i]['nombre_operador'] = data['nombre_operador'];
                     this.importeTotalPartidas();
                })
            },

            getTipoCambio() {
                return this.$store.dispatch('igh/tipo-cambio/index', {
                    params: {
                        scope: ['porFecha:'+moment(this.registro.fecha).format('YYYY-MM-DD')]
                    }
                })
                    .then(data => {
                        this.registro.tipos_cambios = data;
                    }).finally(()=>{
                        this.registro.cargando = false;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(moment(this.registro.fecha_fin).format('YYYY/MM/DD') < moment(this.registro.fecha_inicial).format('YYYY/MM/DD'))
                        {
                            swal('¡Error!', 'El periodo de inicio no puede ser posterior a la fecha de término.', 'error')
                        }
                        else {
                            this.store()
                        }
                        this.$validator.errors.clear();
                    }
                });
            },
            store() {
                return this.$store.dispatch('seguimiento/factura/store', {
                    fi_cubre: moment(this.registro.fecha_inicial).format('YYYY-MM-DD'),
                    ff_cubre:  moment(this.registro.fecha_fin).format('YYYY-MM-DD'),
                    conceptos: this.registro.conceptos,
                    fecha:  moment(this.registro.fecha_emision).format('YYYY-MM-DD'),
                    numero : this.registro.numero_factura,
                    descripcion : this.registro.descripcion,
                    idproyecto : this.registro.id_proyecto,
                    idempresa : this.registro.id_empresa,
                    idcliente : this.registro.id_cliente,
                    idmoneda : this.registro.id_moneda,
                    tipo_cambio : this.registro.tipo_cambio,
                    importe_conceptos : this.registro.importe_conceptos,
                    importe : this.registro.subtotal,
                    iva : this.registro.iva,
                    total : this.registro.total,
                    partidas : this.registro.partidas,
                    importe_partidas_antes : this.registro.importe_partidas_antes,
                    importe_partidas_despues : this.registro.importe_partidas_despues,
                    xml : this.registro.xml ? this.registro.xml : '',
                    certificado : this.registro.xml ? this.registro.certificado : '',
                    descuento : this.registro.xml ? this.registro.descuento : '',
                    no_certificado : this.registro.xml ? this.registro.no_certificado : '',
                    sello : this.registro.xml ? this.registro.sello : '',
                    tipo_comprobante : this.registro.xml ? this.registro.tipo_comprobante : '',
                    version : this.registro.xml ? this.registro.version : '',
                    nombre_archivo : this.registro.xml ? this.registro.nombre_archivo : '',
                    uuid : this.registro.xml ? this.registro.uuid : '',
                    cfdi_relacionado : this.registro.xml ? this.registro.cfdi_relacionado : '',
                    tipo_relacion : this.registro.xml ? this.registro.tipo_relacion : '',
                    serie : this.registro.xml ? this.registro.serie : '',
                    folio : this.registro.xml ? this.registro.folio : '',
                    nombre_archivo_pdf : this.registro.xml ? this.registro.archivo_pdf_nombre : '',
                    archivo_pdf : this.registro.xml ? this.registro.archivo_pdf : ''
                }).then(data=> {
                    this.salir();
                })
            },
            salir()
            {
                this.$router.go(-1);
            },
            onFileChange(e){
                this.registro.archivo_pdf = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.registro.archivo_pdf_nombre = files[0].name;
                if(e.target.id == 'archivo_pdf') {
                    this.createImage(files[0]);
                }
            },
            createImage(file) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.registro.archivo_pdf = e.target.result;
                };
                reader.readAsDataURL(file);

            },
        },
        watch: {
            id_proyecto(value) {
                if(value !== '' && value !== null && value !== undefined){
                    if(value == 0)
                    {
                        swal('¡Error!', 'Seleccione un proyecto válido.', 'error');
                    }
                }
            },
            id_moneda(value) {
                if(value !== '' && value !== null && value !== undefined){
                    if(value != 3 && value != 4) {
                        for (let i = 0; i < this.registro.tipos_cambios.length; i++)
                        {
                            if (value == this.registro.tipos_cambios[i]['moneda']) {
                                this.registro.tipo_cambio = this.registro.tipos_cambios[i]['tipo_cambio'];
                            }
                        }
                    }
                    if(value == 4)
                    {
                        this.registro.tipo_cambio = 0;
                    }
                }
            },
        }
    }
</script>

<style scoped>

</style>
