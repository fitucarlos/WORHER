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
  private constante: string = "modal-backdrop fade show";
  proyecto: any;
  cargando: boolean = true;
  filtrando: boolean = false;
  usuarios: boolean = false;
  chat = false;
  tareas: any[] = []
  edicion: boolean = false;
  filtro: any[] = [];

  constructor(private actRoute: ActivatedRoute, private bbddProyectos: BbddProyectosService) { }

  ngOnInit(): void {
    this.actRoute.params.subscribe(
      (respuesta: any) => {
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

    this.filtro = [
      {
        prioridad: 1,
        valor: true
      },
      {
        prioridad: 2,
        valor: true
      },
      {
        prioridad: 3,
        valor: true
      },
      {
        prioridad: 4,
        valor: true
      },
      {
        prioridad: 5,
        valor: true
      },
    ]

  }

  noMostrarChat() {
    this.chat = false;
  }

  isActualizar(actualizar: boolean) {
    if (actualizar) {
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
    let sos = this.bbddProyectos.getProyectoById(this.id).subscribe(
      (datos: any) => {
        this.cargando = false;
        this.proyecto = datos;
      }
    )
  }

  isCargando() {
    if (!this.cargando) return false
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
    if (nombre == "") nombre = "(Sin nombre)";
    this.bbddProyectos.addLista(this.proyecto.id, nombre);
    this.cargarDatos();
  }

  getTareasListaMover(original: string, tarea: any, lista: any, boton: any) {
    if (original != '-1') {
      let listaId = parseInt(original);
      for (let i = 0; i < this.proyecto.listas.length; i++) {
        if (this.proyecto.listas[i].id == listaId) {
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

  habilitarResto(tarea: string, lista: any, boton: any) {
    if (tarea != '-1') {
      lista.disabled = false;
    }
    else {
      lista.value = '-1';
      lista.disabled = true;
      boton.disabled = true;
    }
  }

  habilitarBoton(lista: string, original: string, boton: any) {
    if (lista != '-1' && lista != original) {
      boton.disabled = false;
    } else {
      boton.disabled = true;
    }
  }

  moverTarea(tareaId: string, listaId: string, original: string) {
    let idLista = parseInt(listaId);
    let idTarea = parseInt(tareaId);
    let originalId = parseInt(original);
    let indiceNuevaLista = -1;
    for (let i = 0; i < this.proyecto.listas.length; i++) {
      if (listaId == this.proyecto.listas[i].id) {
        indiceNuevaLista = i;
        break;
      }
    }

    if (indiceNuevaLista != -1) {
      this.bbddProyectos.moverTarea(idTarea, idLista);

      let indiceTarea = -1;
      let indiceLista = -1;

      for (let i = 0; i < this.proyecto.listas.length && indiceTarea == -1; i++) {
        if (originalId == this.proyecto.listas[i].id) {
          for (let j = 0; j < this.proyecto.listas[i].tareas.length; j++) {
            if (idTarea == this.proyecto.listas[i].tareas[j].id) {
              indiceTarea = j;
              indiceLista = i;
              break;
            }

          }
        }
      }

      if (indiceTarea != -1) {
        let copiaTarea = this.proyecto.listas[indiceLista].tareas[indiceTarea];
        this.proyecto.listas[indiceLista].tareas.splice(indiceTarea, 1);
        this.proyecto.listas[indiceNuevaLista].tareas.push(copiaTarea);
      }

    }
  }

  getListas() {

    return (this.proyecto) ? this.proyecto.listas : [];
  }

  buscarTareaId(id: number) {
    for (let i = 0; i < this.tareas.length; i++) {
      if (this.tareas[i].id == id) {
        return this.tareas[i];
      }

    }
  }

  /*
    getTareasListaEditar(lista:string, tarea:any,nombre:any, prioridad:any, dificultad:any, descripcion:any, boton:any){
      if(lista !='-1'){
        let listaId = parseInt(lista);
        for (let i = 0; i < this.proyecto.listas.length; i++) {
          if(this.proyecto.listas[i].id == listaId){
            this.tareas = this.proyecto.listas[i].tareas;
            break;
          }
          
        }
  
        tarea.disabled = false;
      } else {
        tarea.value = '-1';
        nombre.value = '';
        prioridad.value = '1';
        dificultad.value = '1';
        descripcion.value = '';
        tarea.disabled = true;
        nombre.disabled = true;
        prioridad.disabled = true;
        dificultad.disabled = true;
        descripcion.disabled = true;
        boton.disabled = true;
        descripcion.style.height = '28px';
      }
    }
  
    habilitarEditar(id:string, nombre:any, prioridad:any, dificultad:any, descripcion:any, boton:any){
      if(id !='-1'){
        let tarea:any = this.buscarTareaId(parseInt(id));
        nombre.value = tarea.nombre;
        prioridad.value = tarea.prioridad;
        dificultad.value = tarea.dificultad;
        descripcion.value = tarea.descripcion;
        nombre.disabled = false;
        prioridad.disabled = false;
        dificultad.disabled = false;
        descripcion.disabled = false;
        boton.disabled = false;
        descripcion.style.height = '100px';
      }
      else {
        nombre.value = '';
        prioridad.value = '1';
        dificultad.value = '1';
        descripcion.value = '';
        nombre.disabled = true;
        prioridad.disabled = true;
        dificultad.disabled = true;
        descripcion.disabled = true;
        boton.disabled = true;
        descripcion.style.height = '28px';
      }
    }
  
    editarTarea(tareaId:string, nombre:string, prioridad:string, dificultad:string, descripcion:string){
      this.bbddProyectos.editarTarea(parseInt(tareaId), nombre, descripcion, parseInt(dificultad), parseInt(prioridad));
      
    }
  */

  crearTarea(modal: any, lista: string, nombre: string, descripcion: string, dificultad: string, prioridad: string) {
    modal.style.display = 'none';
    var el = document.querySelector(".modal-open");
    if (el) {
      el.removeAttribute("class");
      el.removeAttribute("style");
    }
    var ele = document.querySelector("#crearTarea");
    if (ele) {
      ele.classList.remove('show');
    }

    let dif: number = parseInt(dificultad);
    let prio: number = parseInt(prioridad);
    this.cargando = true;
    this.bbddProyectos.addTarea(parseInt(lista), nombre, descripcion, parseInt(dificultad), parseInt(prioridad)).subscribe(
      (respuesta) => {
        this.cargarDatos();
      }
    )
  }

  habilitarCrear(lista: string, nombre: any, prioridad: any, dificultad: any, descripcion: any, boton: any) {
    if (lista != '-1') {
      nombre.disabled = false;
      prioridad.disabled = false;
      dificultad.disabled = false;
      descripcion.disabled = false;
      boton.disabled = false;
    }
    else {
      nombre.value = '';
      prioridad.value = '1';
      dificultad.value = '1';
      descripcion.value = '';
      nombre.disabled = true;
      prioridad.disabled = true;
      dificultad.disabled = true;
      descripcion.disabled = true;
      boton.disabled = true;
    }
  }


  filtrarPrioridad(prioridad: string, checked: any) {
    for (let i = 0; i < this.filtro.length; i++) {
      if(this.filtro[i].prioridad == parseInt(prioridad)){
        this.filtro[i].valor = checked.currentTarget.checked;
      }
    }
  }





}
