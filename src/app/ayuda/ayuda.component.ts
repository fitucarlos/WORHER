import { Component, OnInit } from '@angular/core';
import { TokenStorageService } from '../servicios/token-storage.service';

@Component({
  selector: 'app-ayuda',
  templateUrl: './ayuda.component.html',
  styleUrls: ['./ayuda.component.css']
})
export class AyudaComponent implements OnInit {
  isLoggedIn = false;
  valor:string = '0';

  constructor(private tokenStorageService: TokenStorageService) { }

  ngOnInit(): void {
    this.isLoggedIn = !!this.tokenStorageService.getToken();
  }

  mostrarAyuda(ayuda:string){
    this.valor = ayuda;
  }

  subir(){
    window.scroll(0,0);
  }

}
