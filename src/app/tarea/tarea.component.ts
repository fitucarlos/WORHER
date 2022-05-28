import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import Swal from 'sweetalert2';
import { BbddProyectosService } from '../bbdd-proyectos.service';

@Component({
  selector: 'app-tarea',
  templateUrl: './tarea.component.html',
  styleUrls: ['./tarea.component.css']
})
export class TareaComponent implements OnInit {
  @Input() tarea:any;
  @Input() lista:any;
  @Input() proyecto:any;
  @Output() actualizar = new EventEmitter<boolean>();

  miembros:any[] = [];
  editando:boolean = false;

  constructor(private bbddProyectos: BbddProyectosService) { }

  ngOnInit(): void {
  }

    

  getDificultad(){
    let dificultad:string;
    switch(this.tarea.dificultad){
      case 1: dificultad="Fácil"; break;
      case 2: dificultad="Normal"; break;
      default: dificultad="Difícil"; 
    }

    return dificultad;
  }

  getPrioridad(){
    let prioridad:string;
    switch(this.tarea.prioridad){
      case 1: prioridad="Muy baja"; break;
      case 2: prioridad="Baja"; break;
      case 3: prioridad="Normal"; break;
      case 4: prioridad="Alta"; break;
      default: prioridad="Muy alta";
    }

    return prioridad;
  }

  borrarTarea(){
   this.bbddProyectos.deleteTarea(this.tarea.id).subscribe(
     () => {

     }, (error)=>{
       Swal.fire("ERROR", "Error al eliminar la tarea", "error");
     }
   );
   
   this.actualizar.emit(true);
  }

  buscarMiembro(email:string){

  }

  quitarMiembro(miembro:any){
    let encontrado: boolean = false;
    let indice: number = -1;
    for (let i = 0; i < this.miembros.length && !encontrado; i++) {
      if (this.miembros[i] == miembro) {
        encontrado = true;
        indice = i;
      }
    }

    if (indice != -1) this.miembros.splice(indice, 1);

  }

  getListas(){ 
    let listas:any[] = this.proyecto.listas;
    let copiaLista:any = listas[0];
    for (let i = 0; i < listas.length && this.lista != listas[0]; i++) {
      if (this.lista == listas[i]) {
        listas[0] = listas[i];
        listas[i] = copiaLista;
        break;
      }      
    }
    return listas; 
  }
  
  cambiarEditando(){
    this.editando = !this.editando;
  }
  
  editar(nombre:string, prioridad:string, dificultad:string, descripcion:string){
    this.bbddProyectos.editarTarea(this.tarea.id, nombre, descripcion, parseInt(dificultad), parseInt(prioridad));
    this.bbddProyectos.cargar();
    this.cambiarEditando();
    this.actualizar.emit(true);
  }
  
  cancelarEdicion(nombre:any, prioridad:any, dificultad:any, descripcion:any){
    nombre.value = this.tarea.nombre;
    prioridad.value = this.tarea.prioridad;
    dificultad.value = this.tarea.dificultad;
    descripcion.value = this.tarea.descripcion;
    this.cambiarEditando();
  }

  
  
  
  
  


}
