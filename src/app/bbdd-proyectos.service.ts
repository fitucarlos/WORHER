import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import Swal from 'sweetalert2';
import * as CryptoJS from 'crypto-js';

@Injectable({
  providedIn: 'root'
})
export class BbddProyectosService {

  private url: string = "/api/";
  private proyectos: any[] = [];
  private cargando: boolean = true;
  private usuarioId: number = 0;
  private filtro: any[] = [
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

  constructor(private http: HttpClient) {
    if (sessionStorage.getItem('id') && sessionStorage.getItem('id') != null) {
      let id = sessionStorage.getItem('id');
      if (id != null) {
        let desenc = CryptoJS.AES.decrypt(id, 'id').toString(CryptoJS.enc.Utf8);
        this.usuarioId = parseInt(desenc);
        this.cargarDatos();
      }
    }
  }

  setUsuarioId(id: number) {
    this.usuarioId = id;
  }



  isCargando() { return this.cargando }

  cargar() {
    this.cargando = true;
  }

  noCargar() {
    this.cargando = false;
  }


  getProyectos(): any {

    return this.proyectos
  }

  getProyectoById(id: number) { return this.http.get(this.url + 'proyecto/' + id + '/' + this.usuarioId); }

  addLista(proyectoId: number, nombreLista: string) {
    let body = {
      "listas": [
        {
          "nombre": nombreLista
        }
      ]
    };
    return this.http.post(this.url + "proyecto/add_lista/" + proyectoId, body);
  }

  deleteLista(listaId: number) {
    this.http.post(this.url + "proyecto/remove_lista/" + listaId, null).subscribe(
      () => {

      }, (error) => {
        Swal.fire('ERROR', "Error al eliminar la lista.", 'error');
      }
    );
  }

  renombrarLista(idLista: number, nombre: string) {
    let body = {
      "nombre": nombre
    };

    this.http.post(this.url + "edit_lista/" + idLista, body).subscribe(
      (respuesta) => {
        this.cargarDatos();
      }, (error) => {
        Swal.fire('ERROR', "Error al editar la lista", 'error');
      }
    );
  }

  renombrarProyecto(id: number, nombre: string) {
    this.cargar()
    let body = {
      "nombre": nombre
    };

    this.http.post(this.url + "edit_proyecto/" + id, body).subscribe(
      (respuesta) => {
        this.cargarDatos();
      }, (error) => {
        Swal.fire('ERROR', "Error al editar el proyecto", 'error');
      }
    );
  }

  deleteProyecto(id: number) {
    this.http.post(this.url + "proyecto/remove_proyecto/" + id, null).subscribe(
      (respuesta) => {
        this.cargarDatos()
      }, (error) => {
        Swal.fire('ERROR', "Error al eliminar el proyecto", 'error');
      }
    );
  }

  addProyecto(nombre: string, usuarios: any[]) {
    let body = {
      "nombre": nombre
    }
    this.http.post(this.url + "proyecto/" + this.usuarioId, body).subscribe(
      (proyecto: any) => {
        usuarios.forEach(u => {
          this.addUsuarioProyecto(proyecto.id, u.id).subscribe(
            (respuesta) => {
              this.cargarDatos()
            }, (error) => {
              Swal.fire('ERROR', "Error al añadir usuario al proyecto", 'error');
            }
          );
        });
        this.cargarDatos()
      }, (error) => {
        Swal.fire('ERROR', "Error al añadir el proyecto", 'error');
      }
    );
  }

  addUsuarioProyecto(idProyecto: number, id: number) {
    return this.http.post(this.url + "proyecto/add_user/" + idProyecto + "/" + id, null);
  }

  buscarMiembro(email: string) {
    return this.http.get(this.url + "search_user/" + email);
  }

  addTarea(idLista: number, nombre: string, descripcion: string, dificultad: number, prioridad: number) {
    this.cargar();
    let body = {
      "tareas": [
        {
          "nombre": nombre,
          "descripcion": descripcion,
          "dificultad": dificultad,
          "prioridad": prioridad
        }
      ]
    }
    return this.http.post(this.url + 'proyecto/add_tarea/' + idLista, body)
  }

  deleteTarea(id: number) {
    return this.http.post(this.url + "proyecto/remove_tarea/" + id, null);
  }

  moverTarea(idTarea: number, idLista: number) {
    return this.http.get(this.url + 'proyecto/change_tarea/' + idTarea + '/' + idLista)
  }

  editarTarea(id: number, nombre: string, descripcion: string, dificultad: number, prioridad: number) {
    this.cargar();
    let body =
    {
      "nombre": nombre,
      "descripcion": descripcion,
      "dificultad": dificultad,
      "prioridad": prioridad
    };

    this.http.post(this.url + 'edit_tarea/' + id, body).subscribe(
      (respuesta: any) => {
        this.cargarDatos();
      }, (error) => {
        console.log(error)
        Swal.fire('ERROR', "Error al editar la tarea.", 'error');
      }
    )
  }

  addUsuarioTarea(id_tarea: number, id_usuario: number) {
    this.cargar();
    this.http.post(this.url + 'proyecto/add_user_tarea/' + id_tarea + '/' + id_usuario, null).subscribe(
      (respuesta) => {
        this.cargarDatos()
      }, (error) => {
        Swal.fire('ERROR', "Error al añadir usuario a la tarea", 'error');
      }
    );
  }

  buscarMiembroProyecto(id: number, email: string) {
    return this.http.get(this.url + 'proyecto/search_user/' + id + '/' + email);
  }


  cargarDatos() {
    this.cargar();
    this.http.get(this.url + 'proyectos/' + this.usuarioId).subscribe(
      (respuesta: any) => {
        this.proyectos = respuesta;
        this.noCargar()
      }, (error) => {
        Swal.fire('ERROR', "Error al cargar los proyectos", 'error');
      }
    );
  }


  setFiltro(prioridad: number, valor: boolean) {
    for (let i = 0; i < this.filtro.length; i++) {
      if (this.filtro[i].prioridad == prioridad) {
        this.filtro[i].valor = valor;
        break;
      }
    }
  }

  getFiltro() {
    return this.filtro;
  }

  getMiUsuario() {
    return this.usuarioId;
  }

  enviarMensaje(idproyecto: number, texto: string) {
    let body =
    {
      "texto": texto
    };

    return this.http.post(this.url + 'proyecto/add_mensaje/' + idproyecto + '/' + this.usuarioId, body)

  }

  quitarMiembroTarea(tareaId: number, usuarioId: number) {
    this.cargar();
    this.http.post(this.url + 'proyecto/remove_user_tarea/' + tareaId + '/' + usuarioId, null).subscribe(
      (respuesta: any) => {
        this.cargarDatos();
      }, (error) => {
        Swal.fire('ERROR', "Error al eliminar el usuario de la tarea.", 'error');
      }
    )
  }




}
