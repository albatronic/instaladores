/*
 * Tutorial Easy slider
 * Autor: Osiris Magro
 * url: http://innominepixel.wordpress.com
 * Fecha: 15/02/2010
 * Comp�rtelo como quieras
 */

$(document).ready(function(){

    /*
     * Variables
     */

    var slides, timer;

    //Crea un objeto con informaci�n de los slides
    slides = $('#slider .slidesContainer > .slide');


    /*
     * Funciones
     */
    
    //Desplaza el contenedor hacia la direcci�n definida
    //@direction = [left,right]
    function sliderScroll(direction){

       //Calcula la posici�n actual del contenedor
       position = $('#slider').scrollLeft();

       //Calcula la anchura total menos el �ltimo slide.
       //Se usa para calcular cuando el scroll llega al final.
       totalWidth = (slides.length * slides[0].offsetWidth) - slides[0].offsetWidth

       //Se comprueba la variable direction para hacer el scroll hacia izquierda o derecha
       switch (direction) {
            case 'right': //Derecha
                if (position+slides[0].offsetWidth > totalWidth){ //Si la siguiente posici�n se sale del contenedor, vuelve al principio.
                    $('#slider:not(:animated)').animate({scrollLeft:0},1000);
                } else { //Si no es el final, suma a la posici�n actual la anchura del slide.
                    $('#slider:not(:animated)').animate({scrollLeft:position+slides[0].offsetWidth},1000);
                }
                break;

            case 'left': //Izquierda
                if (position-slides[0].offsetWidth < 0){ //Si la siguiente posici�n se sale del contenedor, vuelve al final.
                    $('#slider:not(:animated)').animate({scrollLeft:totalWidth},1000);
                } else { //Si no es el final, resta a la posici�n actual la anchura del slide.
                    $('#slider:not(:animated)').animate({scrollLeft:position-slides[0].offsetWidth},1000);
                }
                break;
        }

   }

   //Funci�n que crea el temporizador
   function initTimer(){
        timer = setInterval(function(){sliderScroll('right');}, 5000);
   }


   /*
    * C�digo
    */

    //Asigna el ancho total de los slides al contenedor
    //La anchura total se obtiene multiplicando la medida de un slide por el n�mero de slides)
    $('#slider .slidesContainer').css('width',slides[0].offsetWidth * slides.length);


    //Click en el bot�n "next"
    $('.next').click(function(){
        clearInterval(timer); //Desactiva el temporizador
        sliderScroll('right'); //Mueve el scroll a la derecha
        initTimer(); //Vuelve a activar el temporizador
        return false;
    });

    //Click en el bot�n "prev"
    $('.prev').click(function(){
        clearInterval(timer); //Desactiva el temporizador
        sliderScroll('left'); //Mueve el scroll a la izquierda
        initTimer(); //Vuelve a activar el temporizador
        return false;
    });

    //Inicia el temporizador
    initTimer();

});