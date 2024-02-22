<template>
    <span>hola</span>

</template>

<script>
    export default {
        name: "validation",
        data() {
            return {
                valid: false,
                verified: this.$router.currentRoute.query.verified == 'false' ? false : true,
                code_secret: ""
            }
        },

        mounted() {
            this.init();
        },

        methods: {
            init() {
               var urr = '/validacion'
				var win = window.open(urr, "_self");

				/*win.onbeforeunload = () => {
					swal("Archivo descargado correctamente.", {
						icon: "success",
						timer: 2000,
						buttons: false
					})
				}*/

                
            },

            

            
        },

       
    }
</script>
<style scoped>


</style>
