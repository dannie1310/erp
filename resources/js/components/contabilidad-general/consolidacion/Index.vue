<template>
    <span>
        <div class="card" id="subcontratantes">
            <div class="card-header">
                <h6 class="card-title">Asignación de Empresa Consolidadora</h6>
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label for="id_empresa" class="col-lg-2 col-form-label">Seleccione Empresa Consolidadora</label>
                        <div class="col-md-7">
                             <model-list-select
                                    name="id_empresa"
                                    placeholder="Seleccionar o buscar por Alias y Razón Social de la Empresa"
                                    data-vv-as="Empresa"
                                    option-value="id"
                                    :custom-text="aliasbddnombre"
                                    v-model="id_empresa"
                                    id="id_empresa"
                                    :list="empresas">
                            </model-list-select>
                        </div>
                </div>

                <div class="row" v-if="id_empresa">
                    <div class="col-sm-9">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="from">EMPRESAS QUE CONSOLIDA</label>
                                    <select multiple id="from" size="10" class="form-control" v-model="mover" :disabled="cargando">
                                        <option v-for="asignado in asignados" :value="asignado.id">{{ asignado.nombre }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="container col-sm-2">
                                <div class="vertical-center align-content-center">
                                    <button class="btn col-xs-12 btn-default" @click="agregar" title="Agregar"><i class="fa fa-long-arrow-left"></i></button>
                                    <button class="btn col-xs-12 btn-default" @click="quitar" title="Quitar"><i class="fa fa-long-arrow-right"></i></button>
                                </div>
                            </div>

                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="to">EMPRESAS DISPONIBLES</label>
                                    <select multiple id="to" size="10" class="form-control" v-model="selected">
                                        <option v-for="disponible in disponibles_ordered" :value="disponible.id">{{ disponible.nombre }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                <button class="btn btn-outline-success float-right" :disabled="!desasignados.length && !nuevos_asignados.length" @click="validate"><i class="fa fa-save"></i></button>
                </div>
            </div>

        </div>
        <div class="modal" ref="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalle de Asignación de Empresa Consolidadora</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Empresa Consolidadora:</th>
                                    <td>{{ nombre }}</td>
                                </tr>
                                 <tr  v-if="nuevos_asignados.length">
                                    <th>Areas Subcontratantes a Asignar:</th>
                                    <td>
                                        <ul>
                                            <li v-for="nuevo in nuevos_asignados">{{ nuevo.nombre }}</li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr v-if="desasignados.length">
                                    <th>Areas Subcontratantes a Desasignar:</th>
                                    <td>
                                        <ul>
                                            <li v-for="desa in desasignados">{{ desa.nombre }}</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" @click="asignar" :disabled="guardando">
                            <span v-if="guardando">
                                <i class="fa fa-spin fa-spinner"></i>
                            </span>
                            <span v-else>
                                <i class="fa fa-save"></i> Aplicar
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </span>

</template>

<script>
    // import UsuarioSelect from "../../igh/usuario/Select";
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "subcontratantes",
        components: {ModelListSelect},
        data() {
            return {
                form: {
                    user_id: '',
                    area_id: []
                },
                cargando: false,
                guardando: false,
                empresas: [],
                nombre: [],
                disponibles: [],
                asignados: [],
                originales: [],
                id_empresa: '',
                mover: [],
                selected: [],
                areas_disponibles: [],
                areas_asignados: [],
                areas_originales:[],
                usuario_seleccionado: ''
            }
        },
        mounted() {
            this.id_empresa = '';
            this.getEmpresasConsolidadoras();
            $(this.$refs.modal).on('hidden.bs.modal', () => {
                this.usuario_seleccionado = '';
            })
        },
        getContratista() {                
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'Contratista' }
                })
                    .then(data => {
                        this.contratistas = data.data;
                    })                    
            },
        methods: {
            addArea(data){
                this.areas_disponibles.push(data);
                this.areas_disponibles = this.areas_disponibles.sort((a, b) => (a.descripcion > b.descripcion) ? 1 : -1);
            },

            changeSelect()
            {
                alert('Change select');
            },

            aliasbddnombre (item){
                return `${item.nombre} - ${item.alias}`;
            },

            getEmpresasConsolidadoras() {
                
                return this.$store.dispatch('seguridad/lista-empresas/index', {
                    params: {sort: 'Nombre', order: 'asc', scope: 'consolidadora'}
                })
                    .then(data => {
                        this.empresas = data.data;
                        console.log('Empresas', this.empresas);
                        
                    })
            },

            getEmpresasAsociadadas(id) {
                this.originales = [];
                return this.$store.dispatch('seguridad/lista-empresas/find', {
                    id: id,
                    params: {sort: 'Nombre', order: 'asc', include: 'consolida'}
                })
                    .then(data => {
                        this.asignados = data.consolida.data;
                        this.originales = data.consolida.data;
                        this.nombre = data.nombre
                        console.log('nombre', this.nombre);
                        this.getEmpresasDisponibles();
                        
                        // this.asignados = this.empresas.diff(this.asignados );
                        // console.log('Disponibles', this.disponibles);
                        // console.log('empieza');
                        
                        // console.log('Asignacion', this.empresas.diff(this.disponibles));
                        // console.log('termina'); 
                    })
            },

            getEmpresasDisponibles()
            {
                return this.$store.dispatch('seguridad/lista-empresas/index', {
                    params: {sort: 'Nombre', order: 'asc', scope: 'disponibles'}
                })
                    .then(data => {
                        this.disponibles = data.data;
                        console.log('Disponibles', this.disponibles);
                        
                    })
            },

            agregar() {
                console.log('agregar', this.selected);
                // this.disponibles = this.disponibles.diff(this.selected);
                // console.log('Disponibles diff', this.disponibles);
                
                this.selected.forEach(consolida => {
                    this.disponibles.forEach(dis => {
                        if(dis.id == consolida) {

                            this.asignados.push(dis);
                            console.log('consolida con push', this.asignados);

                            this.disponibles = this.disponibles.filter(empresa => {
                                return empresa.id != dis.id;
                            });
                            
                            // this.areas_disponibles = this.areas_disponibles.filter(areas => {
                            //     return areas.id != a.id;
                            // });
                        }
                    })
                })
            },

            quitar() {
                console.log('Quitar', this.mover);
                
                this.mover.forEach(consolida => {
                    this.asignados.forEach(asig => {
                        if(asig.id == consolida) {
                            this.disponibles.push(asig);
                            console.log('disponibles', this.disponibles);
                            
                            this.asignados = this.asignados.filter(empresa => {
                                return empresa.id != asig.id;
                            });
                        }
                    })
                })
            },

            asignar() {
                this.guardando = true;
                return this.$store.dispatch('configuracion/area-subcontratante/asignacionAreasSubcontratantes', {
                    user_id: this.form.user_id,
                    area_id: this.areas_asignados.map(area => (
                        area.id
                    ))
                })
                    .then(data => {
                        if (this.currentUser.idusuario == this.form.user_id) {
                            this.$session.set('permisos', data)
                        }
                        this.areas_originales = this.areas_asignados.map(area => (
                            area.id
                        ))
                    } )
                    .finally(() => {
                        $(this.$refs.modal).modal('hide');
                        this.guardando = false;

                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.save()
                    }
                });
            },

            save() {
                this.$store.dispatch('igh/usuario/find', {
                    id: this.form.user_id
                })
                    .then(data => {
                        this.usuario_seleccionado = data.nombre;
                        $(this.$refs.modal).modal('show');
                    })
            }
        },

        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            },
            
            disponibles_ordered() {
                
                return this.disponibles.sort((a,b) => {
                    return (a.nombre<b.nombre?-1:(a.nombre>b.nombre?1:0));
                });
            },
            desasignados() {
                return this.disponibles.filter(areas => {
                    return $.inArray(areas.id, this.areas_originales) > -1;
                })
            },

            nuevos_asignados() {
                return this.asignados.filter(areas => {
                    return $.inArray(areas.id, this.areas_originales) == -1;
                })
            },
            areas_desasignados() {
                return this.areas_disponibles.filter(areas => {
                    return $.inArray(areas.id, this.areas_originales) > -1;
                })
            },
            areas_nuevos_asignados() {
                return this.areas_asignados.filter(areas => {
                    return $.inArray(areas.id, this.areas_originales) == -1;
                })
            }
        },
        watch: {
            'id_empresa'(id) {
                this.$validator.reset();
                console.log('Cambio', id);
                this.getEmpresasAsociadadas(id);
                // this.$validator.reset()
                // if (id) {
                //     this.cargando = true;
                //     this.getAreasUsuario(id)
                //         .finally(() => {
                //             this.cargando = false;
                //         });
                // }
            }

        },
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
    .vue-treeselect__placeholder {
        color: #495057
    }

    .container {
        height: auto;
        position: relative;
        min-height: 50px;
    }

    .vertical-center {
        position: relative;
        top: 50%;
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
