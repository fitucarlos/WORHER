import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class BbddProyectosService {

  private url: string = "/api/";
  private proyectos: any[] = [];
  private cargando: boolean;

  constructor(private http: HttpClient) {
    this.cargando = true;
    this.http.get(this.url + 'proyectos').subscribe(
      (respuesta: any) => {
        this.proyectos = respuesta;
        this.cargando = false;
      }
    );
  }

  isCargando() { return this.cargando }

  getProyectos(): any {

    return this.proyectos
  }

  getProyectoById(id: number) { return this.http.get(this.url + 'proyecto/' + id); }

  addLista(proyectoId: number, nombreLista: string) {
    let body = {
      "listas": [
        {
          "nombre": nombreLista
        }
      ]
    };
    this.http.post(this.url + "proyecto/add_lista/" + proyectoId, body).subscribe(
      (respuesta) => {
        this.cargarDatos();
      }
    );
  }

  deleteLista(listaId: number) {
    this.http.post(this.url + "proyecto/remove_lista/" + listaId, null).subscribe();
  }

  renombrarLista(idLista: number, nombre: string) {
    let body = {
      "nombre": nombre
    };

    this.http.post(this.url + "edit_lista/" + idLista, body).subscribe(
      (respuesta) => {
        this.cargarDatos();
      }
    );
  }

  renombrarProyecto(id: number, nombre: string) {
    this.cargando = true;
    let body = {
      "nombre": nombre
    };

    this.http.post(this.url + "edit_proyecto/" + id, body).subscribe(
      (respuesta) => {
        this.cargarDatos();
      }
    );
  }

  deleteProyecto(id: number) {
    this.http.post(this.url + "proyecto/remove_proyecto/" + id, null).subscribe(
      (respuesta) => {
        this.cargarDatos()
      }
    );
  }

  addProyecto(nombre: string, usuarios: any[]) {
    let body = {
      "nombre": nombre
    }
    this.http.post(this.url + "proyecto", body).subscribe(
      (proyecto: any) => {
        usuarios.forEach(u => {
          this.addUsuarioProyecto(proyecto.id, u.id);
        });
        this.cargarDatos()
      }
    );
  }

  addUsuarioProyecto(idProyecto: number, idUsuario: number) {
    this.http.post(this.url + "proyecto/add_user/" + idProyecto + "/" + idUsuario, null).subscribe(
      (respuesta) => {
        this.cargarDatos()
      }
    );
  }

  buscarMiembro(email: string) {
    return this.http.get(this.url + "proyecto/search_user/" + email);
  }

  addTarea(idLista: number, nombre: string, descripcion: string, dificultad: number, prioridad: number) {
    this.cargando = true;
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

  moverTarea(idTarea:number, idLista:number){
    this.http.get(this.url+'proyecto/change_tarea/'+idTarea+'/'+idLista).subscribe();
  }

  editarTarea(id: number, nombre: string, descripcion: string, dificultad: number, prioridad: number) {
    this.cargando = true;
    let body = 
        {
          "nombre": nombre,
          "descripcion": descripcion,
          "dificultad": dificultad,
          "prioridad": prioridad
        }
      
    this.http.post(this.url + 'edit_tarea/' + id, body).subscribe(
      (respuesta: any) => {
        this.cargarDatos();
      }
    )
  }

  addUsuarioTarea() {
    
  }

  buscarMiembroProyecto(email: string) {
    return this.http.get(this.url + 'proyecto/search_user/' + email);
  }


  cargarDatos() {
    this.cargando = true;
    this.http.get(this.url + 'proyectos').subscribe(
      (respuesta: any) => {
        this.proyectos = respuesta;
        this.cargando = false;
      }
    );
  }

 

  
}
