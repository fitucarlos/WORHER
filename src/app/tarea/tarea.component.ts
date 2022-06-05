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

  editando:boolean = false;
  asignando:boolean = false;
  errores:boolean = false;

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
     this.bbddProyectos.cargar();
     this.actualizar.emit(true);
   
  }

  buscarMiembro(email:any){
    let expReg = /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/
    if (email.value == '') {
      Swal.fire('Aviso', 'No ha introducido ningún email en el buscador. Por favor, introduzca un email de un usuario del proyecto para asignar la tarea.', 'warning')
    } else if (!expReg.test(email.value)) {
      Swal.fire('ERROR', 'No ha introducido un email válido en el buscador. Por favor, introduzca un email de un usuario del proyecto para asignar la tarea.', 'error')
      email.value = '';
      email.focus();
    } else {
      this.bbddProyectos.buscarMiembroProyecto(this.proyecto.id, email.value).subscribe(
        (m: any) => {
          let encontrado: boolean = false;
          for (let i = 0; i < this.tarea.usuarios.length && !encontrado; i++) {
            if (this.tarea.usuarios[i].id == m.id) encontrado = true;
            
          }
          if (!encontrado) this.tarea.usuarios.push(m);
          email.value = '';
        }, (error) => {
          Swal.fire('ERROR', 'El email introducido no pertenece al proyecto', 'error');
          email.value = '';
          email.focus();
        }
      )

    }
  }

  quitarMiembro(miembro:any){
    let encontrado: boolean = false;
    let indice: number = -1;
    for (let i = 0; i < this.tarea.usuarios.length && !encontrado; i++) {
      if (this.tarea.usuarios[i] == miembro) {
        encontrado = true;
        indice = i;
      }
    }

    if (indice != -1) {
      this.bbddProyectos.quitarMiembroTarea(this.tarea.id, miembro.id);
      this.tarea.usuarios.splice(indice, 1);
    }

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
    if(this.editando && this.asignando) this.asignando = false;
    if(this.editando) this.actualizar.emit(false);
    else this.actualizar.emit(true);
  }
  
  editar(nombre:string, prioridad:string, dificultad:string, descripcion:string){
    this.errores = false;
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

  getIniciales(usuario:any){
    let iniciales:string;
    iniciales=usuario.nombre[0]+usuario.apellido[0];
    iniciales = iniciales.toUpperCase();
    return iniciales;
  }

  cambiarAsignar(){
    this.asignando = !this.asignando;
    if(this.asignando && this.editando) this.editando = false;
    if(this.asignando) this.actualizar.emit(false)
    else this.actualizar.emit(true);
  }

  asignarTarea(){
    this.cambiarAsignar();
    this.tarea.usuarios.forEach((u: { id: number; }) => {
      this.bbddProyectos.addUsuarioTarea(this.tarea.id, u.id)
    });
  }

  crearErrores(){
    this.errores = true;
    Swal.fire("Atención", "Debe completar los campos obligatorios", "warning")
  }

  validarForm(){
    
  }
  
  
  
  


}
