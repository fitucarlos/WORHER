import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { Router } from '@angular/router';
import Swal from 'sweetalert2';
import { BbddProyectosService } from '../bbdd-proyectos.service';

@Component({
  selector: 'app-chat',
  templateUrl: './chat.component.html',
  styleUrls: ['./chat.component.css']
})
export class ChatComponent implements OnInit {
  @Input() proyecto: any;
  @Output() actualizar = new EventEmitter<boolean>();

  constructor(private bbddProyectos: BbddProyectosService, private route: Router) {

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
          this.actualizar.emit(true);
          this.bajarScroll();

        }, (error) => {
          Swal.fire('ERROR', 'Error al enviar el mensaje', 'error');
        }
      )
      texto.value = "";
    }
  }

  recargarProyecto() {
    this.bbddProyectos.getProyectoById(this.proyecto.id).subscribe(
      (respuesta: any) => {
        if (this.proyecto.mensajes.length != respuesta.mensajes.length) {
          this.proyecto = respuesta;
          this.bajarScroll();
        }
      }, (error) => {
        Swal.fire('ERROR', 'Error al cargar el proyecto', 'error');
        this.route.navigate(['/inicio']);
      }
    )
  }


  bajarScroll() {
    var target = document.querySelector('#mensajes');
    if (target) {
      target.scrollTop = target.scrollHeight
    }
  }




}
