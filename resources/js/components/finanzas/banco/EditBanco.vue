<template>
        <div class="container-fluid bg-white mt-3  mb-4 rounded" :disabled="cargando">
            <div class="row justify-content-md-center" >
                <!--Margen-->
                    <div class="col-md-12  mt-3" >
                        <hr style="color: #0056b2;" width="95%" size="10" />
                    </div>
                <!--Formulario-->
                <form role="form" @submit.prevent="update">
                <div class="container">
                    <div class="row ml-5">

                                <!--Razón social--->
                                <div class="col-md-12 mt-5 ml-3">
                                    <div class="form-group row ">
                                        <label for="razon_social" class="mr-1" ><b>Banco: </b> </label>
                                        {{ banco.razon_social}}
                                    </div>
                                </div>
                                    <!--RFC-->
                                    <div class="col-md-12 mt-2  ml-3">
                                        <div class="form-group row ">
                                            <label for="staticEmail" class="mr-1 mt-1" ><b>RFC: </b> </label>
                                                    <div class="col-md-4">
                                                        <input class="form-control"
                                                               name="rfc"
                                                               data-vv-as="RFC"
                                                               v-model="banco.rfc"
                                                               v-validate="{ required: true, regex: /\.(js|ts)$/ }"
                                                               id="rfc"
                                                               placeholder="RFC" />
                                                        <span class="text-danger" v-if="rfcValidate">RFC Inválido</span>
                                                    </div>
                                        </div>
                                    </div>
                                    <!--Botón-->
                        <div class="container mt-2">
                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </div>
                        </div>



                        </div>
                        </div>

                </form>
                </div>
            <!--Margen footer-->
            <div class="col-md-12 mb-3 mt-4" >
                <hr style="color: #0056b2;" width="95%" size="10" />
            </div>
            <p class="text-white">..</p>
            </div>
        </div>




</template>
<script>
    const rfcRegex =/^(([A-ZÑ&]{3})[\-]?([0-9]{2})([0][13578]|[1][02])(([0][1-9]|[12][\\d])|[3][01])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))|(([A-ZÑ&]{3})[\-]?([0-9]{2})([0][13456789]|[1][012])(([0][1-9]|[12][\\d])|[3][0])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))|(([A-ZÑ&]{3})[\-]?([02468][048]|[13579][26])[0][2]([0][1-9]|[12][\\d])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))|(([A-ZÑ&]{3})[\-]?([0-9]{2})[0][2]([0][1-9]|[1][0-9]|[2][0-8])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))$/;
    export default {
        name:"banco-editbanco",
        props:['id'],
        data(){
            return {
                cargando: false,
                rfcValidate: false,
            }
        },
        mounted(){
            this.$Progress.start();

        },
        init(){

        },
        methods:{
            update(){
                if(!rfcRegex.test(this.banco.rfc)){
                    return this.invalidRFC();
                } else{
                    this.rfcValidate=false;
                }

                return this.$store.dispatch('cadeco/banco/update',{
                    id: this.id,
                    data: {
                        rfc: this.banco.rfc,
                    }
                })
                    .then(data => {
                        this.$store.commit('cadeco/banco/UPDATE_BANCO', data);
                    })
            },
            invalidRFC(){
                this.rfcValidate=true;
            }

        },
        computed: {
            banco(){
                return this.$store.getters['cadeco/banco/currentBanco']
            }

        },

    }
</script>
