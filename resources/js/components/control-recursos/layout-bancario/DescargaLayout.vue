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
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="idsemana" class="col-form-label">Año y semana:</label>
                            <select class="form-control"
                                    data-vv-as="Semana y año"
                                    id="idsemana"
                                    name="idsemana"
                                    :error="errors.has('idsemana')"
                                    v-validate="{required: true}"
                                    v-model="idsemana">
                                <option value>-- Selecionar --</option>
                                <option v-for="(s) in semanas" :value="s.id">Año: {{s.anio}}    Semana: {{ s.semana }}</option>
                            </select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('idsemana')">{{ errors.first('idsemana') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row" align="left" v-if="solicitudes">
                    <div  class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Serie</th>
                                    <th>Folio</th>
                                    <th>Fecha</th>
                                    <th>Empresa</th>
                                    <th style="min-width: 230px">Cuenta Empresa</th>
                                    <th>Proveedor</th>
                                    <th>Cuenta Proveedor</th>
                                    <th>Importe</th>
                                    <th>Concepto</th>
                                    <th> Seleccionar
                                        <input type="checkbox" v-model="seleccionar">
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(doc, i) in solicitudes">
                                    <td>{{i+1}}</td>
                                    <td>{{doc.serie}}</td>
                                    <td>{{doc.folio}}</td>
                                    <td>{{doc.fecha_format}}</td>
                                    <td>{{doc.empresa.razon_social}}</td>
                                    <td >
                                        <template
                                            v-if="doc.empresa.cuentas_pagadoras_santander && doc.empresa.cuentas_pagadoras_santander.data.length > 0">
                                             <select
                                                 v-if="doc.empresa.cuentas_pagadoras_santander.data.length > 1"
                                                 class="form-control"
                                                 :name="`idcuentaempresa[${i}]`"
                                                 v-model="doc.idcuentaempresa"
                                                 v-validate="{required: doc.selected == true ? true : false}"
                                                 data-vv-as="Cuenta empresa"
                                                 :class="{'is-invalid': errors.has(`idcuentaempresa[${i}]`)}"
                                             >

                                                 <option value >-- Seleccionar --</option>
                                                 <option v-for="cuenta in doc.empresa.cuentas_pagadoras_santander.data" :value="cuenta.id_cuenta">{{ cuenta.numero_cuenta }} ({{cuenta.banco_descripcion}})</option>
                                            </select>
                                            <input v-else
                                                   readonly="readonly"
                                                   class="form-control"
                                                   type="text"
                                                   v-model="doc.numero_cuenta_empresa"
                                            />

                                        </template>

                                        <span v-else style="color: red">
                                            Sin Cuenta Pagadora Registrada
                                        </span>
                                        <div class="invalid-feedback"
                                             v-show="errors.has(`idcuentaempresa[${i}]`)">{{ errors.first(`idcuentaempresa[${i}]`) }}
                                        </div>
                                    </td>
                                    <td>{{doc.proveedor.razon_social}}</td>
                                    <td v-if="doc.cuentaProveedor && doc.cuentaProveedor != null">{{doc.cuentaProveedor.numero_cuenta}} ({{doc.cuentaProveedor.banco_nombre}})</td>
                                    <td v-else>{{doc.id}}</td>
                                    <td>{{doc.importe_format}}</td>
                                    <td>{{doc.concepto}}</td>
                                    <td class="text-center"><input type="checkbox" :value="doc.id" v-model="doc.selected"></td>
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
                        <i class="fa fa-angle-left"></i>Regresar
                    </button>
                    <button type="button" class="btn btn-primary" @click="descargar" :disabled="solicitudes == null ? true : false">
                        <i class="fa fa-download"></i>Descargar
                    </button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "DescargaLayout",
    data() {
        return {
            cargando: false,
            semanas: [],
            idsemana: '',
            solicitudes: null,
            seleccionar: false
        }
    },
    mounted() {
        this.$validator.reset()
        this.getSemanas();
    },
    methods : {
        getSemanas() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/semana-anio/index', {
                params: { scope:'ordenarPorSemana' }
            })
                .then(data => {
                    this.semanas = data.data;
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
        salir()
        {
            this.$validator.reset()
            this.idsemana =  '';
            this.solicitudes = null;
            this.seleccionar = false;
        },
        descargar()
        {
            return this.$store.dispatch('controlRecursos/solicitud-cheque/descargar', {data: this.solicitudes, idsemana: this.idsemana})
                .then(() => {
                    this.salir();
                    this.$emit('success')
                })
        },
        getSolicitudes() {
            return this.$store.dispatch('controlRecursos/solicitud-cheque/index', {
                params: { scope:['porSemanaAnio:'+this.idsemana,'ordenaSerieFolio'], include : ['cuentaProveedor','empresa.cuentas_pagadoras_santander'] }
            })
            .then(data => {
                let _self = this;
                this.solicitudes = data.data;
                this.solicitudes.forEach(function(solicitud, i){
                    if(solicitud.empresa.cuentas_pagadoras_santander.data.length == 1)
                    {
                        _self.solicitudes[i].idcuentaempresa = solicitud.empresa.cuentas_pagadoras_santander.data[0].id_cuenta;
                        _self.solicitudes[i].numero_cuenta_empresa = solicitud.empresa.cuentas_pagadoras_santander.data[0].numero_cuenta +
                        ' ('+solicitud.empresa.cuentas_pagadoras_santander.data[0].banco_descripcion + ")";
                    }
                });
            })
        },
    },
    watch: {
        idsemana(value)
        {
            if(value)
            {
                this.getSolicitudes();
            }
        },
        seleccionar(value)
        {
           this.solicitudes.forEach(function (sol, i) {
               sol['selected'] = value;
           });
        }
    }
}
</script>

<style scoped>

</style>
