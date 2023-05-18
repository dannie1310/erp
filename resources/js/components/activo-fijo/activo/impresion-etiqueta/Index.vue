<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tipo">Imprimir por: </label>
                        <select class="form-control"
                                name="tipo"
                                data-vv-as="Tipo"
                                v-model="tipo"
                                v-validate="{required: true}"
                                :error="errors.has('tipo')"
                                id="tipo">
                            <option value selected>-- Seleccione tipo de busqueda --</option>
                            <option value="1">Usuario</option>
                            <option value="2">Activo</option>
                            <option value="3">Departamento</option>
                            <option value="4">Ref. Factura</option>
                            <option value="5">Proyecto</option>
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-10" v-if="tipo == 1">
                    <div class="form-group">
                        <label for="id_usuario">Usuario: </label>
                        <treeselect
                                    v-model="id_usuario"
                                    :options="usuarios"
                                    data-vv-as="Usuario"
                                    v-validate="{required: tipo == 1 ? true : false}"
                                    placeholder="Seleccione el usuario">
                            <div slot="value-label" slot-scope="{ node }">{{ node.raw.customLabel }}</div>
                        </treeselect>
                        <div style="display:block" class="invalid-feedback" v-show="errors.has('id_usuario')">{{ errors.first('id_usuario') }}</div>
                    </div>
                </div>
                <div class="col-md-6" v-if="tipo == 2">
                    <div class="form-group error-content">
                        <div class="form-group">
                            <label for="codigo">Código:</label>
                            <input class="form-control"
                                   style="width: 100%"
                                   placeholder="Código"
                                   name="codigo"
                                   id="codigo"
                                   data-vv-as="Código"
                                   v-validate="{required: tipo == 2 ? true : false}"
                                   v-model="codigo"
                                   :class="{'is-invalid': errors.has('codigo')}">
                            <div class="invalid-feedback" v-show="errors.has('codigo')">{{ errors.first('codigo') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-10" v-if="tipo == 3">
                    <div class="form-group">
                        <label for="id_departamento">Departamento: </label>
                        <treeselect
                            v-model="id_departamento"
                            :options="departamentos"
                            data-vv-as="Departamento"
                            v-validate="{required: tipo == 3 ? true : false}"
                            placeholder="Seleccione el departamento">
                            <div slot="value-label" slot-scope="{ node }">{{ node.raw.customLabel }}</div>
                        </treeselect>
                        <div style="display:block" class="invalid-feedback" v-show="errors.has('id_departamento')">{{ errors.first('id_departamento') }}</div>
                    </div>
                </div>
                <div class="col-md-6" v-if="tipo == 4">
                    <div class="form-group error-content">
                        <div class="form-group">
                            <label for="referencia">Referencia de Factura:</label>
                            <input class="form-control"
                                   style="width: 100%"
                                   placeholder="Referencia"
                                   name="referencia"
                                   id="referencia"
                                   data-vv-as="Referencia"
                                   v-validate="{required: tipo == 4 ? true : false}"
                                   v-model="referencia"
                                   :class="{'is-invalid': errors.has('referencia')}">
                            <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-10" v-if="tipo == 5">
                    <div class="form-group error-content">
                        <div class="form-group">
                            <label for="proyecto">Proyecto:</label>
                            <treeselect
                                v-model="proyecto"
                                :options="ubicaciones"
                                data-vv-as="proyecto"
                                v-validate="{required: tipo == 5 ? true : false}"
                                placeholder="Seleccione el proyecto">
                                <div slot="value-label" slot-scope="{ node }">{{ node.raw.customLabel }}</div>
                            </treeselect>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('proyecto')">{{ errors.first('proyecto') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <formato v-bind:id="dato" v-bind:tipo="tipo"></formato>
        </div>
    </div>
</template>

<script>
import UsuarioSelect from "../../../igh/usuario/Select";
import Formato from "./FormatoImpresionEtiqueta";
export default {
    name: "Index",
    components: {UsuarioSelect, Formato},
    data() {
        return {
            tipo: '',
            id_usuario: '',
            codigo : null,
            usuarios : [],
            departamentos : [],
            id_departamento : '',
            referencia: null,
            dato: null,
            ubicaciones : [],
            proyecto : ''
        }
    },
    methods: {
        getUsuarios()
        {
            return this.$store.dispatch('activo-fijo/lista-usuario/index', {
                params: {
                    scope: 'partidasUbicacion'
                }
            }).then(data => {
               this.usuarios = data.data;
               this.usuariosAcomodar()
            })
        },
        getDepartamentos()
        {
            return this.$store.dispatch('activo-fijo/lista-departamento/index', {
                params: {
                    scope: 'partidasPorDepartamento'
                }
            }).then(data => {
                this.departamentos = data.data;
                this.departamentosAcomodar();
            })
        },
        getUbicaciones()
        {
            return this.$store.dispatch('activo-fijo/ubicacion-resguardo/index', {
                params: {
                    sort: 'vw_ubicaciones_resguados.Ubicacion', order: 'ASC'
                }
            }).then(data => {
                this.ubicaciones = data.data;
                this.ubicacionesAcomodar();
            })
        },
        usuariosAcomodar () {
            this.usuarios = this.usuarios.map(i => ({
                id: i.id,
                label: `${i.nombre}`+' ('+`${i.ubicacion}`+')',
                customLabel: `${i.nombre}`+' ('+`${i.ubicacion}`+')',
            }));
        },
        departamentosAcomodar () {
            this.departamentos = this.departamentos.map(i => ({
                id: i.id,
                label: `${i.nombre}`,
                customLabel: `${i.nombre}`
            }));
        },
        ubicacionesAcomodar () {
            this.ubicaciones = this.ubicaciones.map(i => ({
                id: i.id,
                label: `${i.nombre}`,
                customLabel: `${i.nombre}`
            }));
        },
    },
    watch: {
        tipo(value) {
            if (value !== '' && value !== null && value !== undefined) {
                if(value == 1)
                {
                    this.getUsuarios();
                }
                if(value == 3)
                {
                    this.getDepartamentos();
                }
                if(value == 5)
                {
                    this.getUbicaciones();
                }
                this.id_usuario = ''
                this.codigo = null
                this.referencia = null
                this.id_departamento = ''
                this.proyecto = ''
            }
        },
        id_usuario(value)
        {
            if (value !== '' && value !== null && value !== undefined)
            {
                this.dato = value

            }
        },
        codigo(value)
        {
            if (value !== '' && value !== null && value !== undefined)
            {
                this.dato = value
            }
        },
        id_departamento(value)
        {
            if (value !== '' && value !== null && value !== undefined)
            {
                this.dato = value
            }
        },
        referencia(value)
        {
            if (value !== '' && value !== null && value !== undefined)
            {
                this.dato = value
            }
        },
        proyecto(value)
        {
            if (value !== '' && value !== null && value !== undefined)
            {
                this.dato = value
            }
        }
    }
}
</script>

<style scoped>

</style>
