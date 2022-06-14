import { CdkDragDrop } from '@angular/cdk/drag-drop';
import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import Swal from 'sweetalert2';
import { BbddProyectosService } from '../bbdd-proyectos.service';


@Component({
  selector: 'app-lista',
  templateUrl: './lista.component.html',
  styleUrls: ['./lista.component.css']
})
export class ListaComponent implements OnInit {
  @Input() lista: any;
  @Input() proyecto: any;
  @Input() filtro: any;
  @Output() actualizar = new EventEmitter<boolean>();
  menu: boolean = false;
  editar: boolean = false;
  miembros: any[] = [];


  constructor(private bbddProyectos: BbddProyectosService) { }

  ngOnInit(): void {
  }

  renombrarLista() {
    this.editar = true;
    this.menu = false;
  }

  actualizarNombre(nombre: string) {
    if (nombre == "") nombre = "(Sin nombre)";
    this.bbddProyectos.renombrarLista(this.lista.id, nombre);
    this.lista.nombre = nombre;
    this.editar = false;
  }

  noActualizar() {
    this.editar = false;
  }

  borrarLista() {
    this.bbddProyectos.deleteLista(this.lista.id);
    let indice: number = -1;
    for (let i = 0; i < this.proyecto.listas.length; i++) {
      if (this.lista.id == this.proyecto.listas[i].id) {
        indice = i;
        break;
      }
    }

    if (indice != -1) {
      this.proyecto.listas.splice(indice, 1);
    }


  }

  buscarMiembro(email: string) {
    this.bbddProyectos.buscarMiembroProyecto(this.proyecto.id, email).subscribe(
      (m: any) => {
        let encontrado: boolean = false;
        for (let i = 0; i < this.miembros.length && !encontrado; i++) {
          if (this.miembros[i].id == m[0].id) encontrado = true;

        }
        if (!encontrado) this.miembros.push(m[0]);
        else Swal.fire('Información', 'El usuario ya tiene asignada la tarea', 'info');
      }
    )
  }

  quitarMiembro(miembro: any) {
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


  actualizarTarea(actualizar: boolean) {
    if (actualizar) {
      this.actualizar.emit(true);
    }
  }









}
