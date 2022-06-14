import { Component, Input, OnInit } from '@angular/core';
import { BbddProyectosService } from '../bbdd-proyectos.service';

@Component({
  selector: 'app-proyecto-tarjeta',
  templateUrl: './proyecto-tarjeta.component.html',
  styleUrls: ['./proyecto-tarjeta.component.css']
})
export class ProyectoTarjetaComponent implements OnInit {
  @Input() p: any;
  editando: boolean = false;
  constructor(private bbddProyectos: BbddProyectosService) { }

  ngOnInit(): void {
  }



  editar() {
    this.editando = true;
  }

  eliminar() {
    this.bbddProyectos.deleteProyecto(this.p.id);

  }

  actualizarNombre(nombre: string) {
    if (nombre == "") nombre = "(Sin nombre)";
    this.bbddProyectos.renombrarProyecto(this.p.id, nombre);
  }

  noActualizar() {
    this.editando = false;
  }

  get3Miembros() {
    let miembros: any[] = [];
    for (let i = 0; i < this.p.usuarios.length && i < 3; i++) {
      miembros.push(this.p.usuarios[i]);
    };

    return miembros;
  }

  getIniciales(usuario: any) {
    let iniciales: string;
    iniciales = usuario.nombre[0] + usuario.apellido[0];
    iniciales = iniciales.toUpperCase();
    return iniciales;
  }



}
