<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <form role="form" @submit.prevent="validate">
                            <div class="card-body">
                                <div class="row justify-content-between">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="id_invitacion">Buscar Invitación:</label>
                                                 <model-list-select
                                                     id="id_invitacion"
                                                     name="id_invitacion"
                                                     option-value="id"
                                                     v-model="id_invitacion"
                                                     :custom-text="idFolioObservaciones"
                                                     :list="invitaciones"
                                                     :placeholder="!cargando?'Seleccionar o buscar por folio':'Cargando...'">
                                                 </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_invitacion')">{{ errors.first('id_invitacion') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" v-if="id_invitacion>0 && !invitacion">
                                    <div class="col-md-12">
                                        <div class="spinner-border text-success" role="status">
                                           <span class="sr-only">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                                <DatosInvitacion v-else v-bind:invitacion="invitacion"></DatosInvitacion>
                            </div>
                             <div class="card-footer">
                                 <div class="row">
                                     <div class="col-md-6">
                                         <documentos-invitacion v-if="invitacion" v-bind:invitacion="invitacion"></documentos-invitacion>
                                     </div>
                                     <div class="col-md-6">
                                         <div class="pull-right">
                                             <button type="button" class="btn btn-secondary" v-on:click="salir">
                                                <i class="fa fa-angle-left"></i>
                                                Regresar</button>
                                            <button type="submit" :disabled="invitacion === null" class="btn btn-primary">
                                                Continuar
                                                <i class="fa fa-angle-right"></i>
                                            </button>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import DatosInvitacion from "../invitacion/partials/DatosInvitacion";
    import DocumentosInvitacion from "../invitacion/partials/DocumentosInvitacion";
    export default {
        name: "cotizacion-proveedor-seleccionar-solicitud",
        components: {
            DocumentosInvitacion,
            DatosInvitacion,
            ModelListSelect},
        data() {
            return {
                cargando: false,
                id_invitacion: '',
                invitaciones : [],
                invitacion : null,
            }
        },
        mounted() {
            this.$store.commit('padronProveedores/invitacion/SET_INVITACION', null);
            this.$validator.reset();
            this.getInvitaciones();
        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[Invitación ${item.numero_folio_format}]-[ ${item.transaccion.tipo_str} ${item.transaccion.numero_folio_format} ]`;
            },
            salir()
            {
                this.$router.push({name: 'cotizacion-proveedor'});
            },
            find() {
                this.cargando = true;
                this.$store.commit('padronProveedores/invitacion/SET_INVITACION', null);
                return this.$store.dispatch('padronProveedores/invitacion/find', {
                    id: this.id_invitacion,
                    params:{include: ["carta_terminos", "formato_cotizacion"]}
                }).then(data => {
                    this.invitacion = data;
                    this.cargando = false;
                })
            },
            getInvitaciones(){
                this.invitaciones = [];
                this.cargando = true;

                return this.$store.dispatch('padronProveedores/invitacion/index', {
                    params:{ include: ['transaccion'], scope: ['invitadoAutenticado'], sort: 'id', order: 'desc'}

                })
                    .then(data => {
                        this.invitaciones = data;
                    })
                    .finally(()=>{
                        this.cargando = false;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        return this.$store.dispatch('padronProveedores/invitacion/abrir', {
                            id: this.id_invitacion,
                            params:{}
                        }).then(data => {
                            if(this.invitacion.con_cotizacion){
                                this.$router.push({name: 'cotizacion-proveedor-edit', params: {id_invitacion: this.id_invitacion}});
                            }else{
                                console.log(this.invitacion.tipo_antecedente)
                                if(this.invitacion.tipo_antecedente == 49) {
                                    this.$router.push({
                                        name: 'presupuesto-proveedor-create',
                                        params: {id_invitacion: this.id_invitacion}
                                    });
                                }
                                if(this.invitacion.tipo_antecedente == 17) {
                                    this.$router.push({
                                        name: 'cotizacion-proveedor-create',
                                        params: {id_invitacion: this.id_invitacion}
                                    });
                                }

                            }
                        });
                    }
                });
            },
        },
        watch: {
            id_invitacion(value)
            {
                if(value !== '' && value !== null && value !== undefined)
                {
                    this.find();
                }
            },
        }
    }
</script>

<style scoped>

</style>
