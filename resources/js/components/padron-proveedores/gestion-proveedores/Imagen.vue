<template>
    <span>

     <div id="myCarousel" class="carousel slide carousel-fade" data-ride="carousel">
         <!--<ul class="carousel-indicators">
             <li class="active"   data-target="#item" data-slide-to="0" ></li>
             <li data-slide-to="1" data-target="#item" ></li>
             <li data-slide-to="2" data-target="#item" ></li>
         </ul>-->
         <div class="carousel-inner">
             <div class="carousel-item active">
                 <img class="img-responsive" src="../../../../../public/uploads/padron_contratistas/MUC1803013I7/codigo.png" :alt="getURLImagen()" width="800" height="500">
                 <div class="carousel-caption">
                     <h3>Porqueria!</h3>
                     <p>We love the dfsdfsdf Apple! aqui?</p>
                 </div>
             </div>

             <div class="carousel-item">
                 <img :src="getURLImagen()"  alt="Los Angedsfsdfsdfles" width="800" height="500">
                 <div class="carousel-caption">
                     <h3>prueba 2</h3>
                     <p>We love the Big Apple!si?</p>
                 </div>
             </div>
             <div class="carousel-item">
                 <img  :src="getURLImagen()"   :alt="getURLImagen()" width="800" height="500">
                 <div class="carousel-caption">
                     <h3>prueba aqui 2</h3>
                     <p>We love the Big Apple!si?</p>
                 </div>
             </div>
             <div class="carousel-item">
                 <img :src="'../../../../../public/uploads/padron_contratistas/MUC1803013I7/'+url" :alt="'../../../../../public/uploads/padron_contratistas/MUC1803013I7/'+url" width="800" height="500">
                 <div class="carousel-caption">
                     <h3>aqq</h3>
                     <p>We love the Big Apple!oooo</p>
                 </div>
             </div>
         </div>
         <a class="carousel-control-prev" href="#myCarousel" data-slide="prev">
             <span class="carousel-control-prev-icon"></span>
         </a>
         <a class="carousel-control-next" href="#myCarousel" data-slide="next">
             <span class="carousel-control-next-icon"></span>
         </a>
     </div>
    </span>
</template>

<script>
    export default {
        name: "Imagen",
        components: {

        },
        data() {
            return {
                url : "1.jpeg",
                url1 : '../../../../../public/uploads/padron_contratistas/MUC1803013I7/imagen.png',
                imagenes: {
                    3: "imagen_prueba.png",
                    2: "imagenprueba.png",
                    1: "imagen.png"
                },
            };
        },
        methods: {
            getURLImagen()
            {
                console.log("../../../../../public/uploads/padron_contratistas/MUC1803013I7/imagenprueba.jpg")
                return "../../../../../public/uploads/padron_contratistas/MUC1803013I7/imagenprueba.jpg";
            }
        }
    }
</script>
<style scoped>
    .carousel-inner img {
        width: 100%;
        height: 100%;
    }
</style>
