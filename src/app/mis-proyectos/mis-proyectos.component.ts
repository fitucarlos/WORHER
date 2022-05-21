import { Component, OnInit } from '@angular/core';
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

  constructor(private bbddProyectos: BbddProyectosService) { 

  }

  ngOnInit(): void { }

  getProyectos() { return this.bbddProyectos.getProyectos() }

  isCargando() { return this.bbddProyectos.isCargando()}

  cambiarModo() {
    this.modoCrear = true;
    this.miembros = [];
  }

  isModoCrear() { return this.modoCrear; }

  addProyecto(nombre: string) {
    this.bbddProyectos.addProyecto(nombre, this.miembros);
  }

  buscarMiembro(email: string) {
    let datos = this.bbddProyectos.buscarMiembro(email).subscribe(
      (m: any) => {
        let encontrado: boolean = false;
        for (let i = 0; i < this.miembros.length && !encontrado; i++) {
          if (this.miembros[i].id == m[0].id) encontrado = true;

        }
        if (!encontrado) this.miembros.push(m[0]);
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



}
