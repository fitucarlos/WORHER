import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { BbddProyectosService } from '../bbdd-proyectos.service';
import { AuthService } from '../servicios/auth.service';
import { TokenStorageService } from '../servicios/token-storage.service';
import * as CryptoJS from 'crypto-js';
@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  form: any = {
    email: null,
    password: null
  };

  isLoggedIn = false;
  isLoginFailed = false;
  errorMessage = '';
  private id:number=0;

  constructor(private authService: AuthService, private tokenStorage: TokenStorageService, private route:Router, private service:BbddProyectosService) { }

  ngOnInit(): void {
    if(this.tokenStorage.getToken()){
      this.isLoggedIn = true;
    }
  }

  onSubmit(): void {
    const {email, password} = this.form;
    this.authService.login(email, password).subscribe({
      next: data => {
        this.tokenStorage.saveToken(data.accessToken);
        this.isLoginFailed = false;
        this.isLoggedIn = true;
        let enc = CryptoJS.AES.encrypt((data.id).toString(), 'id').toString();
        sessionStorage.setItem('id', enc);
        this.service.setUsuarioId(data.id)
        this.service.cargarDatos();
        this.route.navigate(['/inicio']);
      },

      error: err => {
        this.errorMessage = err.error.message;
        this.isLoginFailed = true;
      }

    });
  }

  reloadPage(): void {
    window.location.reload();
  }

  getId(){
    return this.id;
  }

}
