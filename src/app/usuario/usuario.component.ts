import { Component, Input, OnInit } from '@angular/core';
import { BbddProyectosService } from '../bbdd-proyectos.service';

@Component({
  selector: 'app-usuario',
  templateUrl: './usuario.component.html',
  styleUrls: ['./usuario.component.css']
})
export class UsuarioComponent implements OnInit {
  @Input() usuario:any;

  constructor(private bbddProyectos:BbddProyectosService) { }

  ngOnInit(): void {
  }


  getIniciales(){
    let iniciales:string;
    iniciales=this.usuario.nombre[0]+this.usuario.apellido[0];
    iniciales = iniciales.toUpperCase();
    return iniciales;
  }

}
