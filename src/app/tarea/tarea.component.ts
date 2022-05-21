import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
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

  detalles:boolean = false;
  miembros:any[] = [];
  modalMover:boolean = false;

  constructor(private bbddProyectos: BbddProyectosService) { }

  ngOnInit(): void {
  }

  mostrarDetalles(){
    this.detalles = !this.detalles
  }  

  getDificultad(){
    let dificultad:string;
    switch(this.tarea.dificultad){
      case 1: dificultad="Muy fácil"; break;
      case 2: dificultad="Fácil"; break;
      case 3: dificultad="Normal"; break;
      case 4: dificultad="Difícil"; break;
      default: dificultad="Muy difícil";
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
   this.bbddProyectos.deleteTarea(this.tarea.id).subscribe();
   
   let indiceTarea:number = -1;
   let indiceLista:number = -1;
   for (let i = 0; i < this.proyecto.listas.length && indiceTarea == -1; i++) {
      if(this.lista.id == this.proyecto.listas[i].id){

        for (let j = 0; j < this.proyecto.listas[i].tareas.length; j++) {
          if(this.tarea.id == this.proyecto.listas[i].tareas[j].id){
            indiceTarea = j;
            indiceLista = i;
            break;
          }          
        }
      }      
    }

    if(indiceTarea != -1){
      this.proyecto.listas[indiceLista].tareas.splice(indiceTarea, 1);
    }
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

  moverTarea(listaId:string){
    let id = parseInt(listaId);
    let indiceNuevaLista = -1;
    for (let i = 0; i < this.proyecto.listas.length; i++) {
      if(listaId == this.proyecto.listas[i].id){
        indiceNuevaLista = i;
        break;
      }
      
    }

    if(indiceNuevaLista != -1){
      this.bbddProyectos.moverTarea(this.tarea.id, id);
      
      let indiceTarea = -1;
      let indiceLista = -1;
  
      for (let i = 0; i < this.proyecto.listas.length && indiceTarea == -1; i++) {
        if(this.lista.id == this.proyecto.listas[i].id){
          for (let j = 0; j < this.proyecto.listas[i].tareas.length; j++) {
            if(this.tarea.id == this.proyecto.listas[i].tareas[j].id){
              indiceTarea = j;
              indiceLista = i;
              break;
            }
            
          }
        }      
      }
  
  
      if(indiceTarea != -1){
        let copiaTarea = this.proyecto.listas[indiceLista].tareas[indiceTarea];
        this.proyecto.listas[indiceLista].tareas.splice(indiceTarea, 1);
        this.proyecto.listas[indiceNuevaLista].tareas.push(copiaTarea);
      }

    }


  }

 



}
