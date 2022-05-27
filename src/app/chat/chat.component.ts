import { Component, Input, OnInit } from '@angular/core';
import { BbddProyectosService } from '../bbdd-proyectos.service';

@Component({
  selector: 'app-chat',
  templateUrl: './chat.component.html',
  styleUrls: ['./chat.component.css']
})
export class ChatComponent implements OnInit {
  @Input() proyecto: any;

  constructor(private bbddProyectos: BbddProyectosService) {
    let interval = window.setInterval(() => { this.recargarProyecto() }, 30000)
    
    
  }

  ngOnInit(): void {
    setTimeout(() => {
      this.bajarScroll();      
    }, 100);
  }

  getMiUsuario() {
    return this.bbddProyectos.getMiUsuario();
  }

  enviarMensaje(texto: any) {
    if (texto != '') {
      this.bbddProyectos.enviarMensaje(this.proyecto.id, texto.value).subscribe(
        (respuesta) => {
          this.proyecto = respuesta;
          this.bajarScroll();
        }
      )
      texto.value = "";
    }
  }

  recargarProyecto() {
    this.bbddProyectos.getProyectoById(this.proyecto.id).subscribe(
      (respuesta:any) => {
        if(this.proyecto.mensajes.length != respuesta.mensajes.length){
          this.proyecto = respuesta;
          this.bajarScroll();
        }
      }
    )
  }
  

  bajarScroll(){
    var target = document.querySelector('#mensajes');
    if (target) {
      target.scrollTop = target.scrollHeight
    }
  }

  


}
