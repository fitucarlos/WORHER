import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
const AUTH_API = 'http://worher-env.eba-ip3j5hpk.us-east-1.elasticbeanstalk.com/api/auth/';
const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/json' })
};

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  constructor(private http: HttpClient) { }

  login(email: string, password: string): Observable<any> {
    return this.http.post(AUTH_API + 'signin', { email, password }, httpOptions)
  }

  register(nombre: string, apellido: string, email: string, password: string): Observable<any> {
    return this.http.post(AUTH_API + 'signup', { nombre, apellido, email, password }, httpOptions);
  }
}
