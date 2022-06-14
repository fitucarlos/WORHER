import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import swal from 'sweetalert2';
import { BbddProyectosService } from '../bbdd-proyectos.service';

@Component({
  selector: 'app-mis-proyectos',
  templateUrl: './mis-proyectos.component.html',
  styleUrls: ['./mis-proyectos.component.css']
})
export class MisProyectosComponent implements OnInit {
  modoCrear: boolean = false;
  miembros: any[] = [];
  cargando = false;

  constructor(private bbddProyectos: BbddProyectosService, private route: Router) {

  }

  ngOnInit(): void {
    if (!sessionStorage.getItem('id')) {
      this.route.navigate(['/login']);
    }
  }

  getProyectos() { return this.bbddProyectos.getProyectos() }

  isCargando() { return this.bbddProyectos.isCargando() }

  cambiarModo() {
    this.modoCrear = true;
    this.miembros = [];
  }

  isModoCrear() { return this.modoCrear; }

  addProyecto(nombre: string) {
    if (nombre.trim() == '') nombre = '(Proyecto sin nombre)';
    this.bbddProyectos.addProyecto(nombre, this.miembros);
  }

  buscarMiembro(email: any) {
    let expReg = /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/
    if (email.value == '') {
      swal.fire('Aviso', 'No ha introducido ningún email en el buscador. Por favor, introduzca un email registrado en Worher para añadir al usuario al proyecto.', 'warning')
    } else if (!expReg.test(email.value)) {
      swal.fire('ERROR', 'No ha introducido un email válido en el buscador. Por favor, introduzca un email registrado en Worher para añadir al usuario al proyecto.', 'error')
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
          swal.fire('ERROR', 'El email introducido no está registrado en Worher', 'error');
          email.value = '';
          email.focus();
        }
      )

    }

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



}
