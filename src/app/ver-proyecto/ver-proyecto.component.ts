import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { BbddProyectosService } from '../bbdd-proyectos.service';

@Component({
  selector: 'app-ver-proyecto',
  templateUrl: './ver-proyecto.component.html',
  styleUrls: ['./ver-proyecto.component.css']
})
export class VerProyectoComponent implements OnInit {
  static escritorio = false;
  private id: any;
  proyecto: any;
  cargando: boolean = true;
  filtrando: boolean = false;
  usuarios: boolean = false;
  chat = false;
  tareas:any[]=[]

  constructor(private actRoute: ActivatedRoute, private bbddProyectos: BbddProyectosService) { }

  ngOnInit(): void {
    this.actRoute.params.subscribe(
      (respuesta:any) => {
        this.id = respuesta.id;
        this.cargarDatos();
      }
    )

    window.addEventListener("resize", function () {
      let pantalla = window.innerWidth;
      if (pantalla >= 1224) {
        VerProyectoComponent.escritorio = true;
      }
      else {
        VerProyectoComponent.escritorio = false;
      }
    });


  }

  noMostrarChat() {
    this.chat = false;
  }

  isActualizar(actualizar:boolean){
    if(actualizar){
      this.cargarDatos();
    }
  }

  mostrarChat() {
    this.chat = true;

  }

  isChat() {
    if (VerProyectoComponent.escritorio) {
      this.chat = false;
    }
    return this.chat;
  }

  cargarDatos() {
    this.cargando = true;
    let sos= this.bbddProyectos.getProyectoById(this.id).subscribe(
      (datos:any)=> {
        this.cargando = false;
        this.proyecto = datos;
      }
    )
  }

  isCargando() {
    if(!this.cargando) return false
    else return true;
  }

  filtrar() {
    this.filtrando = !this.filtrando;
  }

  mostrarUsuarios() {
    this.usuarios = !this.usuarios;
  }

  get3Miembros() {
    let miembros: any[] = [];
    for (let i = 0; i < this.proyecto.usuarios.length && i < 3; i++) {
      miembros.push(this.proyecto.usuarios[i]);
    };

    return miembros;
  }

  addLista(nombre: string) {
    if(nombre=="") nombre = "(Sin nombre)";
    this.bbddProyectos.addLista(this.proyecto.id, nombre);
    this.cargarDatos();
  }  

  getTareasLista(original:string, tarea:any, lista:any, boton:any){
    if(original !='-1'){
      let listaId = parseInt(original);
      for (let i = 0; i < this.proyecto.listas.length; i++) {
        if(this.proyecto.listas[i].id == listaId){
          this.tareas = this.proyecto.listas[i].tareas;
          break;
        }
        
      }

      tarea.disabled = false;
    } else {
      tarea.value = '-1';
      tarea.disabled = true;
      lista.value = '-1';
      lista.disabled = true;
      boton.disabled = true;
    }
  }

  habilitarResto(tarea:string, lista:any, boton:any){
    if(tarea != '-1'){
      lista.disabled = false;
    }
    else{
      lista.value = '-1';
      lista.disabled = true;
      boton.disabled = true;
    }
  }

  habilitarBoton(lista:string, original:string, boton:any){
    if(lista != '-1' && lista != original){
      boton.disabled = false;
    } else {
      boton.disabled = true;
    }
  }

  moverTarea(tareaId:string, listaId:string, original:string){
    let idLista = parseInt(listaId);
    let idTarea = parseInt(tareaId);
    let originalId = parseInt(original);
    let indiceNuevaLista = -1;
    for (let i = 0; i < this.proyecto.listas.length; i++) {
      if(listaId == this.proyecto.listas[i].id){
        indiceNuevaLista = i;
        break;
      }      
    }

    if(indiceNuevaLista != -1){
      this.bbddProyectos.moverTarea(idTarea, idLista);
      
      let indiceTarea = -1;
      let indiceLista = -1;
  
      for (let i = 0; i < this.proyecto.listas.length && indiceTarea == -1; i++) {
        if(originalId == this.proyecto.listas[i].id){
          for (let j = 0; j < this.proyecto.listas[i].tareas.length; j++) {
            if(idTarea == this.proyecto.listas[i].tareas[j].id){
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

  getListas(){
    return this.proyecto.listas;
  }



}
