<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row">
                      <div class="col-md-2">
                        <label>Empresa EFOS: </label>
                    </div>
                    <div class="col-md-10">
                        <model-list-select
                            :disabled="!bandera"
                            name="id_efos"
                            placeholder="Seleccionar o buscar por razón social o rfc de empresa"
                            data-vv-as="Empresa EFOS"
                            v-model="id_efos"
                            option-value="id"
                            :custom-text="razonSocialRfc"
                            :list="efos"
                        />
                    </div>
                </div>
                <div class="row col-md-12" v-if="sin_cfd">
                    <label><br>NO HAY CFD</label>
                </div>
            </div>
        </div>
        <div class="card" v-if="cargando">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                     </div>
                 </div>
            </div>
        </div>
        <div class="card"  v-if="cfd.length != 0 && cargando == false">
            <div class="card-body table-responsive">
                <div class="row">
                    <div class="col-md-6">
                        <label>CFD</label>
                    </div>
                    <div class="col-md-3" align="right">
					    <label>Total: {{parseFloat(total_cfd).formatMoney(2,'.',',')}}</label>
                    </div>
                    <div class="col-md-3" align="right">
					    <label>Total Seleccionado: {{parseFloat(sumaSeleccionTotales).formatMoney(2,'.',',')}}</label>
                    </div>
                </div>
                <div  class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="index_corto">#</th>
                            <th class="fecha">Serie</th>
                            <th class="fecha">Folio</th>
                            <th style="width: 300px;">UUID</th>
                            <th class="fecha_hora">RFC de Receptor</th>
                            <th>Razón Social de Receptor</th>
                            <th class="fecha_hora">Fecha</th>
                            <th class="money_input">Total</th>
                            <th class="index_corto"></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(p, i) in cfd">
                                <td>{{i+1}}</td>
                                <td>{{p.serie}}</td>
                                <td>{{p.folio}}</td>
                                <td>{{p.uuid}}</td>
                                <td>{{p.rfc_receptor}}</td>
                                <td>{{p.empresa.razon_social}}</td>
                                <td>{{p.fecha_format}}</td>
                                <td class="money">{{p.total_format}}</td>
                                <td><input type="checkbox" :value="p.id" v-model="p.selected" checked></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row" align="right">
             <div class="col-md-12">
                 <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                 <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 || cfd.length == 0" @click="validate">Registrar</button>
             </div>
        </div>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "no-deducidos-cfd-efos-create",
        components: {ModelListSelect},
        data() {
            return {
                cargando : false,
                efos : [],
                efo : [],
                cfd : [],
                id_efos : '',
                bandera : 1,
                total_cfd : 0,
                total_selecionado : 0,
                sin_cfd : false
            }
        },
        mounted() {
            this.getEfos();
        },
        computed: {
            sumaSeleccionTotales()
            {
                let total_selecionado = 0;
                this.cfd.forEach(function (doc, i) {
                    if(typeof doc.selected === 'undefined' || doc.selected === true) {
                        total_selecionado += parseFloat(doc.total);
                    }
                })
                return this.total_selecionado = total_selecionado
            },
        },
        methods: {
            razonSocialRfc (item) {
                return `[${item.rfc}] - [${item.razon_social}] `
            },
            getEfos() {
                return this.$store.dispatch('fiscal/efos/index', {
                    params: {include: ['proveedor'], scope: ['definitivo'], sort: 'razon_social', order: 'asc'}
                }).then(data => {
                    this.efos = data.data;
                    this.bandera = 1;
                })
            },
            getCFD()
            {
                this.cargando =  true;
                return this.$store.dispatch('fiscal/cfd-sat/index', {
                    params: {include: ['empresa', 'proveedor'], scope: ['definitivo','porProveedor:' + this.efo.proveedor.id,'exceptoTipo:P']}
                }).then(data => {
                    this.cfd = data.data;
                    this.cargando = false;
                    if(this.cfd.length == 0)
                    {
                        this.sin_cfd = true;
                    }
                })
            },
            store() {
                return this.$store.dispatch('fiscal/no-deducido/store',  {
                    'efo' : this.efo,
                    'cfd' : this.cfd
                })
                    .then((data) => {
                       this.salir();
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            salir(){
                return this.$router.push({name: 'no-deducidos-cfd-efos'});
            }
        },
        watch: {
            id_efos(value) {
                if (value != '') {
                    this.efos.map(efo => {
                        if (efo.id === value) {
                            this.sin_cfd = false;
                            this.efo = efo
                            this.getCFD()
                        }
                    });
                }
            },
            cfd(value)
            {
                this.total_cfd = 0;
                if(value != '')
                {
                    this.cfd.map(c => {
                        this.total_cfd += c.total
                    });
                    this.total_selecionado = this.total_cfd;
                }
            }
        }
    }
</script>

<style scoped>

</style>
