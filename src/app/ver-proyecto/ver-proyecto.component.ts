import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import Swal from 'sweetalert2';
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
  tareas: any[] = []
  edicion: boolean = false;
  filtro: any[] = [];
  actualizar: boolean = false;
  errores: boolean = false;
  miembros: any[] = [];


  constructor(private actRoute: ActivatedRoute, private bbddProyectos: BbddProyectosService, private route: Router) {
    let interval = window.setInterval(() => { this.cargarDatos() }, 60000)
  }

  ngOnInit(): void {
    this.actRoute.params.subscribe(
      (respuesta: any) => {
        this.id = respuesta.id;
        this.bbddProyectos.getProyectoById(this.id).subscribe(
          (datos: any) => {
            this.cargando = false;
            this.proyecto = datos;
            this.proyecto.usuarios.forEach((u: any) => {
              this.miembros.push(u);
            });

          }, (error) => {
            Swal.fire("ERROR", "Error al cargar el proyecto.", "error");
            this.route.navigate(['/inicio']);
          }
        )
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
    this.actualizar = actualizar;
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
    this.bbddProyectos.getProyectoById(this.id).subscribe(
      (datos: any) => {

        this.bbddProyectos.noCargar();
        let distinto = false;
        if (this.actualizar || (datos.nombre != this.proyecto.nombre || datos.listas.length != this.proyecto.listas.length || this.proyecto.usuarios.length != datos.usuarios.length)) {
          distinto = true;
        } else {
          for (let i = 0; i < datos.listas.length && !distinto; i++) {
            if (datos.listas[i].nombre != this.proyecto.listas[i].nombre ||
              datos.listas[i].id != this.proyecto.listas[i].id ||
              datos.listas[i].tareas.length != this.proyecto.listas[i].tareas.length) {
              distinto = true;
            }
            else {
              for (let j = 0; j < datos.listas[i].tareas.length && !distinto; j++) {
                if (datos.listas[i].tareas[j].id != this.proyecto.listas[i].tareas[j].id ||
                  datos.listas[i].tareas[j].nombre != this.proyecto.listas[i].tareas[j].nombre ||
                  datos.listas[i].tareas[j].dificultad != this.proyecto.listas[i].tareas[j].dificultad ||
                  datos.listas[i].tareas[j].prioridad != this.proyecto.listas[i].tareas[j].prioridad ||
                  datos.listas[i].tareas[j].descripcion != this.proyecto.listas[i].tareas[j].descripcion ||
                  datos.listas[i].tareas[j].usuarios.length != this.proyecto.listas[i].tareas[j].usuarios.length) {
                  distinto = true;
                }
              }
            }
          }
        }

        if(datos.mensajes.length != this.proyecto.mensajes.length){
          this.proyecto = datos;
          this.miembros = [];
          this.proyecto.usuarios.forEach((u: any) => {
            this.miembros.push(u);
          });
          this.bajarScroll();
        } else if (distinto) {
          this.proyecto = datos;
          this.miembros = [];
          this.proyecto.usuarios.forEach((u: any) => {
            this.miembros.push(u);
          });
        }
        this.actualizar = false;
      }, (error) => {
        Swal.fire("ERROR", "Error al cargar el proyecto.", "error");
        this.route.navigate(['/inicio']);
      }
    )


  }

  isCargando() {
    return this.bbddProyectos.isCargando()
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
    this.bbddProyectos.cargar()
    this.actualizar = true;
    this.bbddProyectos.addLista(this.proyecto.id, nombre).subscribe(
      (respuesta) => {
        this.cargarDatos();
      }, (error) => {
        Swal.fire("ERROR", "Error al crear la lista", "error");
      }
    )

  }

  getTareasListaMover(original: string, tarea: any, lista: any) {
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
    }
  }

  habilitarResto(tarea: string, lista: any) {
    if (tarea != '-1') {
      lista.disabled = false;
    }
    else {
      lista.value = '-1';
      lista.disabled = true;
    }
  }

  moverTarea(tareaId: string, listaId: string, original: string) {
    this.errores = false;
    let idLista = parseInt(listaId);
    let idTarea = parseInt(tareaId);
    this.bbddProyectos.cargar();
    this.actualizar = true;
    this.bbddProyectos.moverTarea(idTarea, idLista).subscribe(
      (respuesta) => {
        this.cargarDatos();
      }, (error) => {
        Swal.fire('ERROR', "Error al mover la tarea", 'error');
      }
    );
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

  crearTarea(modal: any, lista: string, nombre: string, descripcion: string, dificultad: string, prioridad: string) {
    this.errores = false;
    if (nombre == '') {
      Swal.fire("Atención", "Debes introducir un nombre para la tarea", "warning");
    } else {
      let dif: number = parseInt(dificultad);
      let prio: number = parseInt(prioridad);
      this.bbddProyectos.cargar();
      this.actualizar = true;
      this.bbddProyectos.addTarea(parseInt(lista), nombre, descripcion, parseInt(dificultad), parseInt(prioridad)).subscribe(
        (respuesta) => {
          this.cargarDatos();
        }, (error) => {
          this.bbddProyectos.noCargar();
          Swal.fire("ERROR", "Error al crear la tarea", "error");
        }
      )
    }
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
      if (this.filtro[i].prioridad == parseInt(prioridad)) {
        this.filtro[i].valor = checked.currentTarget.checked;
      }
    }
  }

  getIniciales(usuario: any) {
    let iniciales: string;
    iniciales = usuario.nombre[0] + usuario.apellido[0];
    iniciales = iniciales.toUpperCase();
    return iniciales;
  }

  buscarMiembro(email: any) {
    let expReg = /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/
    if (email.value == '') {
      Swal.fire('Aviso', 'No ha introducido ningún email en el buscador. Por favor, introduzca un email registrado en Worher para añadir al usuario al proyecto.', 'warning')
    } else if (!expReg.test(email.value)) {
      Swal.fire('ERROR', 'No ha introducido un email válido en el buscador. Por favor, introduzca un email registrado en Worher para añadir al usuario al proyecto.', 'error')
      email.value = '';
      email.focus();
    } else {
      let datos = this.bbddProyectos.buscarMiembro(email.value).subscribe(
        (m: any) => {
          let encontrado: boolean = false;
          for (let i = 0; i < this.miembros.length && !encontrado; i++) {
            if (this.miembros[i].id == m[0].id) encontrado = true;

          }
          if (!encontrado) this.miembros.push(m[0]);
          email.value = '';
        }, (error) => {
          Swal.fire('ERROR', 'El email introducido no está registrado en Worher', 'error');
          email.value = '';
          email.focus();
        }
      )

    }

  }

  quitarMiembro(miembro: any) {
    let encontrado: boolean = false;
    let indice: number = -1;
    for (let i = 0; i < this.proyecto.usuarios.length && !encontrado; i++) {
      if (this.proyecto.usuarios[i] == miembro) {
        encontrado = true;
        indice = i;
      }
    }

    if (indice != -1) this.proyecto.usuarios.splice(indice, 1);


  }

  addMiembro() {
    this.cargando = true;
    this.miembros.forEach((u: { id: number; }) => {
      this.bbddProyectos.addUsuarioProyecto(this.proyecto.id, u.id).subscribe(
        (respuesta) => {
          this.bbddProyectos.cargar()
          this.actualizar = true;
          this.cargarDatos()
        }, (error) => {
          Swal.fire('ERROR', "Error al añadir usuario al proyecto", 'error');
        }
      );
    });
  }

  hayProyecto() {
    return (this.proyecto) ? true : false;
  }

  crearErrores() {
    this.errores = true;
    Swal.fire("Atención", "Debe completar los campos obligatorios", "warning")
  }

  crearErroresListasIguales() {
    this.errores = true;
    Swal.fire("Atención", "Debe seleccionar una lista distinta a la original", "warning")
  }

  validarForm() {
  }

  bajarScroll() {
    var target = document.querySelector('#mensajes');
    if (target) {
      target.scrollTop = target.scrollHeight
    }
  }

  cancelAddMiembro(){
    this.miembros = [];
    this.proyecto.usuarios.forEach((u: any) => {
      this.miembros.push(u);
    });
  }



}
